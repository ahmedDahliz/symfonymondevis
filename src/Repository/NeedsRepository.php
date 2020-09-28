<?php

namespace App\Repository;

use App\Entity\Needs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Needs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Needs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Needs[]    findAll()
 * @method Needs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NeedsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Needs::class);
    }

    // /**
    //  * @return Needs[] Returns an array of Needs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Needs
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
