<?php

namespace App\Entity;

use App\Repository\SalesQoteRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=SalesQoteRepository::class)
 */
class SalesQote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"project_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=50, scale=2)
     * @JmsGroups({"project_details", "order_details"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     * @JmsGroups({"project_details"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="salesQotes")
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"order_details"})
     */
    private $projet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"project_details"})
     */
    private $path;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, mappedBy="salseQuot", cascade={"persist", "remove"})
     * @JmsGroups({"project_details"})
     */
    private $commandeDevis;

    /**
     * SalesQote constructor.
     */
    public function __construct()
    {
        $this->setDate(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getProjet(): ?Project
    {
        return $this->projet;
    }

    public function setProjet(?Project $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getCommandeDevis(): ?Order
    {
        return $this->commandeDevis;
    }

    public function setCommandeDevis(?Order $commandeDevis): self
    {
        $this->commandeDevis = $commandeDevis;

        // set (or unset) the owning side of the relation if necessary
        $newSalseQuot = null === $commandeDevis ? null : $this;
        if ($commandeDevis->getSalseQuot() !== $newSalseQuot) {
            $commandeDevis->setSalseQuot($newSalseQuot);
        }

        return $this;
    }
}
