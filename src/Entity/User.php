<?php

namespace App\Entity;


use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $no = null;

    #[ORM\Column(length: 255)]
    private ?string $username1 = null;

    #[ORM\Column(length: 255)]
    private ?string $email1 = null;

    #[ORM\Column(length: 255)]
    private ?string $password1 = null;

    #[ORM\Column(length: 255)]
    private ?string $roles = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getNo(): ?string
    {
        return $this->no;
    }

    public function setNo(string $no): static
    {
        $this->no = $no;
        return $this;
    }

    public function getUsername1(): ?string
    {
        return $this->username1;
    }

    public function setUsername1(string $username1): static
    {
        $this->username1 = $username1;
        return $this;
    }

    public function getEmail1(): ?string
    {
        return $this->email1;
    }

    public function setEmail1(string $email1): static
    {
        $this->email1 = $email1;
        return $this;
    }

    public function getPassword1(): ?string
    {
        return $this->password1;
    }

    public function setPassword1(string $password1): static
    {
        $this->password1 = $password1;
        return $this;
    }

    // Méthodes pour Symfony Security

    public function getUserIdentifier(): string
    {
        // Utilise le champ qui sert d'identifiant (ici username)
        return (string) $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password1;
    }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->roles) {
            // Si roles est une chaîne, transforme-la en tableau
            $roles = explode(',', $this->roles);
        }
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(string $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, efface-les ici
    }
}