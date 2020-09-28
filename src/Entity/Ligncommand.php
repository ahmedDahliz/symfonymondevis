<?php

namespace App\Entity;

use App\Repository\LigncommandRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=LigncommandRepository::class)
 */
class Ligncommand
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"ligncommand_details", "order_details"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="ligncommands")
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"ligncommand_details"})
     */
    private $command;

    /**
     * @ORM\ManyToOne(targetEntity=Component::class, inversedBy="ligncommands")
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"ligncommand_details", "order_details"})
     */
    private $component;

    /**
     * @ORM\Column(type="integer")
     * @JmsGroups({"ligncommand_details", "order_details"})
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @JmsGroups({"ligncommand_details","order_details"})
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommand(): ?Order
    {
        return $this->command;
    }

    public function setCommand(?Order $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getComponent(): ?Component
    {
        return $this->component;
    }

    public function setComponent(?Component $component): self
    {
        $this->component = $component;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

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
}
