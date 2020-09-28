<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=AreaRepository::class)
 */
class Area
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
     */
    private $coating;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $orientation;

    /**
     * @ORM\Column(type="float")
     * @JmsGroups({"project_details"})
     */
    private $width;

    /**
     * @ORM\Column(type="float")
     * @JmsGroups({"project_details"})
     */
    private $heigth;

    /**
     * @ORM\Column(type="datetime")
     * @JmsGroups({"project_details"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Chimeny::class, mappedBy="area", orphanRemoval=true)
     * @JmsGroups({"project_details"})
     */
    private $chimenies;

    /**
     * @ORM\OneToMany(targetEntity=Window::class, mappedBy="area", orphanRemoval=true)
     * @JmsGroups({"project_details"})
     */
    private $windows;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="areas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"project_details"})
     */
    private $title;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
      
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoating(): ?string
    {
        return $this->coating;
    }

    public function setCoating(string $coating): self
    {
        $this->coating = $coating;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeigth(): ?float
    {
        return $this->heigth;
    }

    public function setHeigth(float $heigth): self
    {
        $this->heigth = $heigth;

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
     * @return Collection|Chimeny[]
     */
    public function getChimenies(): Collection
    {
        return $this->chimenies;
    }

    public function addChimeny(Chimeny $chimeny): self
    {
        if (!$this->chimenies->contains($chimeny)) {
            $this->chimenies[] = $chimeny;
            $chimeny->setArea($this);
        }

        return $this;
    }

    public function removeChimeny(Chimeny $chimeny): self
    {
        if ($this->chimenies->contains($chimeny)) {
            $this->chimenies->removeElement($chimeny);
            // set the owning side to null (unless already changed)
            if ($chimeny->getArea() === $this) {
                $chimeny->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Window[]
     */
    public function getWindows(): Collection
    {
        return $this->windows;
    }

    public function addWindow(Window $window): self
    {
        if (!$this->windows->contains($window)) {
            $this->windows[] = $window;
            $window->setArea($this);
        }

        return $this;
    }

    public function removeWindow(Window $window): self
    {
        if ($this->windows->contains($window)) {
            $this->windows->removeElement($window);
            // set the owning side to null (unless already changed)
            if ($window->getArea() === $this) {
                $window->setArea(null);
            }
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

}
