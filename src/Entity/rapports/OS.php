<?php
namespace App\Entity\rapports;

use App\Entity\utils\BaseUtilisateurNom;
use App\Repository\rapports\OSRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OSRepository::class)]
class OS extends BaseUtilisateurNom
{

}