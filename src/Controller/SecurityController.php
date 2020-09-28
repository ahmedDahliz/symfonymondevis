<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class SecurityController
 * @package App\Controller
 * @Route("api", name="api_")
 */
class SecurityController extends AbstractFOSRestController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * RegistrationController constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return resource|string
     */
    public function index(Request $request)
    {
        $avatarFile = $request->files->get('avatar');
        $avatarFullPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/avatar/avatar-default.png';
        if ($avatarFile) {
            $avatarName = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            $avatarName = str_replace(' ', '-', $avatarName);
            $safeAvatarName = preg_replace('/[^A-Za-z0-9\-]/', '', $avatarName);
            $newFilename = 'mondevis-' . $safeAvatarName . '-' . uniqid() . '.' . $avatarFile->guessExtension();
            try {
                $avatarFile->move($this->getParameter('avatar_directory'), $newFilename);
            } catch (\Exception $e) {
                return new JsonResponse("Une exception a été produite lors du telechargement de l'image ", Response::HTTP_EXPECTATION_FAILED);
            }
            $avatarFullPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/avatar/' . $newFilename;

        }
        $userData = json_decode($request->request->get('user'), true);
        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        $email = $userData['email'];
        $password = $userData['password'];
        $roles = $userData['roles'];
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            //just to make sure
            if ($roles != ['ROLE_USER'])
                $roles = ['ROLE_USER'];
        }
        $createdby = $this->userRepository->findOneBy([
            'email' => $this->getUser()->getUsername()
        ]);
        $user = $this->userRepository->findOneBy([
            'email' => $email
        ]);

        if (!is_null($user)) {
            return $this->view([
                'message' => 'Cette utilisateur existe déjà'
            ], Response::HTTP_CONFLICT);
        }
        $user = new User();
        $user->setRoles($roles);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setCreatedBy($createdby);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );
        $user->setAvatarPath($avatarFullPath);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->view($user, Response::HTTP_CREATED)
            ->setContext((new Context())->setGroups(['public']));

    }

    /**
     * @Route(name="login", path="/login_check")
     * @return JsonResponse
     */
    public function login()
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }
}
