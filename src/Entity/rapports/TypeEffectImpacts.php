<?php
namespace App\Entity\rapports;

use App\Entity\utils\BaseNom;
use App\Repository\rapports\TypeEffectImpactsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeEffectImpactsRepository::class)]
class TypeEffectImpacts extends BaseNom
{
    // public function toArray(array $exclude = []): array
    // {
    //     $result = parent::toArray($exclude);
    //     return $result;
    // }
    
}