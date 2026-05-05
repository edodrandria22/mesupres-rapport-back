<?php

namespace App\Repository\rapports;

use App\Entity\rapports\TypeEffectImpacts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\utils\OrderCriteria;
class TypeEffectImpactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEffectImpacts::class);
    }

    /**
     * Récupérer tous les types non supprimés
     */
    public function findAllActive(OrderCriteria $orderCriteria): array
    {
      
        return $this->createQueryBuilder('t')
            ->andWhere('t.deletedAt IS NULL')
            ->orderBy('t.'.$orderCriteria->getField(), $orderCriteria->getDirection())
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver par nom
     */
    public function findOneByName(string $name): ?TypeEffectImpacts
    {
        return $this->createQueryBuilder('t')
            ->andWhere('LOWER(t.name) = LOWER(:name)')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouver un type actif par ID
     */
    public function findActiveById(int $id): ?TypeEffectImpacts
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}