<?php

namespace App\Controller;

use App\Entity\Panel;
use App\Entity\Configurator;
use App\Repository\PanelRepository;
use App\Repository\ConfiguratorRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\AcceptHeader;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanelController extends AbstractFOSRestController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var PanelRepository
     */
    private $panelRepository;
    /**
     * @var ConfiguratorRepository
     */
    private $configuratoRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * ComponentController constructor.
     * @param SerializerInterface $serializer
     * @param PanelRepository $panelRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, PanelRepository $panelRepository, ConfiguratorRepository $configuratoRepository, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->panelRepository = $panelRepository;
        $this->configuratoRepository = $configuratoRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Rest\Post("/api/panel", name="fos_createpanel")
     * @param Request $request
     * @return JsonResponse
     */
    public function createPanel(Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse(["Le type de media envoyer n'est pas supporter; il doit être de type application/json "], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $Data = json_decode($request->getContent(), true);
        $panel = $this->panelRepository->findOneBy([
            'type' => $Data['type']
        ]);
        if (!is_null($panel)) {
            return new JsonResponse('Cet panneau existe déjà', Response::HTTP_CONFLICT);
        }

        $panel = new Panel();
        $panel->setType($Data['type']);
        $panel->setDescription($Data['description']);
        $panel->setPrice($Data['price']);
        $this->entityManager->persist($panel);
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/api/panel/{id}", name="fos_updatePanel")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePanel($id, Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse(["Le type de media envoyer n'est pas supporter; il doit être de type application/json "], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $panel = $this->panelRepository->find($id);
        if (!$panel) {
            return new JsonResponse("Cet panneau n'existe pas !", Response::HTTP_NOT_FOUND);
        }

        $Data = json_decode($request->getContent(), true);
        $panel->setDescription($Data['description']);
        $panel->setType($Data['type']);
        $panel->setPrice($Data['price']);
        $this->entityManager->flush();
        $response = $this->serializer->serialize($panel, 'json', SerializationContext::create()->setGroups(array('panels_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/panel", name="fos_getPanel")
     * @return JsonResponse
     */
    public function getPanel(): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }
        $panels = $this->panelRepository->findAll();
        $Data = $this->serializer->serialize($panels, 'json', SerializationContext::create()->setGroups(array('panels_details')));
        return JsonResponse::fromJsonString($Data, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/panel/{id}", name="fos_getOnePanel")
     * @param $id
     * @return JsonResponse
     */
    public function getOnePanel($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }
        $panel = $this->panelRepository->find($id);
        if (!$panel) {
            return new JsonResponse("Cet panneau n'existe pas !", Response::HTTP_NOT_FOUND);
        }
        $Data = $this->serializer->serialize($panel, 'json', SerializationContext::create()->setGroups(array('panels_details')));
        return JsonResponse::fromJsonString($Data, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/api/panel/{id}", name="fos_deletepanel")
     * @param $id
     * @return JsonResponse
     */
    public function deletePanel($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }
        $panel = $this->panelRepository->find($id);
        if (!$panel) {
            return new JsonResponse("Cet panneau n'existe pas !", Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($panel);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);

    }

}
