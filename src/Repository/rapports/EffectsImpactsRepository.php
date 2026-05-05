<?php

namespace App\Repository\rapports;

use App\Entity\rapports\EffectsImpacts;
use App\Entity\rapports\Activites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\utils\OrderCriteria;
use App\Entity\rapports\TypeEffectImpacts;

class EffectsImpactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EffectsImpacts::class);
    }

    public function findByActivite(
        Activites $activite,
        OrderCriteria $criteria
    ): array {
        
        return $this->createQueryBuilder('e')
            ->andWhere('e.activite = :activite')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('activite', $activite)
            ->orderBy('e.'.$criteria->getField(), $criteria->getDirection())
            ->getQuery()
            ->getResult();
    }
    public function findByActiviteType(Activites $activite,TypeEffectImpacts $typeEffectImpact,OrderCriteria $criteria): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.activite = :activite')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('activite', $activite)
            ->andWhere('e.typeEffectImpact = :typeEffectImpact')
            ->setParameter('typeEffectImpact', $typeEffectImpact)
            ->orderBy('e.'.$criteria->getField(), $criteria->getDirection())
            ->getQuery()
            ->getResult();
    }
}