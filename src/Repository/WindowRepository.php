<?php

namespace App\Repository;

use App\Entity\Window;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Window|null find($id, $lockMode = null, $lockVersion = null)
 * @method Window|null findOneBy(array $criteria, array $orderBy = null)
 * @method Window[]    findAll()
 * @method Window[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WindowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Window::class);
    }

    // /**
    //  * @return Window[] Returns an array of Window objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Window
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
