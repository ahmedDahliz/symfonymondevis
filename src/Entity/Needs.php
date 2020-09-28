<?php

namespace App\Entity;

use App\Repository\NeedsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NeedsRepository::class)
 */
class Needs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $electricPower;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $electricSetup;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $electricCollection;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $collectionType;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $heatingProduction;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $heatingNumberBouche;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $waterHeating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $waterHeatingWay;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $thermicStorage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $smart_r;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="needs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getElectricPower(): ?float
    {
        return $this->electricPower;
    }

    public function setElectricPower(float $electricPower): self
    {
        $this->electricPower = $electricPower;

        return $this;
    }

    public function getElectricSetup(): ?string
    {
        return $this->electricSetup;
    }

    public function setElectricSetup(string $electricSetup): self
    {
        $this->electricSetup = $electricSetup;

        return $this;
    }

    public function getElectricCollection(): ?bool
    {
        return $this->electricCollection;
    }

    public function setElectricCollection(?bool $electricCollection): self
    {
        $this->electricCollection = $electricCollection;

        return $this;
    }

    public function getCollectionType(): ?string
    {
        return $this->collectionType;
    }

    public function setCollectionType(?string $collectionType): self
    {
        $this->collectionType = $collectionType;

        return $this;
    }

    public function getHeatingProduction(): ?bool
    {
        return $this->heatingProduction;
    }

    public function setHeatingProduction(?bool $heatingProduction): self
    {
        $this->heatingProduction = $heatingProduction;

        return $this;
    }

    public function getHeatingNumberBouche(): ?int
    {
        return $this->heatingNumberBouche;
    }

    public function setHeatingNumberBouche(?int $heatingNumberBouche): self
    {
        $this->heatingNumberBouche = $heatingNumberBouche;

        return $this;
    }

    public function getWaterHeating(): ?bool
    {
        return $this->waterHeating;
    }

    public function setWaterHeating(?bool $waterHeating): self
    {
        $this->waterHeating = $waterHeating;

        return $this;
    }

    public function getWaterHeatingWay(): ?string
    {
        return $this->waterHeatingWay;
    }

    public function setWaterHeatingWay(?string $waterHeatingWay): self
    {
        $this->waterHeatingWay = $waterHeatingWay;

        return $this;
    }

    public function getThermicStorage(): ?bool
    {
        return $this->thermicStorage;
    }

    public function setThermicStorage(?bool $thermicStorage): self
    {
        $this->thermicStorage = $thermicStorage;

        return $this;
    }

    public function getSmartR(): ?bool
    {
        return $this->smart_r;
    }

    public function setSmartR(?bool $smart_r): self
    {
        $this->smart_r = $smart_r;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

   
}
