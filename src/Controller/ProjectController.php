<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Area;
use App\Entity\Needs;
use App\Entity\Configurator;
use App\Entity\SalesQote;
use App\Entity\Gamme;
use App\Entity\Panel;
use App\Entity\Window;
use App\Entity\Chimeny;
use App\Entity\Component;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\AreaRepository;
use App\Repository\NeedsRepository;
use App\Repository\ConfiguratorRepository;
use App\Repository\SalesQoteRepository;
use App\Repository\GammeRepository;
use App\Repository\PanelRepository;
use App\Repository\WindowRepository;
use App\Repository\ChimenyRepository;
use App\Repository\ComponentRepository;
use App\Repository\UserRepository;
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

class ProjectController extends AbstractFOSRestController
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
     * @var AreaRepository
     */
    private $areaRepository;
    /**
     * @var NeedsRepository
     */
    private $needRepository;
    /**
     * @var ConfiguratorRepository
     */
    private $configuratorRepository;
    /**
     * @var SalesQoteController
     */
    private $salesQoteRepository;
    /**
     * @var GammeRepository
     */
    private $gammeRepository;
    /**
     * @var PanelRepository
     */
    private $pannelRepository;
    /**
     * @var WindowRepository
     */
    private $windowRepository;
    /**
     * @var ChimenyRepository
     */
    private $chimenyRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * ProjectController constructor.
     * @param SerializerInterface $serializer
     * @param ProjectRepository $projectRepository
     * @param AreaRepository $areaRepository
     * @param NeedsRepository $needRepository
     * @param ConfiguratorRepository $configuratorRepository
     * @param SalesQoteController $salesQoteRepository
     * @param WindowRepository $windowRepository
     * @param ChimenyRepository $chimenyRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, ProjectRepository $projectRepository,
                                AreaRepository $areaRepository, NeedsRepository $needRepository,
                                ConfiguratorRepository $configuratorRepository, SalesQoteRepository $salesQoteRepository,
                                GammeRepository $gammeRepository, PanelRepository $pannelRepository,
                                WindowRepository $windowRepository, ChimenyRepository $chimenyRepository,
                                ComponentRepository $componentRepository, UserRepository $userRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->projectRepository = $projectRepository;
        $this->areaRepository = $areaRepository;
        $this->needRepository = $needRepository;
        $this->configuratorRepository = $configuratorRepository;
        $this->salesQoteRepository = $salesQoteRepository;
        $this->gammeRepository = $gammeRepository;
        $this->pannelRepository = $pannelRepository;
        $this->windowRepository = $windowRepository;
        $this->chimenyRepository = $chimenyRepository;
        $this->componentRepository = $componentRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

    }


    /**
     * @Rest\Post("/api/projects", name="fos_createProjects")
     * @param Request $request
     * @return mixed|JsonResponse
     */
    public function createProject(Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }

        $projectData = json_decode($request->request->get('project'), true);
        $needsData = json_decode($request->request->get('needs'), true);
        $areasData = json_decode($request->request->get('areas'), true);
        $configuratorsData = json_decode($request->request->get('configurators'), true);
        $_configurators = json_decode($request->request->get('_configurators_data'), true);
        $_components = json_decode($request->request->get('_components_data'), true);
        $_areasData = json_decode($request->request->get('_areas_data'), true);
        $_areas = json_decode($request->request->get('_areas'), true);

        $project = $this->projectRepository->findOneBy([
            'title' => $projectData['title']
        ]);
        if (!is_null($project)) {
            return new JsonResponse('Ce project existe déjà', Response::HTTP_CONFLICT);
        }

        //add project-table
        $project = new Project();
        $project->setTitle(trim($projectData['title']));
        $project->setAdress(trim($projectData['adress']));
        $project->setCity(trim($projectData['city']));
        $project->setPostalCode(intval($projectData['postalcode']));
        $project->setPrice($projectData['price']);
        $project->setStatus("Nouveau");
        if ($projectData['client']) {
            $client = $this->userRepository->find($projectData['client']['id']);
            $project->setClient($client);
        }
        $createdby = $this->userRepository->findOneBy([
            'email' => $this->getUser()->getUsername()
        ]);
        $project->setUser($createdby);
        $project->setProjectData($projectData);
        $project->setAreas($_areas);
        $project->setAreasData($_areasData);
        $project->setNeedsData($needsData);
        $project->setComponentsData($_components);
        $project->setConfigurtorsData($_configurators);
        $this->entityManager->persist($project);

        //add areas-table
        foreach ($areasData as $area) {
            $projectArea = new Area();
            $projectArea->setTitle($area['title']);
            $projectArea->setCoating($area['coating']);
            $projectArea->setOrientation($area['orientation']);
            $projectArea->setWidth(floatval($area['width']));
            $projectArea->setHeigth(floatval($area['height']));

            //add Chimeny-table
            if (array_key_exists("chimneys", $area)) {
                foreach ($area['chimneys'] as $chimeny) {
                    $areaChimeny = new Chimeny();
                    $areaChimeny->setWidth(floatval($chimeny['width']));
                    $areaChimeny->setHeigth(floatval($chimeny['height']));
                    $areaChimeny->setArea($projectArea);
                    $this->entityManager->persist($areaChimeny);
                }
            }
            //add Window-table
            if (array_key_exists("windows", $area)) {
                foreach ($area['windows'] as $window) {
                    $areaWindow = new Window();
                    $areaWindow->setWidth(floatval($window['width']));
                    $areaWindow->setHeigth(floatval($window['height']));
                    $areaWindow->setArea($projectArea);
                    $this->entityManager->persist($areaWindow);
                }
            }
            $projectArea->setProject($project);
            $this->entityManager->persist($projectArea);
        }


        //add needs-table
        foreach ($needsData as $need) {
            $projectNeed = new Needs();
            $projectNeed->setElectricPower(floatval($need['need']['electric_power']));
            $projectNeed->setElectricSetup(trim($need['need']['electric_setup']));
            $projectNeed->setElectricCollection($need['need']['electric_collection']);
            $projectNeed->setCollectionType($need['need']['collection_type']);
            $projectNeed->setHeatingProduction($need['need']['heating_production']);
            $projectNeed->setHeatingNumberBouche(intval($need['need']['heating_number_bouche']));
            $projectNeed->setWaterHeating($need['need']['water_heating']);
            $projectNeed->setWaterHeatingWay($need['need']['water_heating_way']);
            $projectNeed->setThermicStorage($need['need']['thermic_storage']);
            $projectNeed->setSmartR($need['need']['smart_r']);
            $projectNeed->setProject($project);
            $this->entityManager->persist($projectNeed);
        }

        // add configuirator-table
        foreach ($configuratorsData as $configurator) {
            $projectConfigurator = new Configurator();
            $projectConfigurator->setElectricPower(floatval($configurator['electric_power']));
            $projectConfigurator->setSolarFields($configurator['solarFields']);
            $projectConfigurator->setGridChoice($configurator['gridChoice']);

            if ($configurator['range']) {
                $range = $this->gammeRepository->find($configurator['range']);
                $projectConfigurator->setGamme($range);
            }
            foreach ($configurator['panels'] as $configPanel) {
                $panel = $this->pannelRepository->find($configPanel['id']);
                for ($i = 0; $i < $configPanel['number']; $i++) {
                    $projectConfigurator->addPanel($panel);
                }
            }
            foreach ($configurator['components'] as $configComponent) {
                $component = $this->componentRepository->find($configComponent['id']);
                for ($i = 0; $i < $configComponent['number']; $i++) {
                    $projectConfigurator->addComponent($component);
                }
            }
            $projectConfigurator->setProject($project);
            $this->entityManager->persist($projectConfigurator);
        }

        $projectSalesQote = new SalesQote();
        $projectSalesQote->setPrice($projectData['price']);
        $projectSalesQote->setStatus("En traitement");
        $projectSalesQote->setProjet($project);
        $this->entityManager->persist($projectSalesQote);
        $this->entityManager->flush();
        $idCurrently = $projectSalesQote->getId();

        return new JsonResponse($idCurrently, Response::HTTP_OK);

    }


    /**
     * @Rest\Put("/api/projects/{id}", name="fos_updateProject")
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProject($id, Request $request)
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if (!$acceptHeader->has('application/json') && !$acceptHeader->has('application/ld-json')) {
            return new JsonResponse("Le type de media envoyer n'est pas supporter; il doit être de type application/json ", Response::HTTP_UNSUPPORTED_MEDIA_TYPE, ["Status Code" => "415 Unsupported Media Type"]);
        }
        $project = $this->projectRepository->find($id);
        if (!$project) {
            return new JsonResponse("Ce project n'existe pas !", Response::HTTP_NOT_FOUND);
        }

        //update project-table
        $projectData = json_decode($request->request->get('project'), true);
        $needsData = json_decode($request->request->get('needs'), true);
        $areasData = json_decode($request->request->get('areas'), true);
        $configuratorsData = json_decode($request->request->get('configurators'), true);
        $_configurators = json_decode($request->request->get('_configurators_data'), true);
        $_components = json_decode($request->request->get('_components_data'), true);
        $_areasData = json_decode($request->request->get('_areas_data'), true);
        $_areas = json_decode($request->request->get('_areas'), true);

        $project->setTitle(trim($projectData['title']));
        $project->setAdress($projectData['adress']);
        $project->setCity(trim($projectData['city']));
        $project->setPostalCode($projectData['postalcode']);
        $project->setPrice($projectData['price']);
        $project->setStatus("En traitement");
        if ($projectData['client']) {
            $client = $this->userRepository->find($projectData['client']['id']);
            $project->setClient($client);
        }
        $project->setProjectData($projectData);
        $project->setAreas($_areas);
        $project->setAreasData($_areasData);
        $project->setNeedsData($needsData);
        $project->setComponentsData($_components);
        $project->setConfigurtorsData($_configurators);

        foreach ($project->getAreas() as $areaToDel){
            $this->entityManager->remove($areaToDel);
        }

        //update area-tables
        foreach ($areasData as $area) {
            $projectArea = new Area();
            $projectArea->setTitle($area['title']);
            $projectArea->setCoating($area['coating']);
            $projectArea->setOrientation($area['orientation']);
            $projectArea->setWidth(floatval($area['width']));
            $projectArea->setHeigth(floatval($area['height']));
            $projectArea->setProject($project);
            $this->entityManager->persist($projectArea);

            //update Chimeny-table
            if (array_key_exists("chimneys", $area)) {
                foreach ($area['chimneys'] as $chimeny) {
                    $areaChimeny = new Chimeny();
                    $areaChimeny->setWidth(floatval($chimeny['width']));
                    $areaChimeny->setHeigth(floatval($chimeny['height']));
                    $areaChimeny->setArea($projectArea);
                    $this->entityManager->persist($areaChimeny);
                }
            }
            //update Window-table
            if (array_key_exists("windows", $area)) {
                foreach ($area['windows'] as $window) {
                    $areaWindow = new Window();
                    $areaWindow->setWidth(floatval($window['width']));
                    $areaWindow->setHeigth(floatval($window['height']));
                    $areaWindow->setArea($projectArea);
                    $this->entityManager->persist($areaWindow);
                }
            }
        }

        foreach ($project->getNeeds() as $needToDel){
            $this->entityManager->remove($needToDel);
        }
        //update need-table
        foreach ($needsData as $need) {
            $projectNeed = new Needs();
            $projectNeed->setElectricPower(floatval($need['need']['electric_power']));
            $projectNeed->setElectricSetup(trim($need['need']['electric_setup']));
            $projectNeed->setElectricCollection($need['need']['electric_collection']);
            $projectNeed->setCollectionType($need['need']['collection_type']);
            $projectNeed->setHeatingProduction($need['need']['heating_production']);
            $projectNeed->setHeatingNumberBouche(intval($need['need']['heating_number_bouche']));
            $projectNeed->setWaterHeating($need['need']['water_heating']);
            $projectNeed->setWaterHeatingWay($need['need']['water_heating_way']);
            $projectNeed->setThermicStorage($need['need']['thermic_storage']);
            $projectNeed->setSmartR($need['need']['smart_r']);
            $projectNeed->setProject($project);
            $this->entityManager->persist($projectNeed);
        }

        foreach ($project->getConfigurators() as $configToDel){
            $this->entityManager->remove($configToDel);
        }
        // update configuirator-table
        foreach ($configuratorsData as $configurator) {
            $projectConfigurator = new Configurator();
            $projectConfigurator->setElectricPower(floatval($configurator['electric_power']));
            $projectConfigurator->setSolarFields($configurator['solarFields']);
            $projectConfigurator->setGridChoice($configurator['gridChoice']);

            if ($configurator['range']) {
                $range = $this->gammeRepository->find($configurator['range']);
                $projectConfigurator->setGamme($range);
            }
            foreach ($configurator['panels'] as $configPanel) {
                $panel = $this->pannelRepository->find($configPanel['id']);
                for ($i = 0; $i < $configPanel['number']; $i++) {
                    $projectConfigurator->addPanel($panel);
                }
            }
            foreach ($configurator['components'] as $configComponent) {
                $component = $this->componentRepository->find($configComponent['id']);
                for ($i = 0; $i < $configComponent['number']; $i++) {
                    $projectConfigurator->addComponent($component);
                }
            }
            $projectConfigurator->setProject($project);
            $this->entityManager->persist($projectConfigurator);
        }


        $projectSalesQote = $this->salesQoteRepository->find($project->getSalesQotes()[0]->getId());
        $projectSalesQote->setPrice($projectData['price']);
        $this->entityManager->flush();

        return JsonResponse::fromJsonString($projectSalesQote->getId(), Response::HTTP_OK);
    }


    /**
     * @Rest\Get("/api/projects", name="fos_getProjects")
     *
     */
    public function getProjects()
    {
        switch ($this->getUser()->getRoles()[0]) {
            case 'ROLE_ADMIN':
                $projects = $this->projectRepository->findBy([
                    'user' => $this->getUser()
                ]);
                break;
            case 'ROLE_USER':
                $projects = $this->projectRepository->findBy([
                    'client' => $this->getUser()
                ]);
                break;
            case 'ROLE_SUPER_ADMIN':
                $projects = $this->projectRepository->findAll();
                // $projects =  $this->projectRepository->createQueryBuilder('p')->getQuery()->getResult();
                break;
        }
        $prjectsData = $this->serializer->serialize($projects, 'json', SerializationContext::create()->setGroups(array('project_details')));
        return JsonResponse::fromJsonString($prjectsData, Response::HTTP_OK);
    }


    private function groupByClient(array $projects)
    {
        return array_reduce($projects,
            function ($list, $project) {
                $list[$project->getClient()->getFirstName()][] = $project;
                return $list;
            }, []);
    }

    /**
     * @Rest\Get("/api/projects/byClient", name="fos_getProjectsByClient")
     * @return JsonResponse
     */
    public function getProjectsByClient(): JsonResponse
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $projects = $this->projectRepository->findBy([
                'user' => $this->getUser()
            ]);
        } else {
            $projects = $this->projectRepository->findAll();
        }
        $projectsByClient = $this->groupByClient($projects);
        //  $projectsData = $this->serializer->serialize($projectsByClient,'json');
        foreach ($projectsByClient as $client => $project) {

            $clientTab [] = $client;
        }
        return new JsonResponse($clientTab, Response::HTTP_OK);
    }


    /**
     * @Rest\Get("/api/projects/{id}", name="fos_getOneProjects")
     * @param $id
     * @return JsonResponse
     */
    public function getOneProjects($id): JsonResponse
    {
//        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
//
//        }
        $project = $this->projectRepository->find($id);
        if (!$project) {
            return new JsonResponse("Ce projet n'existe pas !", Response::HTTP_NOT_FOUND);
        }
        $projectData = $this->serializer->serialize($project, 'json', SerializationContext::create()->setGroups(array('project_details')));
        return JsonResponse::fromJsonString($projectData, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/api/projects/{id}", name="fos_deleteprojects")
     * @param $id
     * @return JsonResponse
     */
    public function deleteProjects($id): JsonResponse
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accéder à ce resource", Response::HTTP_FORBIDDEN);
        }
        $project = $this->projectRepository->find($id);
        if (!$project) {
            return new JsonResponse('Ce projet n\'existe pas !', Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($project);
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }


    /**
     * @Rest\Get("/api/salesQuotes", name="fos_getSalesQotes")
     * @return JsonResponse
     */
    public function getQuote(): JsonResponse
    {

        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }

        $salesQuotes = $this->salesQoteRepository->findAll();
        $devisData = $this->serializer->serialize($salesQuotes, 'json', SerializationContext::create()->setGroups(array('project_details')));
        return JsonResponse::fromJsonString($devisData, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/api/salesQuote/setFile/{id}", name="fos_setSalesQotesFile")
     * @param $id
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function setFileQuote($id, Request $request)
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }

        $salesQuote = $this->salesQoteRepository->find($id);
        $quoteFile = $request->files->get('quote');
        $quoteFullPath = $this->uploadFiles('/uploads/quote/', 'quote_files_directory', $request, $quoteFile);
        $salesQuote->setPath($quoteFullPath);
        $this->entityManager->flush();
        return JsonResponse::fromJsonString("", Response::HTTP_OK);
    }


    /**
     * @Rest\Put("/api/project/update/status/{id}", name="fos_updateStatus")
     * @param $id
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function updateProjectStatus($id, Request $request)
    {
        if (count($this->getUser()->getRoles()) == 1 && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return new JsonResponse("Vous n'avez pas le droit d'accedé à ce ressource", Response::HTTP_FORBIDDEN);
        }

        $projet = $this->projectRepository->find($id);
        $status = $request->getContent();
        $projet->setStatus($status);
        $this->entityManager->flush();
        return JsonResponse::fromJsonString("", Response::HTTP_OK);
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
        return "";
    }




}
