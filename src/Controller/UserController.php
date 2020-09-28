<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use JMS\Serializer\SerializerInterface;

class UserController extends AbstractFOSRestController
{

    /**
     * @var SerializerInterface
     */
    private $_serializer;
    /**
     * @var UserRepository
     */
    private $_userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $_entityManager;

    /**
     * UpdateUserController constructor.
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->_serializer = $serializer;
        $this->_userRepository = $userRepository;
        $this->_entityManager = $entityManager;
    }


    /**
     * @Rest\Put("/api/users/{id}", name="fos_updateUser")
     * @param $id
     * @param Request $request
     * @param UserPasswordEncoderInterface $encode
     * @return mixed|JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUser($id, Request $request, UserPasswordEncoderInterface $encode)
    {

        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse(["Le type de media envoyer n'est pas supporter; il doit être de type application/json "], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $user = $this->_userRepository->find($id);
        if (!$user) {
            return new JsonResponse(["Cette utilisateur n'existe pas !"], Response::HTTP_NOT_FOUND);
        }
        $avatarFile = $request->files->get('avatar');
        $avatarDeleted = filter_var($request->request->get('avatarDeleted'), FILTER_VALIDATE_BOOLEAN);
        $avatarDeleted ? $avatarFullPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/avatar/avatar-default.png' : $avatarFullPath = null;
        if ($avatarFile) {
            $avatarName = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            $avatarName = str_replace(' ', '-', $avatarName);
            $safeAvatarName = preg_replace('/[^A-Za-z0-9\-]/', '', $avatarName);
            $newFilename = 'mondevis-' . $safeAvatarName . '-' . uniqid() . '.' . $avatarFile->guessExtension();
            try {
                $avatarFile->move($this->getParameter('avatar_directory'), $newFilename);
                $isUpload = true;
            } catch (\Exception $e) {
                return new JsonResponse("Une exception a été produite lors du telechargement de l'image ", Response::HTTP_EXPECTATION_FAILED);
            }
            $avatarFullPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/avatar/' . $newFilename;

        }
        $data = json_decode($request->request->get('user'));

        $user->setFirstName($data->first_name);
        $user->setLastName($data->last_name);
        $user->setEmail($data->email);
        $user->setRoles($data->roles);
        if ($avatarFullPath) {
            $user->setAvatarPath($avatarFullPath);
        }
        if ($data->password) {
            $user->setPassword($encode->encodePassword($user, $data->password));
        }
        $this->_entityManager->flush();
        $response = $this->_serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/users", name="fos_getUsers")
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $users = $this->_userRepository->findOneBy([
                'email' => $this->getUser()->getUsername()
            ])->getUsers();
        } else {
            $users = $this->_userRepository->findAll();
        }
        $data = $this->_serializer->serialize($users, 'json', SerializationContext::create()->setGroups(array('user_details')));
        $response = '{"hydra:member":' . $data . '}';
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/users/{id}", name="fos_getOneUser")
     * @param $id
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getOneUser($id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse("Cette utilisateur n'existe pas !", Response::HTTP_NOT_FOUND);
        }
        $response = $this->_serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/api/users/{id}", name="fos_deleteUser")
     * @param $id
     * @return JsonResponse
     */
    public function deleteUser($id): JsonResponse
    {
        $user = $this->_userRepository->find($id);
        if (!$user) {
            return new JsonResponse(["Cette utilisateur n'existe pas !"], Response::HTTP_NOT_FOUND);
        }
        $this->_entityManager->remove($user);
        $this->_entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
