<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\PanelRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var PanelRepository
     */
    private $panelRepsitory;

    private $filesystem;

    /**
     * ProductController constructor.
     *
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer, PanelRepository $panelRepository)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->panelRepsitory = $panelRepository;
        $this->filesystem = new Filesystem();
    }

    /**
     * Get All Products List.
     *
     * @Rest\Get("/api/products", name="getProducts")
     * @return Response|mixed
     */
    public function getAllProducts()
    {
        $products = $this->productRepository->findAll();
        $productData = $this->serializer->serialize($products, 'json', SerializationContext::create()->setGroups(array('product_details', 'components_product_details')));

        return new Response($productData, Response::HTTP_OK);
    }

    /**
     * Find a product.
     *
     * @Rest\GET("/api/products/{id}", name="findProduct")
     * @param $id
     * @return JsonResponse
     */
    public function findProductById($id)
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse('Ce produit n\'existe pas !', Response::HTTP_NOT_FOUND);
        }
        $productData = $this->serializer->serialize($product, 'json', SerializationContext::create()->setGroups(array('product_details', 'components_product_details')));
        return JsonResponse::fromJsonString($productData, Response::HTTP_OK);
    }

    /**
     * Create a product.
     *
     * @Rest\Post("/api/products", name="createProduct")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function createProduct(Request $request, SerializerInterface $serializer)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse([
                "Le type de media envoyé n'est pas supportée; il doit être de type application/json"
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, [
                "Status Code" => "415 Unsupported Media Type"
            ]);
        }

        $imageFile = $request->files->get('picture_image');
        $imageFullPath = $this->uploadFiles('/uploads/product/images/', 'product_image_directory', $request, $imageFile);
        $productData = json_decode($request->request->get('productData'), true);
        $linkedPanles = json_decode($request->request->get('linkedPanls'), true);
        $product = $this->productRepository->findOneBy([
            'name' => $productData['name']
        ]);
        if ($product) {
            return new JsonResponse('Ce produit existe déjà', Response::HTTP_CONFLICT);
        }

        $product = new Product();
        foreach ($linkedPanles as $linkedPanel) {
            $product->addPanel($this->panelRepsitory->find($linkedPanel['id']));
        }
        $product->setName($productData['name']);
        $product->setDescription($productData['description']);
        $product->setPrice($productData['price']);
        $product->setGridRows($productData['rows']);
        $product->setGridColumns($productData['columns']);
        $product->setKitDescription($productData['kit']);
        $product->setElectricPower($productData['electric_power']);
        $product->setElectricalInstallation($productData['electrical_installation']);
        $product->setElectricalAssembly($productData['electrical_assembly']);
        $product->setElectricalAssemblyType($productData['electrical_assembly_type']);
        $product->setHeatProduction($productData['heat_production']);
        $product->setExchangerNumber($productData['exchanger_number']);
        $product->setDomesticWaterHeating($productData['domestic_water_heating']);
        $product->setDomesticWaterHeatingWay($productData['domestic_water_heating_way']);
        $product->setThermalStorage($productData['thermal_storage']);
        $product->setSmartR($productData['smart_r']);
        $product->setProductImage($imageFullPath);
        $product->setPanelsData($linkedPanles);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);
    }

    /**
     * Edit a product.
     *
     * @Rest\Put("/api/products/{id}", name="updateProduct")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function editProduct($id, Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse(["Le type de media envoyé n'est pas supportée; il doit être de type application/json"], Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse('Ce produit n\'existe pas !', Response::HTTP_NOT_FOUND);
        }
        $imageFullPath = null;
        $picture_image = $request->files->get('picture_image');
        if ($picture_image) {
            $imageFullPath = $this->uploadFiles('/uploads/product/images/', 'product_image_directory', $request, $picture_image);
        }
        $productData = json_decode($request->request->get('productData'), true);
        $linkedPanles = json_decode($request->request->get('linkedPanls'), true);
        foreach ($linkedPanles as $linkedPanel) {
            $product->addPanel($this->panelRepsitory->find($linkedPanel['id']));
        }
        $product->setName($productData['name']);
        $product->setDescription($productData['description']);
        $product->setPrice($productData['price']);
        $product->setGridRows($productData['rows']);
        $product->setGridColumns($productData['columns']);
        $product->setKitDescription($productData['kit']);
        $product->setElectricPower($productData['electric_power']);
        $product->setElectricalInstallation($productData['electrical_installation']);
        $product->setElectricalAssembly($productData['electrical_assembly']);
        $product->setElectricalAssemblyType($productData['electrical_assembly_type']);
        $product->setHeatProduction($productData['heat_production']);
        $product->setExchangerNumber($productData['exchanger_number']);
        $product->setDomesticWaterHeating($productData['domestic_water_heating']);
        $product->setDomesticWaterHeatingWay($productData['domestic_water_heating_way']);
        $product->setThermalStorage($productData['thermal_storage']);
        $product->setSmartR($productData['smart_r']);
        if ($imageFullPath) {
            $product->setProductImage($imageFullPath);
        }
        $product->setPanelsData($linkedPanles);
        $this->entityManager->flush();
        $response = $this->serializer->serialize($productData, 'json', SerializationContext::create()->setGroups(array('product_details')));
        return JsonResponse::fromJsonString($response, Response::HTTP_OK);
    }

    /**
     * Delete a product.
     *
     * @Rest\Delete("/api/products/{id}", name="deleteProduct")
     * @param $id
     * @return JsonResponse
     */
    public function deleteProduct($id)
    {
        if (1 == count($this->getUser()->getRoles()) && 'ROLE_USER' == $this->getUser()->getRoles()[0]) {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }
        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse('Ce produit n\'existe pas !', Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    public function getDefaultImage(Request $request, $path)
    {
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        return $url . $path . 'product-default.png';
    }

    public function uploadFiles($path, $directory, Request $request, UploadedFile $file = null)
    {
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        if ($file) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = str_replace(' ', '-', $fileName);
            $safeFileName = preg_replace('/[^A-Za-z0-9\-]/', '', $fileName);
            $newFilename = 'mondevis-product-' . $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($this->getParameter($directory), $newFilename);
            } catch (Exception $e) {
                return new JsonResponse("Une exception a été produite lors du téléchargement de l'image " . $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }

            return $url . $path . $newFilename;
        }

        return $this->getDefaultImage($request, $path);
    }
}
