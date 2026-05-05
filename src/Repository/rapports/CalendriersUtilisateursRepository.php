<?php

namespace App\Repository\rapports;

use App\Entity\rapports\CalendriersUtilisateurs;
use App\Entity\rapports\Calendriers;
use App\Entity\utilisateurs\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CalendriersUtilisateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendriersUtilisateurs::class);
    }

    /**
     * Trouver par utilisateur
     * @return CalendriersUtilisateurs[]
     */
    public function findByUtilisateur(Utilisateurs $utilisateur, string $order = 'DESC',int $limit = 10): array
    {
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        return $this->createQueryBuilder('cu')
            ->leftJoin('cu.calendrier', 'u')      // 🔥 IMPORTANT
            ->addSelect('u')       
            ->andWhere('cu.utilisateur = :utilisateur')
            ->andWhere('cu.deletedAt IS NULL')
            ->setParameter('utilisateur', $utilisateur)
            ->orderBy('cu.createdAt', $order)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver par calendrier
     * @return CalendriersUtilisateurs[]
     */
    public function findByCalendrier(Calendriers $calendrier, string $order = 'DESC'): array
    {
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        return $this->createQueryBuilder('cu')
            ->leftJoin('cu.utilisateur', 'u')      // 🔥 IMPORTANT
            ->addSelect('u')                       // 🔥 IMPORTANT
            ->andWhere('cu.calendrier = :calendrier')
            ->andWhere('cu.deletedAt IS NULL')
            ->setParameter('calendrier', $calendrier)
            ->orderBy('cu.createdAt', $order)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifier si un utilisateur est déjà assigné à un calendrier
     */
    public function findOneByUtilisateurAndCalendrier(Utilisateurs $utilisateur, Calendriers $calendrier): ?CalendriersUtilisateurs
    {
        return $this->createQueryBuilder('cu')
            ->andWhere('cu.utilisateur = :utilisateur')
            ->andWhere('cu.calendrier = :calendrier')
            ->andWhere('cu.deletedAt IS NULL')
            ->setParameter('utilisateur', $utilisateur)
            ->setParameter('calendrier', $calendrier)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findActiveById(int $id): ?CalendriersUtilisateurs
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.utilisateur', 'u')      // 🔥 IMPORTANT
            ->leftJoin('t.calendrier', 'c')      // 🔥 IMPORTANT
            ->addSelect('u')                       // 🔥 IMPORTANT
            ->addSelect('c')                       // 🔥 IMPORTANT
            ->andWhere('t.id = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findOneByUtilisateurAndCalendrierDeletedAt(Utilisateurs $utilisateur, Calendriers $calendrier,string $order = 'DESC'): array
    {
        return $this->createQueryBuilder('cu')
            ->andWhere('cu.utilisateur = :utilisateur')
            ->andWhere('cu.calendrier = :calendrier')
            ->andWhere('cu.deletedAt IS NOT NULL')
            ->setParameter('utilisateur', $utilisateur)
            ->setParameter('calendrier', $calendrier)
            ->orderBy('cu.createdAt', $order)
            ->getQuery()
            ->getResult();
    }
}