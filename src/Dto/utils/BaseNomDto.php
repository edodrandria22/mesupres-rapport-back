<?php

namespace App\Dto\utils;

use Symfony\Component\Validator\Constraints as Assert;

class BaseNomDto
{
    
    #[Assert\NotBlank(message: 'Le name est obligatoire.')]
    public ?string $name = null;
}