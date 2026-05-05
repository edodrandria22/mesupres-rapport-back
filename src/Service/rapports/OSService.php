<?php

namespace App\Service\rapports;

use App\Repository\rapports\OSRepository;
use App\Service\utils\BaseUtilisateurNomService;
use Doctrine\ORM\EntityManagerInterface;

class OSService extends BaseUtilisateurNomService
{
    private OSRepository $repository;

    public function __construct(OSRepository $repository, EntityManagerInterface $em)
    {
        parent::__construct($em);
        $this->repository = $repository;
    }

    protected function getRepository(): OSRepository
    {
        return $this->repository;
    }
}