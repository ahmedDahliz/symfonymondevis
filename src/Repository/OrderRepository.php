<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Project;
use App\Repository\LigncommandRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @var LigncommandRepository
     */
    private $lignCommandeRepository;
    /**
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;

    public function __construct(ManagerRegistry $registry, ProjectRepository $projectRepository, LigncommandRepository $lignCommandeRepository,  ComponentRepository $componentRepository)
    {
        parent::__construct($registry, Order::class);
        $this->lignCommandeRepository = $lignCommandeRepository;
        $this->componentRepository = $componentRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @return Project[]|mixed Returns an array of Project objects
     */

    public function searchOrder($searchData, $isClient)
    {
        $types = [
            'quotedProject' => 'Projet devisé',
            'inCatalog' => 'En Catalogue'
        ];
        try {
            $query = $this->createQueryBuilder('o');
            if (!empty($searchData['type'])) {
                $query = $query
                    ->andWhere('o.type LIKE :type')
                    ->setParameter('type', "%{$types[$searchData['type']]}%");
                if($searchData['type'] == 'quotedProject') {
                    if (!empty($searchData['project'])) {
                        $projet = $this->projectRepository->createQueryBuilder('p')
                            ->where('p.title LIKE :title')
                            ->setParameter('title', "%".trim($searchData['project'])."%")->getQuery()->getResult();

                        $prjSQ = array_map(function($prj){
                            return $prj->getSalesQotes();
                        },$projet);
                        $sqId = array_map(function($sq){
                            return $sq[0]->getId();
                        }, $prjSQ);
                        $query = $query
                            ->andWhere('o.salseQuot IN (:salseQuots)')
                            ->setParameter('salseQuots', $sqId);
                    }
                }
                if($searchData['type'] == 'inCatalog') {
                    if (!empty($searchData['components'])) {
                        $component = $this->componentRepository->createQueryBuilder('c')
                            ->where('c.title LIKE :title')
                            ->setParameter('title', "%{$searchData['components']}%")->getQuery()->getResult();

                        $orderLine  =  $this->lignCommandeRepository->findBy([
                            'component' =>  array_map(function($comp){
                                return $comp->getId();
                            },$component)
                        ]);
                        $lcIds = array_map(function($lc){
                            return $lc->getId();
                        },$orderLine);
                        $query = $query->select('o')
                            ->join('o.ligncommands', 'lc', Join::WITH, 'lc.id in (:lingcommande)' )
                            ->setParameter('lingcommande',  $lcIds);
                    }
                }
            }

            if (!empty($searchData['created_at'])) {
                $query = $query
                    ->andWhere('o.createAt = :created_at')
                    ->setParameter('created_at', new \DateTime($searchData['created_at']));
            }


            if (!empty($searchData['status'])) {
                $query = $query
                    ->andWhere('o.status LIKE :status')
                    ->setParameter('status', "%{$searchData['status']}%");
            }
            if ($isClient){
                if (!empty($isClient->getId())) {
                    $query = $query
                        ->andWhere('o.client = :client')
                        ->setParameter('client', $isClient->getId());
                }
            }else {
                if (!empty($searchData['client'])) {
                    $query = $query
                        ->andWhere('o.client = :client')
                        ->setParameter('client', $searchData['client']);
                }
            }

            return $query->getQuery()->getResult();

        } catch (\Exception $e) {
        }


    }



    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
