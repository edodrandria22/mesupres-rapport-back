<?php
namespace App\Entity\rapports;

use App\Entity\utils\BaseNom;
use App\Repository\rapports\TypeCalendriersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeCalendriersRepository::class)]
class TypeCalendriers extends BaseNom
{
    public function toArray(array $exclude = []): array
    {
        $result = parent::toArray($exclude);
        $result['name'] = $this->getName();
        return $result;
    }

}