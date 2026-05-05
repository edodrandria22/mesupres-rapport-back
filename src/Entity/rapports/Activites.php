<?php

namespace App\Entity\rapports;

use App\Entity\rapports\CalendriersUtilisateurs;
use App\Entity\utils\BaseNom;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivitesRepository::class)]
class Activites extends BaseNom
{
    #[ORM\ManyToOne(targetEntity: CalendriersUtilisateurs::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CalendriersUtilisateurs $calendrierUtilisateur = null;
    public function getCalendrierUtilisateur(): ?CalendriersUtilisateurs
    {
        return $this->calendrierUtilisateur;
    }

    public function setCalendrierUtilisateur(?CalendriersUtilisateurs $calendrierUtilisateur): self
    {
        $this->calendrierUtilisateur = $calendrierUtilisateur;
        return $this;
    }
    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);
        // $data['calendrierUtilisateur'] = $this->getCalendrierUtilisateur()->toArray();
        return $data;
    }

}
