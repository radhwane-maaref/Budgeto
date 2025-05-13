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

    public function sumTotalExpenses($user): float
    {
        return (float) $this->createQueryBuilder('e')
            ->select('SUM(e.amount)')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function sumExpensesByCategory($user): array
    {
        return $this->createQueryBuilder('e')
            ->select('c.name AS category, SUM(e.amount) AS total')
            ->join('e.category', 'c')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->groupBy('c.name')
            ->getQuery()
            ->getResult();
    }
    

    
        
}
