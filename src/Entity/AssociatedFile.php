<?php

namespace App\Entity;

use App\Repository\AssociatedFileRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass=AssociatedFileRepository::class)
 */
class AssociatedFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JmsGroups({"components_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"components_details"})
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"components_details"})
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Component::class, inversedBy="file")
     * @ORM\JoinColumn(nullable=false)
     */
    private $component;

    /**
     * @ORM\Column(type="datetime")
     * @JmsGroups({"components_details"})
     */
    private $createdAt;

    /**
     * AssociatedFile constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
