<?php
namespace App\Entity\rapports;


use App\Entity\utils\BaseUtilisateurNom;
use App\Repository\rapports\LIRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LIRepository::class)]
class LI extends BaseUtilisateurNom
{

}