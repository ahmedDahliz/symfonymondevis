<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }


    /**
     * @return Project[]|mixed Returns an array of Project objects
     */

    public function searchProjet($searchData, $isClient)
    {
        try {

            $query = $this->createQueryBuilder('p');

            if (!empty($searchData['title'])) {
                $query = $query
                    ->andWhere('p.title LIKE :title')
                    ->setParameter('title', "%{$searchData['title']}%");
//                return $query->getQuery()->getResult()[0]->getId();
            }

            if (!empty($searchData['adress'])) {
                $query = $query
                    ->andWhere('p.adress LIKE :adress')
                    ->setParameter('adress', "%{$searchData['adress']}%");
            }

            if (!empty($searchData['price'])) {
                $query = $query
                    ->andWhere('p.price LIKE :price')
                    ->setParameter('price', "%{$searchData['price']}%");
            }

            if (!empty($searchData['createdAt'])) {
                $query = $query
                    ->andWhere('p.createdAt = :createdAt')
                    ->setParameter('createdAt', new \DateTime($searchData['created_at']));
            }


            if (!empty($searchData['status'])) {
                $query = $query
                    ->andWhere('p.status = :status')
                    ->setParameter('status', $searchData['status']);
            }

            if ($isClient){
                if (!empty($isClient->getId())) {
                    $query = $query
                        ->andWhere('p.client = :client')
                        ->setParameter('client', $isClient->getId());
                }

            }else {
                if (!empty($searchData['idClient'])) {
                    $query = $query
                        ->andWhere('p.client = :client')
                        ->setParameter('client', $searchData['idClient']);
                }
            }
            return $query->getQuery()->getResult();

        } catch (\Exception $e) {
        }


    }


























    // public function searchProjet($searchData, $isClient)
    // {
    //     try {
    //         if ($isClient){
    //             return $this->createQueryBuilder('p')
    //                 ->Where('p.title = :title')
    //                 ->setParameter('title', $searchData['title'])
    //                 ->andWhere('p.adress = :adress')
    //                 ->setParameter('adress', $searchData['adress'])
    //                 ->andWhere('p.price = :price')
    //                 ->setParameter('price', $searchData['price'])
    //                 ->andWhere('p.createdAt = :createdAt')
    //                 ->setParameter('createdAt', new \DateTime($searchData['created_at']))
    //                 ->andWhere('p.client = :client')
    //                 ->setParameter('client', $isClient->getId())
    //                 ->getQuery()
    //                 ->getResult();
    //         }else {
    //             return $this->createQueryBuilder('p')
    //                 ->Where('p.title = :title')
    //                 ->setParameter('title', $searchData['title'])
    //                 ->andWhere('p.adress = :adress')
    //                 ->setParameter('adress', $searchData['adress'])
    //                 ->andWhere('p.price = :price')
    //                 ->setParameter('price', $searchData['price'])
    //                 ->andWhere('p.createdAt = :createdAt')
    //                 ->setParameter('createdAt', new \DateTime($searchData['created_at']))
    //                 ->andWhere('p.client = :client')
    //                 ->setParameter('client', $searchData['idClient'])
    //                 ->getQuery()
    //                 ->getResult();
    //         }

    //     } catch (\Exception $e) {
    //     }


    // }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    // public function findOneBySomeField($searchData): ?Project
    // {
    //     return $this->createQueryBuilder('p')
    //     ->andWhere('p.title = :title')
    //     ->setParameter('title', $searchData['title'])
    //     ->andWhere('p.adress = :adress')
    //     ->setParameter('adress', $searchData['adress'])
    //     ->andWhere('p.price = :price')
    //     ->setParameter('price', $searchData['price'])
    //     ->andWhere('p.createdAt = :createdAt')
    //     ->setParameter('createdAt', $searchData['createdAt'])
    //     ->andWhere('p.client = :client')
    //     ->setParameter('client', $searchData['client']['first_name']) 
    //     ->getQuery()
    //     ->getOneOrNullResult() ;

    // }

}
