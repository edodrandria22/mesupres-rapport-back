<?php

namespace App\Service\rapports;

use App\Repository\rapports\LIRepository;
use App\Service\utils\BaseUtilisateurNomService;
use Doctrine\ORM\EntityManagerInterface;

class LIService extends BaseUtilisateurNomService
{
    private LIRepository $repository;

    public function __construct(LIRepository $repository, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->repository = $repository;
    }

    protected function getRepository(): LIRepository
    {
        return $this->repository;
    }
}