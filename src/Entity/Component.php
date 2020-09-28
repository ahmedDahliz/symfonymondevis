<?php

namespace App\Entity;

use App\Repository\ComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=ComponentRepository::class)
 */
class Component
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"components_details","components_product_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"components_details","components_product_details"})
     */
    private $manRef;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"components_details","components_product_details"})
     */
    private $rexelRef;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"components_details","components_product_details", "order_details"})
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @JmsGroups({"components_details","components_product_details", "order_details"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="text")
     * @JmsGroups({"components_details", "order_details"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"components_details","components_product_details", "order_details"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"components_details"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=AssociatedFile::class, mappedBy="component", orphanRemoval=true)
     * @JmsGroups({"components_details"})
     */
    private $files;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @JmsGroups({"components_details", "components_product_details"})
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     * @JmsGroups({"components_details"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, inversedBy="components")
     * @JmsGroups({"components_product_details"})
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity=Configurator::class, mappedBy="component")
     * @JmsGroups({"components_configurator_details"})
     *
     */
    private $configurators;

    /**
     * @ORM\ManyToMany(targetEntity=Order::class, mappedBy="componenets")
     * @JmsGroups({"components_order_details"})
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Ligncommand::class, mappedBy="component")
     * @JmsGroups({"components_order_details"})
     */
    private $ligncommands;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
        $this->files = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->configurators = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->ligncommands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getManRef(): ?string
    {
        return $this->manRef;
    }

    public function setManRef(string $manRef): self
    {
        $this->manRef = $manRef;

        return $this;
    }

    public function getRexelRef(): ?string
    {
        return $this->rexelRef;
    }

    public function setRexelRef(string $rexelRef): self
    {
        $this->rexelRef = $rexelRef;

        return $this;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|AssociatedFile[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(AssociatedFile $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setComponent($this);
        }

        return $this;
    }

    public function removeFile(AssociatedFile $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getComponent() === $this) {
                $file->setComponent(null);
            }
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
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }

    public function removeAllProducts(): self
    {
        $this->products->clear();
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
            $configurator->addComponent($this);
        }

        return $this;
    }

    public function removeConfigurator(Configurator $configurator): self
    {
        if ($this->configurators->contains($configurator)) {
            $this->configurators->removeElement($configurator);
            $configurator->removeComponent($this);
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->addComponenet($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->removeComponenet($this);
        }

        return $this;
    }

    /**
     * @return Collection|Ligncommand[]
     */
    public function getLigncommands(): Collection
    {
        return $this->ligncommands;
    }

    public function addLigncommand(Ligncommand $ligncommand): self
    {
        if (!$this->ligncommands->contains($ligncommand)) {
            $this->ligncommands[] = $ligncommand;
            $ligncommand->setComponent($this);
        }

        return $this;
    }

    public function removeLigncommand(Ligncommand $ligncommand): self
    {
        if ($this->ligncommands->contains($ligncommand)) {
            $this->ligncommands->removeElement($ligncommand);
            // set the owning side to null (unless already changed)
            if ($ligncommand->getComponent() === $this) {
                $ligncommand->setComponent(null);
            }
        }

        return $this;
    }
}
