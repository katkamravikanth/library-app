<?php

namespace App\Library\Repository;

use App\Library\Entity\Borrowing;
use App\Library\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Borrowing>
 */
class BorrowingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrowing::class);
    }

    /**
     * @return Borrowing[] Returns an array of Borrowing objects
     */
    public function findActiveBorrowingByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->andWhere('b.checkInDate IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
       ;
   }
}
