<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GammeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;
/**
 * @ORM\Entity(repositoryClass=GammeRepository::class)
 */
class Gamme
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"range_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JmsGroups({"range_details"})
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Configurator::class, mappedBy="gamme", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     * @JmsGroups({"range_configurator_details"})
     */
    private $configurators;

    public function __construct()
    {
        $this->configurators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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
            $configurator->setGamme($this);
        }

        return $this;
    }

    public function removeConfigurator(Configurator $configurator): self
    {
        if ($this->configurators->contains($configurator)) {
            $this->configurators->removeElement($configurator);
            // set the owning side to null (unless already changed)
            if ($configurator->getGamme() === $this) {
                $configurator->setGamme(null);
            }
        }

        return $this;
    }

   
}
