<?php

namespace App\Repository\rapports;

use App\Entity\rapports\Activites;
use App\Entity\rapports\CalendriersUtilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivitesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activites::class);
    }

    /**
     * Trouver les semaines d’un utilisateur
     */
    public function findByCalendrierUtilisateur(CalendriersUtilisateurs $calendrierUtilisateur, string $order = 'DESC'): array
    {
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        return $this->createQueryBuilder('s')
        
            ->andWhere('s.calendrierUtilisateur = :calendrierUtilisateur')
            ->andWhere('s.deletedAt IS NULL')
            ->setParameter('calendrierUtilisateur', $calendrierUtilisateur)
            ->orderBy('s.createdAt', $order)
            ->getQuery()
            ->getResult();
    }



}