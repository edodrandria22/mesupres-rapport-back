<?php

namespace App\Dto\rapports;

use Symfony\Component\Validator\Constraints as Assert;

class ActiviteCollectionDto
{   
    #[Assert\NotNull(message: "Le calendrier est obligatoire.")]
    #[Assert\Type(
        type: 'integer',
        message: "L'identifiant du calendrier doit être un nombre entier."
    )]
    #[Assert\Positive(message: "L'identifiant du calendrier doit être un nombre positif.")]
    public ?int $idCalendrier = null;
    
    
    /** @var ActiviteDto[] */
    #[Assert\NotNull]
    public array $activites = [];

    public function getIdCalendrier(): ?int
    {
        return $this->idCalendrier;
    }

    public function setIdCalendrier(?int $idCalendrier): self
    {
        $this->idCalendrier = $idCalendrier;
        return $this;
    }

    
    /**
     * @return ActiviteDto[]
     */
    public function getActivites(): array
    {
        return $this->activites;
    }

    /**
     * @param ActiviteDto[] $activites
     */
    public function setActivites(array $activites): self
    {
        $this->activites = $activites;
        return $this;
    }

    /**
     * Ajouter une activite
     */
    public function addActivite(ActiviteDto $activite): self
    {
        $this->activites[] = $activite;
        return $this;
    }
}