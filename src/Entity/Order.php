<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;


/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"order_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"order_details"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"order_details"})
     */
    private $status;

    /**
     * @ORM\Column(type="date")
     * @JmsGroups({"order_details"})
     */
    private $createAt;

    /**
     * @ORM\OneToOne(targetEntity=SalesQote::class, inversedBy="commandeDevis", cascade={"persist", "remove"})
     * @JmsGroups({"order_details"})
     */
    private $salseQuot;

    /**
     * @ORM\ManyToMany(targetEntity=Component::class, inversedBy="orders")
     * @JmsGroups({"order_details"})
     */
    private $componenets;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @JmsGroups({"order_details"})
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=Ligncommand::class, mappedBy="command", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     * @JmsGroups({"order_details"})
     */
    private $ligncommands;

    public function __construct()
    {
        $this->setCreateAt(new \DateTime('now'));
        $this->componenets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getSalseQuot(): ?SalesQote
    {
        return $this->salseQuot;
    }

    public function setSalseQuot(?SalesQote $salseQuot): self
    {
        $this->salseQuot = $salseQuot;

        return $this;
    }

    /**
     * @return Collection|Component[]
     */
    public function getComponenets(): Collection
    {
        return $this->componenets;
    }

    public function addComponenet(Component $componenet): self
    {
        if (!$this->componenets->contains($componenet)) {
            $this->componenets[] = $componenet;
        }

        return $this;
    }

    public function removeComponenet(Component $componenet): self
    {
        if ($this->componenets->contains($componenet)) {
            $this->componenets->removeElement($componenet);
        }

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
            $ligncommand->setCommand($this);
        }

        return $this;
    }

    public function removeLigncommand(Ligncommand $ligncommand): self
    {
        if ($this->ligncommands->contains($ligncommand)) {
            $this->ligncommands->removeElement($ligncommand);
            // set the owning side to null (unless already changed)
            if ($ligncommand->getCommand() === $this) {
                $ligncommand->setCommand(null);
            }
        }

        return $this;
    }
}
