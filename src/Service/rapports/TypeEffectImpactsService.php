<?php

namespace App\Service\rapports;

use App\Dto\utils\OrderCriteria;
use App\Entity\rapports\TypeEffectImpacts;
use App\Repository\rapports\TypeEffectImpactsRepository;
use Doctrine\ORM\EntityManagerInterface;

class TypeEffectImpactsService
{
    private TypeEffectImpactsRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(
        TypeEffectImpactsRepository $repository,
        EntityManagerInterface $em
    ) {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function getAllActive(OrderCriteria $criteria): array
    {
        return $this->repository->findAllActive($criteria);
    }

    public function getById(int $id): ?TypeEffectImpacts
    {
        return $this->repository->findActiveById($id);
    }

    public function getByName(string $name): ?TypeEffectImpacts
    {
        return $this->repository->findOneByName($name);
    }

}