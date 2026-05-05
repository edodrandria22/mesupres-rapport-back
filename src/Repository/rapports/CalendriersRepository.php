<?php

namespace App\Repository\rapports;

use App\Entity\rapports\Calendriers;
use App\Entity\rapports\TypeCalendriers;
use App\Repository\utils\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\utils\OrderCriteria;
class CalendriersRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendriers::class);
    }

    /**
     * Trouver tous les calendriers non supprimés
     */
public function findAllActive(OrderCriteria $criteria = new OrderCriteria()): array
{
    return $this->createQueryBuilder('c')
        ->andWhere('c.deletedAt IS NULL')
        ->orderBy('c.' . $criteria->getField(), $criteria->getDirection())
        ->getQuery()
        ->getResult();
}

    /**
     * Trouver par type de calendrier
     */
    public function findByType(TypeCalendriers $type, OrderCriteria $criteria = new OrderCriteria()): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.typeCalendriers = :type')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('type', $type)
            ->orderBy('c.' . $criteria->getField(), $criteria->getDirection())
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les calendriers entre deux dates
     */
    public function findBetweenDates(
        \DateTimeInterface $debut,
        \DateTimeInterface $fin,
        OrderCriteria $criteria = new OrderCriteria()
    ): array {
        return $this->createQueryBuilder('c')
            ->join('c.typeCalendriers', 't')
            ->addSelect('t')
            ->andWhere('c.dateDebut >= :debut')
            ->andWhere('c.dateFin <= :fin')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('c.' . $criteria->getField(), $criteria->getDirection())
            ->getQuery()
            ->getResult();
    }
    public function findDate(
    \DateTimeInterface $date,
    OrderCriteria $criteria = new OrderCriteria(),
    int $heure = 0
    ): array {

        // Cloner la date pour éviter de modifier l’original
        $adjustedDate = (new \DateTime($date->format('Y-m-d H:i:s')));

        // Ajustement des heures
        if ($heure !== 0) {
            if ($heure > 0) {
                $adjustedDate->modify("+{$heure} hours");
            } else {
                $adjustedDate->modify("{$heure} hours"); // ex: -2 hours
            }
        }

        $qb = $this->createQueryBuilder('c')
            ->join('c.typeCalendriers', 't')
            ->addSelect('t')
            ->andWhere('c.dateDebut <= :date')
            ->andWhere('c.dateFin >= :adjustedDate')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('date', $date)
            ->setParameter('adjustedDate', $adjustedDate)
            ->orderBy('c.' . $criteria->getField(), $criteria->getDirection());

        return $qb->getQuery()->getResult();
    }
    /**
     * Trouver un calendrier actif par ID
     */
    public function findActiveById(int $id): ?Calendriers
    {
        return $this->createQueryBuilder('c')
            ->join('c.typeCalendriers', 't')
            ->addSelect('t')
            ->andWhere('c.id = :id')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findBetweenDatesDebut(
        \DateTimeInterface $debut,
        \DateTimeInterface $fin,
        OrderCriteria $criteria = new OrderCriteria()
    ): array {
        return $this->createQueryBuilder('c')
            ->join('c.typeCalendriers', 't')
            ->addSelect('t')
            ->andWhere('c.dateDebut >= :debut')
            ->andWhere('c.dateDebut <= :fin')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('c.' . $criteria->getField(), $criteria->getDirection())
            ->getQuery()
            ->getResult();
    }
    
}