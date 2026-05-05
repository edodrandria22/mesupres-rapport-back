<?php

namespace App\Dto\rapports;

use App\Entity\rapports\Activites;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Constraints\Count;

class ActiviteDto
{
    #[Assert\NotBlank(message: "L'activite est obligatoire.")]
    public ?EffectImpactDto $activite ;

    /** @var EffectImpactDto[] */
    #[Assert\NotNull(message: "La liste des activités ne peut pas être nulle.")]
    #[Count(min: 1, minMessage: "Vous devez fournir au moins une activité.")]

    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $effects = [];
    
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $impacts = [];
    
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $produits = [];
    
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $cibles = [];
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $previsions = [];
    
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $realisations = [];
    
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $taux = [];
    
    /** @var EffectImpactDto[] */
    #[Assert\NotNull]
    public array $observations = [];

    

    public function getActivite(): ?EffectImpactDto
    {
        return $this->activite;
    }

    public function setActivite(?EffectImpactDto $activite): self
    {
        $this->activite = $activite;
        return $this;
    }

    /**
     * @return EffectImpactDto[]
     */
    public function getEffects(): array
    {
        return $this->effects;
    }
    
    public function getImpacts(): array
    {
        return $this->impacts;
    }
    
    public function getProduits(): array
    {
        return $this->produits;
    }
    
    public function getCibles(): array
    {
        return $this->cibles;
    }
    
    public function getPrevisions(): array
    {
        return $this->previsions;
    }
    
    public function getRealisations(): array
    {
        return $this->realisations;
    }
    
    public function getTaux(): array
    {
        return $this->taux;
    }
    
    public function getObservations(): array
    {
        return $this->observations;
    }

    /**
     * @param EffectImpactDto[] $effects
     */
    public function setEffects(array $effects): self
    {
        $this->effects = $effects;
        return $this;
    }
    public function setImpacts(array $impacts): self
    {
        $this->impacts = $impacts;
        return $this;
    }
    public function setProduits(array $produits): self
    {
        $this->produits = $produits;
        return $this;
    }
    public function setCibles(array $cibles): self
    {
        $this->cibles = $cibles;
        return $this;
    }
    public function setPrevisions(array $previsions): self
    {
        $this->previsions = $previsions;
        return $this;
    }
    public function setRealisations(array $realisations): self
    {
        $this->realisations = $realisations;
        return $this;
    }
    public function setTaux(array $taux): self
    {
        $this->taux = $taux;
        return $this;
    }
    public function setObservations(array $observations): self
    {
        $this->observations = $observations;
        return $this;
    }

    /**
     * Ajouter un effet
     */
    public function addEffect(EffectImpactDto $effectImpactDto): self
    {
        $this->effects[] = $effectImpactDto;
        return $this;
    }
    public function addImpact(EffectImpactDto $effectImpactDto): self
    {
        $this->impacts[] = $effectImpactDto;
        return $this;
    }
    public function addProduit(EffectImpactDto $effectImpactDto): self
    {
        $this->produits[] = $effectImpactDto;
        return $this;
    }
    public function addCible(EffectImpactDto $effectImpactDto): self
    {
        $this->cibles[] = $effectImpactDto;
        return $this;
    }
    public function addPrevision(EffectImpactDto $effectImpactDto): self
    {
        $this->previsions[] = $effectImpactDto;
        return $this;
    }
    public function addRealisation(EffectImpactDto $effectImpactDto): self
    {
        $this->realisations[] = $effectImpactDto;
        return $this;
    }
    public function addTaux(EffectImpactDto $effectImpactDto): self
    {
        $this->taux[] = $effectImpactDto;
        return $this;
    }
    public function addObservation(EffectImpactDto $effectImpactDto): self
    {
        $this->observations[] = $effectImpactDto;
        return $this;
    }
    public function getActiviteClass(): Activites
    {
        $result = new Activites();
        $result->setName($this->activite->getName());
        return $result;
    }
}