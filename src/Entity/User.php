<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use  JMS\Serializer\Annotation\Groups as JmsGroups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"public"})
     * @JmsGroups({"user_details", "project_details", "order_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"user_details", "project_details", "order_details"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"user_details", "project_details", "order_details"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"public"})
     * @JmsGroups({"user_details", "project_details", "order_details"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @JmsGroups({"user_details", "project_details"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @JmsGroups({"user_details"})
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @JmsGroups({"user_details", "project_details"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="users")
     * @JmsGroups({"user_details"})
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="createdBy")
     * @JmsGroups({"user_details"})
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     * @JmsGroups({"user_details"})
     */
    private $avatarPath;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="user")
     * @JmsGroups({"project_details"})
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="client")
     * @JmsGroups({"project_details"})
     */
    private $clientProjets;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="client")
     */
    private $orders;


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->clientProjets = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }


    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCreatedBy($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCreatedBy() === $this) {
                $user->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getClientProjets(): Collection
    {
        return $this->clientProjets;
    }

    public function addClientProjet(Project $clientProjet): self
    {
        if (!$this->clientProjets->contains($clientProjet)) {
            $this->clientProjets[] = $clientProjet;
            $clientProjets->setClient($this);
        }

        return $this;
    }

    public function removeClientProjet(Project $clientProjet): self
    {
        if ($this->clientProjets->contains($clientProjet)) {
            $this->clientProjets->removeElement($clientProjet);
            // set the owning side to null (unless already changed)
            if ($clientProjet->getClient() === $this) {
                $clientProjet->setClient(null);
            }
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
            $order->setClient($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getClient() === $this) {
                $order->setClient(null);
            }
        }

        return $this;
    }


}
