<?php

namespace App\Repository\rapports;

use App\Entity\rapports\TypeCalendriers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TypeCalendriersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCalendriers::class);
    }

    /**
     * Récupérer tous les types non supprimés
     */
    public function findAllActive(string $order = 'ASC'): array
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        return $this->createQueryBuilder('t')
            ->andWhere('t.deletedAt IS NULL')
            ->orderBy('t.createdAt', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver par nom
     */
    public function findOneByName(string $name): ?TypeCalendriers
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
    public function findActiveById(int $id): ?TypeCalendriers
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}