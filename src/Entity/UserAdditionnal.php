<?php

namespace App\Entity;

use App\Repository\UserAdditionnalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: UserAdditionnalRepository::class)]
class UserAdditionnal extends User
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
}
