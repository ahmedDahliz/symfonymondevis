<?php

namespace App\Repository;

use App\Entity\AssociatedFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssociatedFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssociatedFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssociatedFile[]    findAll()
 * @method AssociatedFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociatedFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociatedFile::class);
    }

    // /**
    //  * @return AssociatedFile[] Returns an array of AssociatedFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssociatedFile
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
