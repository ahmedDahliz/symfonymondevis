<?php

namespace App\Repository;

use App\Entity\Configurator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Configurator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Configurator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Configurator[]    findAll()
 * @method Configurator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfiguratorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configurator::class);
    }

    // /**
    //  * @return Configurator[] Returns an array of Configurator objects
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
    public function findOneBySomeField($value): ?Configurator
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
