<?php

namespace App\Entity;

use App\Repository\PanelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=PanelRepository::class)
 */
class Panel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"panels_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @JmsGroups({"panels_details"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"panels_details"})
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Configurator::class, mappedBy="panels")
     * @JmsGroups({"panels_configurator_details"})
     */
    private $configurators;


    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="panels")
     * @JmsGroups({"panels_product_details"})
     */
    private $products;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @JmsGroups({"panels_details"})
     */
    private $price;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->configurators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addPanel($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removePanel($this);
        }

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
            $configurator->addPanel($this);
        }

        return $this;
    }

    public function removeConfigurator(Configurator $configurator): self
    {
        if ($this->configurators->contains($configurator)) {
            $this->configurators->removeElement($configurator);
            $configurator->removePanel($this);
        }

        return $this;
    }
}
