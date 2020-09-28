<?php

namespace App\Entity;

use App\Repository\ChimenyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChimenyRepository::class)
 */
class Chimeny
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $heigth;

    /**
     * @ORM\ManyToOne(targetEntity=Area::class, inversedBy="chimenies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $area;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeigth(): ?float
    {
        return $this->heigth;
    }

    public function setHeigth(?float $heigth): self
    {
        $this->heigth = $heigth;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

        return $this;
    }
}
