<?php

namespace App\Dto\rapports;

use App\Entity\rapports\EffectsImpacts;

class EffectImpactDto
{
    public ?string $name = null;
    
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEffectsImpactsClass(): EffectsImpacts
    {
        $result = new EffectsImpacts();
        $result->setName($this->name);
        return $result;
    }
}
