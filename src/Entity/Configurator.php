<?php

namespace App\Entity;

use App\Repository\ConfiguratorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;
/**
 * @ORM\Entity(repositoryClass=ConfiguratorRepository::class)
 */
class Configurator
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"project_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JmsGroups({"project_details"})
     */
    private $electricPower;


    /**
     * @ORM\ManyToOne(targetEntity=Gamme::class, inversedBy="configurators")
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"project_details"})
     */
    private $gamme;

    /**
     * @ORM\ManyToMany(targetEntity=Component::class, inversedBy="configurators")
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"project_details"})
     */
    private $component;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="configurators")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToMany(targetEntity=Panel::class, inversedBy="configurators")
     * @JmsGroups({"project_details"})
     */
    private $panels;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"project_details"})
     */
    private $gridChoice;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $solarFields;


    public function __construct()
    {
        $this->component = new ArrayCollection();
        $this->panels = new ArrayCollection();
    }


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


    public function getGamme(): ?Gamme
    {
        return $this->gamme;
    }

    public function setGamme(?Gamme $gamme): self
    {
        $this->gamme = $gamme;

        return $this;
    }

    

   

   


    /**
     * @return Collection|Component[]
     */
    public function getComponent(): Collection
    {
        return $this->component;
    }

    public function addComponent(Component $component): self
    {
        if (!$this->component->contains($component)) {
            $this->component[] = $component;
        }

        return $this;
    }

    public function removeComponent(Component $component): self
    {
        if ($this->component->contains($component)) {
            $this->component->removeElement($component);
        }

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

    public function getGridChoice(): ?string
    {
        return $this->gridChoice;
    }

    public function setGridChoice(?string $gridChoice): self
    {
        $this->gridChoice = $gridChoice;

        return $this;
    }

    public function getSolarFields(): ?string
    {
        return $this->solarFields;
    }

    public function setSolarFields(string $solarFields): self
    {
        $this->solarFields = $solarFields;

        return $this;
    }


   
}
