<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProjectSearchController extends AbstractFOSRestController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * ProjectSearchController constructor.
     * @param SerializerInterface $serializer
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer,
                                ProjectRepository $projectRepository,
                                UserRepository $userRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

    }

    /**
     * @Rest\Post("/api/projectSearch", name="fos_searchProjects")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse|mixed
     */

    public function search(Request $request)
    {
        $projects = [];
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }

        $searchData = json_decode($request->getContent(), true);
        $isClient = null;
        if($this->getUser()->getRoles()[0] == 'ROLE_USER'){
            $isClient = $this->userRepository->findOneBy([
                'email' => $this->getUser()->getUsername()
            ]);
        }

        $projects = $this->projectRepository->searchProjet($searchData, $isClient);
        if (!$projects) {
            return new JsonResponse('Ce projet n\'existe pas !', Response::HTTP_NOT_FOUND);
        }
        $prjectsData = $this->serializer->serialize($projects, 'json', SerializationContext::create()->setGroups(array('project_details')));
        return JsonResponse::fromJsonString($prjectsData, Response::HTTP_OK);
    }

    public function extractDataExcel(Request $request)
    {


    }


}
