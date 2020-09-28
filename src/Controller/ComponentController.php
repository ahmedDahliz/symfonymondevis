<?php

namespace App\Controller;

use App\Entity\AssociatedFile;
use App\Entity\Component;
use App\Entity\Product;
use App\Repository\AssociatedFileRepository;
use App\Repository\ComponentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ComponentController extends AbstractFOSRestController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var AssociatedFileRepository
     */
    private $associatedFileRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ComponentController constructor.
     * @param SerializerInterface $serializer
     * @param ComponentRepository $componentRepository
     * @param EntityManagerInterface $entityManager
     * @param AssociatedFileRepository $associatedFileRepository
     */
    public function __construct(SerializerInterface $serializer, ProductRepository $productRepository, ComponentRepository $componentRepository, EntityManagerInterface $entityManager, AssociatedFileRepository $associatedFileRepository)
    {
        $this->serializer = $serializer;
        $this->componentRepository = $componentRepository;
        $this->entityManager = $entityManager;
        $this->associatedFileRepository = $associatedFileRepository;
        $this->productRepository = $productRepository;
    }


    /**
     * to upload a file to the corresponding directory
     * @param $file
     * @param $path
     * @param $directory
     * @param Request $request
     * @return String
     */
    private function uploadFiles($path, $directory, Request $request, UploadedFile $file = null)
    {
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        if ($file) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = str_replace(' ', '-', $fileName);
            $safeFileName = preg_replace('/[^A-Za-z0-9\-]/', '', $fileName);
            $newFilename = 'mondevis-' . $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter($directory), $newFilename);
            } catch (\Exception $e) {
                return new JsonResponse("Une exception a été produite lors du téléchargement de l'image ", Response::HTTP_EXPECTATION_FAILED);
            }
            return $url . $path . $newFilename;
        }
        return $this->getDefaultImage($request, $path);
    }

    /**
     * to upload a file to the corresponding directory
     * @param Request $request
     * @param $path
     * @param bool $isImage
     * @return String
     */
    private function getDefaultImage(Request $request, $path)
    {
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        return $url . $path . 'image-default.png';
    }

    /**
     * @Rest\Post("/api/components", name="fos_createComponent")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     */
    public function createComponent(Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $imageFile = $request->files->get('image');
        $imageFullPath = $this->uploadFiles('/uploads/component/images/', 'component_images_directory', $request, $imageFile);

        $componentData = json_decode($request->request->get('component'), true);
        $component = $this->componentRepository->findOneBy([
            'manRef' => $componentData['man_ref']
        ]);
        if (!is_null($component)) {
            return new JsonResponse('Ce composant existe déjà', Response::HTTP_CONFLICT);
        }
        $component = new Component();
        $component->setManRef(trim($componentData['man_ref']));
        $component->setRexelRef(trim($componentData['rexel_ref']));
        $component->setTitle(trim($componentData['title']));
        $component->setDescription(trim($componentData['description']));
        $component->setQuantity(trim($componentData['quantity']));
        $component->setPrice(trim($componentData['price']));
        $component->setType(trim($componentData['type']));
        $component->setImage($imageFullPath);
        foreach ($componentData['products'] as $productId) {
            $product = $this->productRepository->find($productId);
            $component->addProduct($product);
        }
        $this->entityManager->persist($component);

        $associatedFiles = [];
        foreach ($request->files->all() as $key => $file) {
            if (substr($key, 0, 4) == 'pd_f') $associatedFiles[] = $file;
        }
        foreach ($associatedFiles as $associatedFile) {
            $fileFullPath = $this->uploadFiles('/uploads/component/files/', 'component_files_directory', $request, $associatedFile);
            $componentFile = new AssociatedFile();
            $componentFile->setTitle(trim($associatedFile->getClientOriginalName()));
            $componentFile->setPath($fileFullPath);
            $componentFile->setComponent($component);
            $this->entityManager->persist($componentFile);
        }
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);

    }

    /**
     * @Rest\Put("/api/components/{id}", name="fos_updateComponent")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateComponents($id, Request $request)
    {

        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $component = $this->componentRepository->find($id);
        if (!$component) {
            return new JsonResponse("Ce composant n'existe pas !", Response::HTTP_NOT_FOUND);
        }
        $imageFile = $request->files->get('image');

        $imageDeleted = filter_var($request->request->get('imageDeleted'), FILTER_VALIDATE_BOOLEAN);
        ($imageDeleted || $imageFile) ? $imageFullPath = $this->uploadFiles('/uploads/component/images/', 'component_images_directory', $request, $imageFile) : $imageFullPath = null;
        $componentData = json_decode($request->request->get('component'), true);
        $component->setManRef(trim($componentData['man_ref']));
        $component->setRexelRef(trim($componentData['rexel_ref']));
        $component->setTitle(trim($componentData['title']));
        $component->setDescription(trim($componentData['description']));
        $component->setQuantity(trim($componentData['quantity']));
        $component->setPrice(trim($componentData['price']));
        $component->setType(trim($componentData['type']));
        if ($imageFullPath) {
            $component->setImage($imageFullPath);
        }
        $component->removeAllProducts();
        foreach (json_decode($request->request->get('products'), true) as $productId) {
            $product = $this->productRepository->find($productId);
            $component->addProduct($product);
        }
        $deletedFilesIds = json_decode($request->request->get('deletedFilesIds'), true);
        foreach ($deletedFilesIds as $fileId) {
            $associatedFile = $this->associatedFileRepository->find($fileId);
            $component->removeFile($associatedFile);
            $this->entityManager->remove($associatedFile);
        }
        $associatedFiles = [];
        foreach ($request->files->all() as $key => $file) {
            if (substr($key, 0, 4) == 'pd_f') $associatedFiles[] = $file;
        }
        foreach ($associatedFiles as $associatedFile) {
            $fileFullPath = $this->uploadFiles('/uploads/component/files/', 'component_files_directory', $request, $associatedFile);
            $componentFile = new AssociatedFile();
            $componentFile->setTitle(trim($associatedFile->getClientOriginalName()));
            $componentFile->setPath($fileFullPath);
            $componentFile->setComponent($component);
            $this->entityManager->persist($componentFile);
        }
        $this->entityManager->flush();
        $response = $this->serializer->serialize($component, 'json', SerializationContext::create()->setGroups(array('components_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/components", name="fos_getComponents")
     * @return JsonResponse
     */
    public function getComponents(): JsonResponse
    {
//        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
//            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
//        }
        $components = $this->componentRepository->findAll();
        $componentsData = $this->serializer->serialize($components, 'json', SerializationContext::create()->setGroups(array('components_details', 'components_product_details')));
        return JsonResponse::fromJsonString($componentsData, Response::HTTP_OK);
    }


    /**
     * @Rest\Get("/api/components/byType", name="fos_getComponentsByType")
     * @return JsonResponse
     */
    public function getComponentsByType(): JsonResponse
    {
//        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
//            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
//        }
        $components = $this->componentRepository->findAll();
        $components = $this->groupByType($components);
        $componentsData = $this->serializer->serialize($components, 'json', SerializationContext::create()->setGroups(array('components_details')));
        return JsonResponse::fromJsonString($componentsData, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/api/components/{id}", name="fos_getOneComponent")
     * @param $id
     * @return JsonResponse
     */
    public function getOneComponents($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }

        $component = $this->componentRepository->find($id);
        if (!$component) {
            return new JsonResponse("Ce composant n'existe pas !", Response::HTTP_NOT_FOUND);
        }

        $componentData = $this->serializer->serialize($component, 'json', SerializationContext::create()->setGroups(array('components_details', 'components_product_details')));
        return JsonResponse::fromJsonString($componentData, Response::HTTP_OK);

    }

    private function groupByType(array $components)
    {
        return array_reduce($components,
            function ($list, $component) {
                $list[$component->getType()][] = $component;
                return $list;
            }, []);
    }

    /**
     * @Rest\Delete("/api/components/{id}", name="fos_deleteomponents")
     * @param $id
     * @return JsonResponse
     */
    public function deleteComponents($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }
        $component = $this->componentRepository->find($id);
        if (!$component) {
            return new JsonResponse('Ce composant n\'existe pas !', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($component);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);

    }


}
