<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Expenses;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Expenses>
 */
class ExpensesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expenses::class);
    }

    //    /**
    //     * @return Expenses[] Returns an array of Expenses objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Expenses
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getTotalForCurrentMonth(User $user): float
    {
        $firstDay = new \DateTime('first day of this month 00:00:00');
        $lastDay = new \DateTime('last day of this month 23:59:59');
    
        $qb = $this->createQueryBuilder('e')
            ->select('SUM(e.amount)')
            ->where('e.user = :user')
            ->andWhere('e.date BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $firstDay)
            ->setParameter('end', $lastDay);
    
        return (float) $qb->getQuery()->getSingleScalarResult();
    }
    
        
}
