<?php

namespace App\Entity\rapports;
use App\Entity\utils\BaseEntite;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\rapports\TypeCalendriers;

#[ORM\Entity(repositoryClass: CalendriersRepository::class)]
class Calendriers extends BaseEntite
{
    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(targetEntity: TypeCalendriers::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCalendriers $typeCalendriers = null;
    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }
    public function setTypeCalendriers(?TypeCalendriers $typeCalendriers): self
    {
        $this->typeCalendriers = $typeCalendriers;
        return $this;
    }
    public function getTypeCalendriers(): ?TypeCalendriers
    {
        return $this->typeCalendriers;
    }
    public function toArray(array $exclude = []): array
    {
        $data = parent::toArray($exclude);
        // $data['dateDebut'] = $this->getDateDebut()->format('Y-m-d');
        // $data['dateFin'] = $this->getDateFin()->format('Y-m-d');
        $data['typeCalendrier'] = $this->getTypeCalendriers()->toArray($exclude);
        return $data;
    }
    
    

    
}