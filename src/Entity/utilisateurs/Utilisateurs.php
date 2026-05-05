<?php

namespace App\Entity\utilisateurs;

use App\Entity\utils\BaseValidation;
use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
class Utilisateurs extends BaseValidation
{
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    #[ORM\Column(length: 255)]
    private ?string $entite = null;


    #[ORM\ManyToOne(targetEntity: Roles::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Roles $role = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $rang = null;

    
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $sigle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailCopie = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function getEntite(): ?string
    {
        return $this->entite;
    }

    public function setEntite(string $entite): static
    {
        $this->entite = $entite;
        return $this;
    }


    public function getRole(): ?Roles
    {
        return $this->role;
    }

    public function setRole(?Roles $role): static
    {
        $this->role = $role;
        return $this;
    }
    public function getRang(): ?int
    {
        return $this->rang;
    }

    public function setRang(?int $rang): static
    {
        $this->rang = $rang;
        return $this;
    }
    public function getEmailCopie(): ?string
    {
        return $this->emailCopie;
    }
    
    public function setEmailCopie(?string $emailCopie): static
    {
        $this->emailCopie = $emailCopie;
        return $this;
    }

    public function toArray(array $exclude = [], bool $includeIdRole = false): array
    {
        $data = parent::toArray($exclude);

        $data['role'] = $this->getRole() ? $this->getRole()->getName() : null;

        if ($includeIdRole) {
            $data['idRole'] = $this->getRole() ? $this->getRole()->getId() : null;
        }

        return $data;
    }
    public function getSigle(): ?string
    {
        return $this->sigle;
    }
    
    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;
        return $this;
    }
}
