<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"product_details", "components_product_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"product_details", "components_product_details"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @JmsGroups({"product_details", "components_product_details"})
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @JmsGroups({"product_details", "components_product_details"})
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @JmsGroups({"product_details"})
     */
    private $electricPower;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"product_details"})
     */
    private $electricalInstallation;

    /**
     * @ORM\Column(type="boolean")
     * @JmsGroups({"product_details"})
     */
    private $electricalAssembly;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"product_details"})
     */
    private $electricalAssemblyType;

    /**
     * @ORM\Column(type="boolean")
     * @JmsGroups({"product_details"})
     */
    private $heatProduction;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JmsGroups({"product_details"})
     */
    private $exchangerNumber;

    /**
     * @ORM\Column(type="boolean")
     * @JmsGroups({"product_details"})
     */
    private $domesticWaterHeating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"product_details"})
     */
    private $domesticWaterHeatingWay;

    /**
     * @ORM\Column(type="boolean")
     * @JmsGroups({"product_details"})
     */
    private $thermalStorage;

    /**
     * @ORM\Column(type="boolean")
     * @JmsGroups({"product_details"})
     */
    private $smartR;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"product_details"})
     */
    private $productImage;

    /**
     * @ORM\ManyToMany(targetEntity=Component::class, mappedBy="products")
     * @JmsGroups({"components_product_details"})
     */
    private $components;

    /**
     * @ORM\ManyToMany(targetEntity=Panel::class, inversedBy="products")
     * @JmsGroups({"panels_product_details"})
     */
    private $panels;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JmsGroups({"product_details"})
     */
    private $gridRows;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JmsGroups({"product_details"})
     */
    private $gridColumns;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"product_details"})
     */
    private $kitDescription;

    /**
     * @ORM\Column(type="array")
     * @JmsGroups({"product_details"})
     */
    private $_panelsData = [];


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->components = new ArrayCollection();
        $this->panels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getElectricPower(): ?int
    {
        return $this->electricPower;
    }

    public function setElectricPower(?int $electricPower): self
    {
        $this->electricPower = $electricPower;

        return $this;
    }

    public function getElectricalInstallation(): ?string
    {
        return $this->electricalInstallation;
    }

    public function setElectricalInstallation(string $electricalInstallation): self
    {
        $this->electricalInstallation = $electricalInstallation;

        return $this;
    }

    public function getElectricalAssembly(): ?bool
    {
        return $this->electricalAssembly;
    }

    public function setElectricalAssembly(bool $electricalAssembly): self
    {
        $this->electricalAssembly = $electricalAssembly;

        return $this;
    }

    public function getElectricalAssemblyType(): ?string
    {
        return $this->electricalAssemblyType;
    }

    public function setElectricalAssemblyType(?string $electricalAssemblyType): self
    {
        $this->electricalAssemblyType = $electricalAssemblyType;

        return $this;
    }

    public function getHeatProduction(): ?bool
    {
        return $this->heatProduction;
    }

    public function setHeatProduction(bool $heatProduction): self
    {
        $this->heatProduction = $heatProduction;

        return $this;
    }

    public function getExchangerNumber(): ?int
    {
        return $this->exchangerNumber;
    }

    public function setExchangerNumber(int $exchangerNumber): self
    {
        $this->exchangerNumber = $exchangerNumber;

        return $this;
    }

    public function getDomesticWaterHeating(): ?bool
    {
        return $this->domesticWaterHeating;
    }

    public function setDomesticWaterHeating(bool $domesticWaterHeating): self
    {
        $this->domesticWaterHeating = $domesticWaterHeating;

        return $this;
    }

    public function getDomesticWaterHeatingWay(): ?string
    {
        return $this->domesticWaterHeatingWay;
    }

    public function setDomesticWaterHeatingWay(?string $domesticWaterHeatingWay): self
    {
        $this->domesticWaterHeatingWay = $domesticWaterHeatingWay;

        return $this;
    }

    public function getThermalStorage(): ?bool
    {
        return $this->thermalStorage;
    }

    public function setThermalStorage(bool $thermalStorage): self
    {
        $this->thermalStorage = $thermalStorage;

        return $this;
    }

    public function getSmartR(): ?bool
    {
        return $this->smartR;
    }

    public function setSmartR(bool $smartR): self
    {
        $this->smartR = $smartR;

        return $this;
    }

    public function getProductImage(): ?string
    {
        return $this->productImage;
    }

    public function setProductImage(string $productImage): self
    {
        $this->productImage = $productImage;

        return $this;
    }

    /**
     * @return Collection|Component[]
     */
    public function getComponents(): Collection
    {
        return $this->components;
    }

    public function addComponent(Component $component): self
    {
        if (!$this->components->contains($component)) {
            $this->components[] = $component;
            $component->addProduct($this);
        }

        return $this;
    }

    public function removeComponent(Component $component): self
    {
        if ($this->components->contains($component)) {
            $this->components->removeElement($component);
            $component->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection|Panel[]
     */
    public function getPanels(): Collection
    {
        return $this->panels;
    }

    public function addPanel(Panel $panel): self
    {
        if (!$this->panels->contains($panel)) {
            $this->panels[] = $panel;
        }

        return $this;
    }

    public function removePanel(Panel $panel): self
    {
        if ($this->panels->contains($panel)) {
            $this->panels->removeElement($panel);
        }

        return $this;
    }

    public function getGridRows(): ?int
    {
        return $this->gridRows;
    }

    public function setGridRows(?int $gridRows): self
    {
        $this->gridRows = $gridRows;

        return $this;
    }

    public function getGridColumns(): ?int
    {
        return $this->gridColumns;
    }

    public function setGridColumns(?int $gridColumns): self
    {
        $this->gridColumns = $gridColumns;

        return $this;
    }

    public function getKitDescription(): ?string
    {
        return $this->kitDescription;
    }

    public function setKitDescription(string $kitDescription): self
    {
        $this->kitDescription = $kitDescription;

        return $this;
    }

    public function getPanelsData(): ?array
    {
        return $this->_panelsData;
    }

    public function setPanelsData(array $_panelsData): self
    {
        $this->_panelsData = $_panelsData;

        return $this;
    }

}