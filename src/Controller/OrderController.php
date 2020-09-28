<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\SalesQote;
use App\Entity\Component;
use App\Entity\User;
use App\Entity\Ligncommand;


use App\Repository\OrderRepository;
use App\Repository\SalesQoteRepository;
use App\Repository\ComponentRepository;
use App\Repository\UserRepository;
use App\Repository\LigncommandRepository;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var SalesQoteRepository
     */
    private $salesQoteRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;

    /**
     * @var LigncommandRepository
     */
    private $lignCommandRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * OrderController constructor.
     * @param SerializerInterface $serializer
     * @param OrderRepository $orderRepository
     * @param SalesQoteRepository $salesQoteRepository
     * @param ComponentRepository $componentRepository
     * @param LigncommandRepository $lignCommandRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */


    public function __construct(SerializerInterface $serializer,
                                OrderRepository $orderRepository,
                                SalesQoteRepository $salesQoteRepository,
                                ComponentRepository $componentRepository,
                                LigncommandRepository $lignCommandRepository,
                                UserRepository $userRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
        $this->salesQoteRepository = $salesQoteRepository;
        $this->componentRepository = $componentRepository;
        $this->lignCommandRepository = $lignCommandRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Rest\Post("/api/orders", name="fos_createOrders")
     * @param Request $request
     * @return mixed|JsonResponse
     */
    public function createOrder(Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }

        // $ordersData = json_decode($request->request->get('order'), true);
        // $orderComponentData = json_decode($request->request->get('ligncommands'), true);
        $ordersData = json_decode($request->getContent(), true);
        // $orser = $this->orderRepository->findOneBy([
        //     'id' => $ordersData['id']
        // ]);

        // if (!is_null($orser)) {
        //     return new JsonResponse('Ce Commande existe déjà', Response::HTTP_CONFLICT);
        // }

        $orderdata = new Order();
        $orderdata->setType('En Catalogue');
        $orderdata->setStatus('Nouveau');
        $client = $this->userRepository->findOneBy([
            'email' => $this->getUser()->getUsername()
        ]);
        $orderdata->setClient($client);
//        if ($ordersData['client']) {
//            $client = $this->userRepository->find($ordersData['client']['id']);
//        }
        if ($ordersData['salseQuot']) {
            $salseQuot = $this->salesQoteRepository->find($ordersData['salseQuot']);
            $orderdata->setType('Projet devisé');
            $orderdata->setSalseQuot($salseQuot);
        }

        $this->entityManager->persist($orderdata);
        $this->entityManager->flush();

        if(isset($ordersData['ligncommands'])) {
            foreach ($ordersData['ligncommands'] as $obj) {
                $ordercomponentDATA = new Ligncommand();
                $ordercomponentDATA->setCommand($orderdata);
                if ($obj['component']) {
                    $component = $this->componentRepository->find($obj['component']['id']);
                    $ordercomponentDATA->setComponent($component);
                }
                $ordercomponentDATA->setPrice($obj['price']);
                $ordercomponentDATA->setQuantity($obj['quantity']);

                $this->entityManager->persist($ordercomponentDATA);
            }
        }
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);

    }


    /**
     * @Rest\Post("/api/orders/{projectId}", name="fos_orderProject")
     * @param Request $request
     * @param $projectId
     * @return JsonResponse
     */
//    public function orderAllByProject(Request $request, $projectId)
//    {
//        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
//        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
//            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
//        }
//        $ordersData = json_decode($request->getContent(), true);
//        foreach ($ordersData as $orderData) {
//            $order = new Order();
//            $order->setType("projet devis");
//            $order->setStatus("Nouveau");
//            if ($orderData['client']) {
//                $client = $this->userRepository->find($orderData['client']);
//                $order->setClient($client);
//            }
//            if (!is_null($projectId)) {
//                $salseQuot = $this->salesQoteRepository->findSalesQotesByProject($projectId);
//                $order->setSalseQuot($salseQuot);
//            }
//            $this->entityManager->persist($order);
//        }
//        $this->entityManager->flush();
//        return new JsonResponse([], Response::HTTP_OK);
//    }

    /**
     * Find a sales quote By Project
     * @Rest\Post("/api/orders/{projectId}/{salesQuoteId}", name="fos_orderProject")
     * @param Request $request
     * @param $projectId
     * @param $salesQuoteId
     * @return JsonResponse
     */
//    public function orderByProject(Request $request, $projectId, $salesQuoteId)
//    {
//        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
//        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
//            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
//        }
//        $ordersData = json_decode($request->getContent(), true);
//        foreach ($ordersData as $orderData) {
//            $order = new Order();
//            $order->setType("composent");
//            $order->setStatus("En catalogue");
//            if ($orderData['client']) {
//                $client = $this->userRepository->find($orderData['client']);
//                $order->setClient($client);
//            }
//            if (!is_null($projectId) && !is_null($salesQuoteId)) {
//                $salseQuot = $this->salesQoteRepository->findOneSalesQotesByProject($salesQuoteId, $projectId);
//                $order->setSalseQuot($salseQuot);
//            }
//            $this->entityManager->persist($order);
//        }
//    }


    /**
     * @Rest\Post("/api/filter/order", name="fos_orderFilter")
     * @param Request $request
     * @return mixed
     */

    public function search(Request $request)
    {

        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $searchData = json_decode($request->getContent(), true);

        $orders = [];
        $isClient = null;
        if($this->getUser()->getRoles()[0] == 'ROLE_USER'){
            $isClient = $this->userRepository->findOneBy([
                'email' => $this->getUser()->getUsername()
            ]);
        }
//         return $searchData;
//         return $this->orderRepository->searchOrder($searchData, $isClient);
        $orders = $this->orderRepository->searchOrder($searchData, $isClient);
        if (!$orders) {
            return new JsonResponse('Cette commande n\'existe pas !', Response::HTTP_NOT_FOUND);
        }
        $prjectsData = $this->serializer->serialize($orders, 'json', SerializationContext::create()->setGroups(array('order_details')));
        return JsonResponse::fromJsonString($prjectsData, Response::HTTP_OK);
    }


    /**
     * @Rest\Get("/api/orders", name="fos_ordersOrders")
     * @return JsonResponse|mixed
     */
    public function getOrders()
    {
        switch ($this->getUser()->getRoles()[0]) {
            case 'ROLE_ADMIN':
                $clients = $this->userRepository->findBy([
                    'createdBy' => $this->getUser()
                ]);
                $orders = $this->orderRepository->createQueryBuilder('o')
                    ->where('o.client IN (:clients)')
                    ->setParameter('clients', $clients)
                    ->getQuery()->getResult();
                break;
            case 'ROLE_USER':

                $orders = $this->orderRepository->findBy([
                    'client' => $this->getUser()
                ]);
                break;
            case 'ROLE_SUPER_ADMIN':
                $orders = $this->orderRepository->findAll();
                break;
        }

        $data = $this->serializer->serialize($orders, 'json', SerializationContext::create()->setGroups(array('order_details')));
        return JsonResponse::fromJsonString($data, Response::HTTP_OK);

    }

    /**
     * @Rest\Post("/api/orders/update/status/{id}", name="fos_updateOrdersStatus")
     * @return JsonResponse|mixed
     */
    public function updateStatus($id, Request $request)
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }

        $orders = $this->orderRepository->find($id);
        $orders->setStatus($request->getContent());
        $this->entityManager->flush();
        return JsonResponse::fromJsonString("", Response::HTTP_OK);

    }
}
