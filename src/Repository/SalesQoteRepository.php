<?php

namespace App\Repository;

use App\Entity\SalesQote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SalesQote|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalesQote|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalesQote[]    findAll()
 * @method SalesQote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalesQoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesQote::class);
    }

    // /**
    //  * @return SalesQote[] Returns an array of SalesQote objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SalesQote
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findSalesQotesByProject($id): SalesQote
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT sq.*, p.title FROM sales_qote sq INNER JOIN project p on sq.projet_id = p.id WHERE sq.projet_id = :id ORDER BY sq.date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    public function findOneSalesQotesByProject($id, $projectId): SalesQote
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT sq.*, p.title FROM sales_qote sq INNER JOIN project p on sq.projet_id = p.id WHERE sq.projet_id = :projectId AND sq.id = :id ORDER BY sq.date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['projectId' => $projectId]);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }
}
