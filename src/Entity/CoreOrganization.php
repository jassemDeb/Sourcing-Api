<?php

namespace App\Entity;

use App\Repository\CoreOrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoreOrganizationRepository::class)]
class CoreOrganization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: UserAdditionnal::class, inversedBy: 'coreOrganizations')]
    private Collection $user_additionnal;

    #[ORM\ManyToMany(targetEntity: CoreOrganizationType::class, mappedBy: 'core_organization')]
    private Collection $coreOrganizationTypes;

    #[ORM\OneToMany(mappedBy: 'core_organization', targetEntity: DashboardConfiguration::class)]
    private Collection $dashboard_configuration;

    #[ORM\OneToMany(mappedBy: 'core_organization', targetEntity: DashboardWidget::class)]
    private Collection $dashboardWidgets;

    public function __construct()
    {
        $this->user_additionnal = new ArrayCollection();
        $this->coreOrganizationTypes = new ArrayCollection();
        $this->dashboard_configuration = new ArrayCollection();
        $this->dashboardWidgets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, UserAdditionnal>
     */
    public function getUserAdditionnalId(): Collection
    {
        return $this->user_additionnal_id;
    }

    public function addUserAdditionnalId(UserAdditionnal $userAdditionnalId): static
    {
        if (!$this->user_additionnal_id->contains($userAdditionnalId)) {
            $this->user_additionnal_id->add($userAdditionnalId);
        }

        return $this;
    }

    public function removeUserAdditionnalId(UserAdditionnal $userAdditionnalId): static
    {
        $this->user_additionnal_id->removeElement($userAdditionnalId);

        return $this;
    }

    /**
     * @return Collection<int, CoreOrganizationType>
     */
    public function getCoreOrganizationTypes(): Collection
    {
        return $this->coreOrganizationTypes;
    }

    public function addCoreOrganizationType(CoreOrganizationType $coreOrganizationType): static
    {
        if (!$this->coreOrganizationTypes->contains($coreOrganizationType)) {
            $this->coreOrganizationTypes->add($coreOrganizationType);
            $coreOrganizationType->addCoreOrganizationId($this);
        }

        return $this;
    }

    public function removeCoreOrganizationType(CoreOrganizationType $coreOrganizationType): static
    {
        if ($this->coreOrganizationTypes->removeElement($coreOrganizationType)) {
            $coreOrganizationType->removeCoreOrganizationId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DashboardConfiguration>
     */
    public function getDashboardConfigurationId(): Collection
    {
        return $this->dashboard_configuration_id;
    }

    public function addDashboardConfigurationId(DashboardConfiguration $dashboardConfigurationId): static
    {
        if (!$this->dashboard_configuration_id->contains($dashboardConfigurationId)) {
            $this->dashboard_configuration_id->add($dashboardConfigurationId);
            $dashboardConfigurationId->setCoreOrganizationId($this);
        }

        return $this;
    }

    public function removeDashboardConfigurationId(DashboardConfiguration $dashboardConfigurationId): static
    {
        if ($this->dashboard_configuration_id->removeElement($dashboardConfigurationId)) {
            // set the owning side to null (unless already changed)
            if ($dashboardConfigurationId->getCoreOrganizationId() === $this) {
                $dashboardConfigurationId->setCoreOrganizationId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DashboardWidget>
     */
    public function getDashboardWidgets(): Collection
    {
        return $this->dashboardWidgets;
    }

    public function addDashboardWidget(DashboardWidget $dashboardWidget): static
    {
        if (!$this->dashboardWidgets->contains($dashboardWidget)) {
            $this->dashboardWidgets->add($dashboardWidget);
            $dashboardWidget->setCoreOrganization($this);
        }

        return $this;
    }

    public function removeDashboardWidget(DashboardWidget $dashboardWidget): static
    {
        if ($this->dashboardWidgets->removeElement($dashboardWidget)) {
            // set the owning side to null (unless already changed)
            if ($dashboardWidget->getCoreOrganization() === $this) {
                $dashboardWidget->setCoreOrganization(null);
            }
        }

        return $this;
    }
}
