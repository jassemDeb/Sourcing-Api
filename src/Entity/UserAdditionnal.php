<?php

namespace App\Entity;

use App\Repository\UserAdditionnalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserAdditionnalRepository::class)]
class UserAdditionnal implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\ManyToMany(targetEntity: CoreOrganization::class, mappedBy: 'user_additionnal')]
    private Collection $coreOrganizations;

    #[ORM\OneToMany(mappedBy: 'core_user_additionnal', targetEntity: DashboardConfiguration::class)]
    private Collection $dashboard_configuration;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $fullname = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $created_At = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updated_At = null;

    #[ORM\OneToOne(inversedBy: 'userAdditionnal', cascade: ['persist', 'remove'])]
    private ?User $user = null;







    public function __construct()
    {
        $this->coreOrganizations = new ArrayCollection();
        $this->dashboard_configuration = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, CoreOrganization>
     */
    public function getCoreOrganizations(): Collection
    {
        return $this->coreOrganizations;
    }

    public function addCoreOrganization(CoreOrganization $coreOrganization): static
    {
        if (!$this->coreOrganizations->contains($coreOrganization)) {
            $this->coreOrganizations->add($coreOrganization);
            $coreOrganization->addUserAdditionnalId($this);
        }

        return $this;
    }

    public function removeCoreOrganization(CoreOrganization $coreOrganization): static
    {
        if ($this->coreOrganizations->removeElement($coreOrganization)) {
            $coreOrganization->removeUserAdditionnalId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DashboardConfiguration>
     */
    public function getDashboardConfigurationId(): Collection
    {
        return $this->dashboard_configuration;
    }

    public function addDashboardConfigurationId(DashboardConfiguration $dashboardConfigurationId): static
    {
        if (!$this->dashboard_configuration->contains($dashboardConfigurationId)) {
            $this->dashboard_configuration->add($dashboardConfigurationId);
            $dashboardConfigurationId->setCoreUserAdditionnalId($this);
        }

        return $this;
    }

    public function removeDashboardConfigurationId(DashboardConfiguration $dashboardConfigurationId): static
    {
        if ($this->dashboard_configuration->removeElement($dashboardConfigurationId)) {
            // set the owning side to null (unless already changed)
            if ($dashboardConfigurationId->getCoreUserAdditionnalId() === $this) {
                $dashboardConfigurationId->setCoreUserAdditionnalId(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_At;
    }

    public function setCreatedAt(\DateTimeInterface $created_At): self
    {
        if ($created_At === null) {
            $this->created_At = new \DateTimeInterface();
        } else {
            $this->created_At = $created_At;
        }
        
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_At;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_At): self
    {
        if ($updated_At === null) {
            $this->updated_At = new \DateTimeInterface();
        } else {
            $this->updated_At = $updated_At;
        }
        
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }






}
