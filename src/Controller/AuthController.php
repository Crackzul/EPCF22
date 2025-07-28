<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $error = null;
        
        // Si déjà connecté, rediriger vers le dashboard
        if ($session->has('user_id')) {
            return $this->redirectToRoute('app_dashboard');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            // Rechercher l'utilisateur par email
            $user = $entityManager->getRepository(User::class)->findOneBy(['email1' => $email]);

            if ($user && $user->getPassword1() === $password) {
                // Créer la session utilisateur
                $session->set('user_id', $user->getId());
                $session->set('user_name', $user->getUsername());
                $session->set('user_email', $user->getEmail1());
                $session->set('user_role', $user->getRoles()[0] ?? 'ROLE_USER');

                return $this->redirectToRoute('app_dashboard');
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        }

        return $this->render('auth/login.html.twig', [
            'error' => $error
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(SessionInterface $session): Response
    {
        if (!$session->has('user_id')) {
            return $this->redirectToRoute('app_login');
        }

        $user = [
            'id' => $session->get('user_id'),
            'name' => $session->get('user_name'),
            'email' => $session->get('user_email'),
            'role' => $session->get('user_role')
        ];

        return $this->render('auth/dashboard.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('app_login');
    }
} 