<?php

namespace App\Entity\utilisateurs;

use App\Entity\utils\BaseNom;
use App\Repository\utilisateurs\RolesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
class Roles extends BaseNom
{
    
}
