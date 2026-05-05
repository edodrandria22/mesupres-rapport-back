<?php

namespace App\Dto\rapports;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\rapports\Calendriers;
class CalendrierDto
{
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateDebut = null;

    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $dateFin = null;

    #[Assert\NotNull(message: "Le type de calendrier est obligatoire.")]
    #[Assert\Type('integer', message: "L'identifiant du type de calendrier doit être un entier.")]
    #[Assert\Positive(message: "L'identifiant du type de calendrier doit être positif.")]
    public ?int $typeCalendrierId = null;

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

    public function getTypeCalendrierId(): ?int
    {
        return $this->typeCalendrierId;
    }

    public function setTypeCalendrierId(?int $typeCalendrierId): self
    {
        $this->typeCalendrierId = $typeCalendrierId;
        return $this;
    }

    public function toEntity(): Calendriers
    {
        $calendrier = new Calendriers();
        if ($this->dateDebut >= $this->dateFin) {
            throw new \InvalidArgumentException("La date de début doit être antérieure ou egal à la date de fin.");
        }
        $calendrier->setDateDebut($this->dateDebut);
        $calendrier->setDateFin($this->dateFin);
        return $calendrier;
    }
}