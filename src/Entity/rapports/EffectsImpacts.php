<?php

namespace App\Entity\rapports;

use App\Entity\utils\BaseNom;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EffectsImpactsRepository::class)]
class EffectsImpacts extends BaseNom
{
    #[ORM\ManyToOne(targetEntity: Activites::class)]
    private ?Activites $activite = null;

    #[ORM\ManyToOne(targetEntity: TypeEffectImpacts::class)]
    private ?TypeEffectImpacts $typeEffectImpact = null;

    public function getActivite(): ?Activites
    {
        return $this->activite;
    }

    public function setActivite(?Activites $activite): self
    {
        $this->activite = $activite;
        return $this;
    }
    public function setTypeEffectImpact(?TypeEffectImpacts $typeEffectImpact): self
    {
        $this->typeEffectImpact = $typeEffectImpact;
        return $this;
    }
    public function getTypeEffectImpact(): ?TypeEffectImpacts
    {
        return $this->typeEffectImpact;
    }
    // public function toArray(array $exclude = []): array
    // {
    //     $data = parent::toArray($exclude);
    //     // $data['activite'] = $this->getActivite() ? $this->getActivite()->toArray() : [];        
    //     return $data;
    // }
}