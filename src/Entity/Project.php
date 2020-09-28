<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"project_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details", "order_details"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     * @JmsGroups({"project_details"})
     */
    private $postalCode;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @JmsGroups({"project_details"})
     */
    private $price;


    /**
     * @ORM\Column(type="date")
     * @JmsGroups({"project_details"})
     */
    private $createdAt;


    /**
     * @ORM\OneToMany(targetEntity=SalesQote::class, mappedBy="projet", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"project_details"})
     */
    private $salesQotes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projects")
     * @JmsGroups({"project_details", "order_details"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="clientProjets")
     * @JmsGroups({"project_details", "order_details"})
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=Area::class, mappedBy="project", cascade={"remove"})
     * @JmsGroups({"project_details"})
     */
    private $areas;

    /**
     * @ORM\OneToMany(targetEntity=Configurator::class, mappedBy="project", orphanRemoval=true)
     */
    private $configurators;

    /**
     * @ORM\OneToMany(targetEntity=Needs::class, mappedBy="project", orphanRemoval=true)
     */
    private $needs;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $status;

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"project_details"})
     */
    private $_project_data = [];

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"project_details"})
     */
    private $_areas = [];

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"project_details"})
     */
    private $_areas_data = [];

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"project_details"})
     */
    private $_configurtors_data = [];

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"project_details"})
     */
    private $_needs_data = [];

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"project_details"})
     */
    private $_components_data = [];


    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
        $this->salesQotes = new ArrayCollection();
        $this->areas = new ArrayCollection();
        $this->configurators = new ArrayCollection();
        $this->needs = new ArrayCollection();
    }

    public function removeAllAreas(): self
    {
        $this->areas->clear();
        return $this;
    }

    public function removeAllNeeds(): self
    {
        $this->needs->clear();
        return $this;
    }

    public function removeAllConfigurators(): self
    {
        $this->configurators->clear();
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * @return Collection|SalesQote[]
     */
    public function getSalesQotes(): Collection
    {
        return $this->salesQotes;
    }

    public function addSalesQote(SalesQote $salesQote): self
    {
        if (!$this->salesQotes->contains($salesQote)) {
            $this->salesQotes[] = $salesQote;
            $salesQote->setProjet($this);
        }

        return $this;
    }

    public function removeSalesQote(SalesQote $salesQote): self
    {
        if ($this->salesQotes->contains($salesQote)) {
            $this->salesQotes->removeElement($salesQote);
            // set the owning side to null (unless already changed)
            if ($salesQote->getProjet() === $this) {
                $salesQote->setProjet(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Area[]
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function addArea(Area $area): self
    {
        if (!$this->areas->contains($area)) {
            $this->areas[] = $area;
            $area->setProject($this);
        }

        return $this;
    }

    public function removeArea(Area $area): self
    {
        if ($this->areas->contains($area)) {
            $this->areas->removeElement($area);
            // set the owning side to null (unless already changed)
            if ($area->getProject() === $this) {
                $area->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Configurator[]
     */
    public function getConfigurators(): Collection
    {
        return $this->configurators;
    }

    public function addConfigurator(Configurator $configurator): self
    {
        if (!$this->configurators->contains($configurator)) {
            $this->configurators[] = $configurator;
            $configurator->setProject($this);
        }

        return $this;
    }

    public function removeConfigurator(Configurator $configurator): self
    {
        if ($this->configurators->contains($configurator)) {
            $this->configurators->removeElement($configurator);
            // set the owning side to null (unless already changed)
            if ($configurator->getProject() === $this) {
                $configurator->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Needs[]
     */
    public function getNeeds(): Collection
    {
        return $this->needs;
    }

    public function addNeed(Needs $need): self
    {
        if (!$this->needs->contains($need)) {
            $this->needs[] = $need;
            $need->setProject($this);
        }

        return $this;
    }

    public function removeNeed(Needs $need): self
    {
        if ($this->needs->contains($need)) {
            $this->needs->removeElement($need);
            // set the owning side to null (unless already changed)
            if ($need->getProject() === $this) {
                $need->setProject(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProjectData(): ?array
    {
        return $this->_project_data;
    }

    public function setProjectData(array $_project_data): self
    {
        $this->_project_data = $_project_data;

        return $this;
    }

    public function setAreas(array $_areas): self
    {
        $this->_areas = $_areas;

        return $this;
    }

    public function getAreasData(): ?array
    {
        return $this->_areas_data;
    }

    public function setAreasData(array $_areas_data): self
    {
        $this->_areas_data = $_areas_data;

        return $this;
    }

    public function getConfigurtorsData(): ?array
    {
        return $this->_configurtors_data;
    }

    public function setConfigurtorsData(array $_configurtors_data): self
    {
        $this->_configurtors_data = $_configurtors_data;

        return $this;
    }

    public function getNeedsData(): ?array
    {
        return $this->_needs_data;
    }

    public function setNeedsData(array $_needs_data): self
    {
        $this->_needs_data = $_needs_data;

        return $this;
    }

    public function getComponentsData(): ?array
    {
        return $this->_components_data;
    }

    public function setComponentsData(array $_components_data): self
    {
        $this->_components_data = $_components_data;

        return $this;
    }


}
