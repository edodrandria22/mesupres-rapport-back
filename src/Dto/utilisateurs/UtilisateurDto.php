<?php

namespace App\Dto\utilisateurs;

use Symfony\Component\Validator\Constraints as Assert;

class UtilisateurDto
{
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Format d'email invalide.")]
    public ?string $email = null;

    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(
        min: 6,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères."
    )]
    public ?string $mdp = null;

    #[Assert\NotBlank(message: "L'entité est obligatoire.")]
    public ?string $entite = null;

    #[Assert\NotNull(message: "Le rôle est obligatoire.")]
    #[Assert\Type(
        type: 'integer',
        message: "L'identifiant du rôle doit être un entier."
    )]
    #[Assert\Positive(message: "L'identifiant du rôle doit être positif.")]
    public ?int $idRole = null;

    #[Assert\Positive(message: "Le rang doit être positif.")]
    public ?int $rang = null;

    public ?string $sigle = null;

    #[Assert\Email(message: "Format d'email invalide.")]
    public ?string $emailCopie = null;

    // ===== GETTERS =====

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function getEntite(): ?string
    {
        return $this->entite;
    }

    public function getIdRole(): ?int
    {
        return $this->idRole;
    }

    public function getRang(): ?int
    {
        return $this->rang;
    }
    
    public function getSigle(): ?string
    {
        return $this->sigle;
    }
    
    public function getEmailCopie(): ?string
    {
        return $this->emailCopie;
    }
}