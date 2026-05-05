<?php

namespace App\Entity\rapports;
use App\Entity\rapports\Calendriers;
use App\Entity\utilisateurs\Utilisateurs;
use App\Entity\utils\BaseValidation;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: CalendriersUtilisateursRepository::class)]
class CalendriersUtilisateurs extends BaseValidation
{
    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $utilisateur = null;
    
    #[ORM\ManyToOne(targetEntity: Calendriers::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calendriers $calendrier = null;

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateur;
    }
    public function setUtilisateur(?Utilisateurs $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
    public function getCalendrier(): ?Calendriers
    {
        return $this->calendrier;
    }
    public function setCalendrier(?Calendriers $calendrier): self
    {
        $this->calendrier = $calendrier;
        return $this;
    }
    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);
        $excludeUtilisateur = array_merge($exclude, ['mdp']);
        $data['user'] = $this->getUtilisateur()->toArray($excludeUtilisateur);
        $data['calendrier'] = $this->getCalendrier()->toArray($exclude);
        $statut = "EN COURS";
        if ($this->getDateValidation() !== null) {
            $statut = "VALIDE";
        }
        $data['statut'] = $statut;
        return $data;
    }
    
    
    
}