<?php

namespace App\Dto\rapports;

use Symfony\Component\Validator\Constraints as Assert;

class MailRappelDTO
{
    #[Assert\NotBlank(message: 'Le destinataire est requis')]
    #[Assert\Email(message: 'L\'email du destinataire n\'est pas valide')]
    public ?string $destinataire = null;
    
    #[Assert\NotBlank(message: 'Le type de rapport est requis')]
    public ?string $typeRapport = null;
    
    #[Assert\NotBlank(message: 'La date limite est requise')]
    public ?string $dateLimite = null;

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(?string $destinataire): self
    {
        $this->destinataire = $destinataire;
        return $this;
    }

    public function getTypeRapport(): ?string
    {
        return $this->typeRapport;
    }

    public function setTypeRapport(?string $typeRapport): self
    {
        $this->typeRapport = $typeRapport;
        return $this;
    }

    public function getDateLimite(): ?string
    {
        return $this->dateLimite;
    }

    public function setDateLimite(?string $dateLimite): self
    {
        $this->dateLimite = $dateLimite;
        return $this;
    }
}
