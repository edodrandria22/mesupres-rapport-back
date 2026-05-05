<?php

namespace App\Repository\utilisateurs;
use App\Repository\utils\BaseRepository;
use App\Entity\utilisateurs\Utilisateurs;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\rapports\Calendriers;
use App\Dto\utils\OrderCriteria;
class UtilisateursRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateurs::class);
    }

    //    /**
    //     * @return Utilisateur[] Returns an array of Utilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Utilisateur
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function login(string $email, string $plainPassword): ?Utilisateurs
    {
        $user = $this->findOneBy(['email' => $email]);

        if ($user && password_verify($plainPassword, $user->getMdp())) {
            return $user;
        }

        return null; 
    }
    public function getAllParOrdre(OrderCriteria $criteria): array
       {
           return $this->createQueryBuilder('u')
               
               ->orderBy('u.'.$criteria->getField(), $criteria->getDirection())
               ->where('u.deletedAt IS NULL')
               ->getQuery()
               ->getResult()
           ;
       }
     /**
     * Retourne tous les utilisateurs qui ne sont pas liés au calendrier donné
     *
     * @param Calendriers $calendrier
     * @return Utilisateurs[]
     */
    public function findUsersNotInCalendrier(Calendriers $calendrier): array
    {
        $subQb = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(cu2.utilisateur)')
            ->from('App\Entity\rapports\CalendriersUtilisateurs', 'cu2')
            ->where('cu2.calendrier = :calendrier')
            ->andWhere('cu2.deletedAt IS NULL');

        $qb = $this->createQueryBuilder('u');

        $qb->where(
            $qb->expr()->notIn('u.id', $subQb->getDQL())
        )
        ->setParameter('calendrier', $calendrier);

        return $qb->getQuery()->getResult();
    }
    

}
