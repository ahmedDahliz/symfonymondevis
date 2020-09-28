<?php

namespace App\Repository;

use App\Entity\Chimeny;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chimeny|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chimeny|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chimeny[]    findAll()
 * @method Chimeny[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChimenyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chimeny::class);
    }

    // /**
    //  * @return Chimeny[] Returns an array of Chimeny objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chimeny
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
