<?php

namespace App\Controller;

use App\Entity\Wine;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class WineController extends AbstractController
{
    #[Route('/api/wines', name: 'app_api_wines', methods: ['GET'])]
    public function getWines(EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        if (!$session->has('user_id')) {
            return new JsonResponse(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        $userId = $session->get('user_id');
        $wines = $entityManager->getRepository(Wine::class)->findBy(['userId' => $userId]);
        
        $wineData = [];
        foreach ($wines as $wine) {
            $wineData[] = [
                'id' => $wine->getId(),
                'name' => $wine->getName(),
                'year' => $wine->getYear(),
                'country' => $wine->getCountry(),
                'description' => $wine->getDescription(),
                'price' => $wine->getPrice(),
                'image' => $wine->getImage() ?? null
            ];
        }

        return new JsonResponse([
            'success' => true,
            'wines' => $wineData,
            'count' => count($wineData)
        ]);
    }

    #[Route('/add-wine', name: 'app_add_wine', methods: ['GET', 'POST'])]
    public function addWine(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if (!$session->has('user_id')) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $wine = new Wine();
            $wine->setName($request->request->get('name'));
            $wine->setYear((int) $request->request->get('year'));
            $wine->setCountry($request->request->get('country'));
            $wine->setDescription($request->request->get('description'));
            $wine->setPrice((float) $request->request->get('price'));
            $wine->setUserId($session->get('user_id'));

            $entityManager->persist($wine);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('wine/add.html.twig');
    }

    #[Route('/api/wines/{id}', name: 'app_api_wine_delete', methods: ['DELETE'])]
    public function deleteWine(int $id, EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        if (!$session->has('user_id')) {
            return new JsonResponse(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        $wine = $entityManager->getRepository(Wine::class)->find($id);
        
        if (!$wine || $wine->getUserId() !== $session->get('user_id')) {
            return new JsonResponse(['success' => false, 'message' => 'Vin non trouvé'], 404);
        }

        $entityManager->remove($wine);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
} 