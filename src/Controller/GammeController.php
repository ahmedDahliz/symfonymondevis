<?php

namespace App\Controller;

use App\Entity\Gamme;
use App\Repository\GammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;


class GammeController extends AbstractFOSRestController
{

    /**
     * @var SerializerInterface
     */
    private $_serializer;
    /**
     * @var GammeRepository
     */
    private $_gammeRepository;
    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;


    /**
     * UpdateGammeController constructor.
     * @param SerializerInterface $serializer
     * @param GammeRepository $gammeRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, GammeRepository $gammeRepository, EntityManagerInterface $entityManager)
    {
        $this->_serializer = $serializer;
        $this->_gammeRepository = $gammeRepository;
        $this->_entityManager = $entityManager;
    }

    /**
     * @Rest\Post("/api/gammes", name="fos_creategamme")
     * @param Request $request
     * @return JsonResponse
     */
    public function createGamme(Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse(["Le type de media envoyer n'est pas supporter; il doit être de type application/json "], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $Data = json_decode($request->getContent(), true);

        $gamme = $this->_gammeRepository->findOneBy([
            'title' => $Data['title']
        ]);

        if (!is_null($gamme)) {
            return new JsonResponse('Ce gamme existe déjà', Response::HTTP_CONFLICT);
        }

        $gamme = new Gamme();
        $gamme->setTitle($Data['title']);
        $this->_entityManager->persist($gamme);
        $this->_entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);
    }


    /**
     * @Rest\Put("/api/gammes/{id}", name="fos_updateGamme")
     * @param $id
     * @param Request $request
     * @return mixed|JsonResponse
     */
    public function updateGamme($id, Request $request)
    {

        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse(["Le type de media envoyer n'est pas supporter; il doit être de type application/json "], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $gamme = $this->_gammeRepository->find($id);
        if (!$gamme) {
            return new JsonResponse(["Cette gamme n'existe pas !"], 404, []);
        }

        $data = json_decode($request->getContent(), true);
        $gamme->setTitle($data['title']);
        $this->_entityManager->flush();
        $response = $this->_serializer->serialize($gamme, 'json', SerializationContext::create()->setGroups(array('range_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }


    /**
     * @Rest\Get("/api/gammes", name="fos_getGammes")
     * @return JsonResponse
     */
    public function getGammes(): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }


        $gammes = $this->_gammeRepository->findAll();
        $data = $this->_serializer->serialize($gammes, 'json', SerializationContext::create()->setGroups(array('range_details')));
        return JsonResponse::fromJsonString($data, Response::HTTP_OK);

    }

    /**
     * @Rest\Get("/api/gammes/{id}", name="fos_getOneGamme")
     * @param $id
     * @return JsonResponse
     */
    public function getOneGamme($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }
        $gamme = $this->_gammeRepository->find($id);
        if (!$gamme) {
            return new JsonResponse(["Cette Gamme n'existe pas !"], Response::HTTP_NOT_FOUND, []);
        }
        $response = $this->_serializer->serialize($gamme, 'json', SerializationContext::create()->setGroups(array('range_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/api/gammes/{id}", name="fos_deleteGamme")
     * @param $id
     * @return JsonResponse
     */
    public function deleteGamme($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }
        $gamme = $this->_gammeRepository->find($id);
        if (!$gamme) {
            return new JsonResponse(["Cette Gamme n'existe pas !"], Response::HTTP_NOT_FOUND, []);
        }
        $this->_entityManager->remove($gamme);
        $this->_entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }


}
