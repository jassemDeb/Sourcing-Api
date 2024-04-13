<?php

namespace App\Entity;

use App\Repository\DashboardConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardConfigurationRepository::class)]
class DashboardConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $is_default = null;

    #[ORM\ManyToOne(inversedBy: 'dashboard_configuration')]
    private ?UserAdditionnal $core_user_additionnal = null;

    #[ORM\ManyToOne(inversedBy: 'dashboard_configuration')]
    private ?CoreOrganization $core_organization = null;

    #[ORM\OneToMany(mappedBy: 'core_dashboard_configuration', targetEntity: DashboardConfigurationWidget::class)]
    private Collection $core_dashboard_configuration_widget;

    #[ORM\ManyToOne(inversedBy: 'dashboardConfigurations')]
    private ?CoreOrganizationType $core_organization_type = null;

    public function __construct()
    {
        $this->core_dashboard_configuration_widget = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsDefault(): ?bool
    {
        return $this->is_default;
    }

    public function setIsDefault(bool $is_default): static
    {
        $this->is_default = $is_default;

        return $this;
    }

    public function getCoreUserAdditionnalId(): ?UserAdditionnal
    {
        return $this->core_user_additionnal;
    }

    public function setCoreUserAdditionnalId(?UserAdditionnal $core_user_additionnal): static
    {
        $this->core_user_additionnal = $core_user_additionnal;

        return $this;
    }

    public function getCoreOrganizationId(): ?CoreOrganization
    {
        return $this->core_organization;
    }

    public function setCoreOrganizationId(?CoreOrganization $core_organization): static
    {
        $this->core_organization = $core_organization;

        return $this;
    }

    /**
     * @return Collection<int, DashboardConfigurationWidget>
     */
    public function getCoreDashboardConfigurationWidgetId(): Collection
    {
        return $this->core_dashboard_configuration_widget;
    }

    public function addCoreDashboardConfigurationWidgetId(DashboardConfigurationWidget $coreDashboardConfigurationWidgetId): static
    {
        if (!$this->core_dashboard_configuration_widget->contains($coreDashboardConfigurationWidgetId)) {
            $this->core_dashboard_configuration_widget->add($coreDashboardConfigurationWidgetId);
            $coreDashboardConfigurationWidgetId->setCoreDashboardConfigurationId($this);
        }

        return $this;
    }

    public function removeCoreDashboardConfigurationWidgetId(DashboardConfigurationWidget $coreDashboardConfigurationWidgetId): static
    {
        if ($this->core_dashboard_configuration_widget->removeElement($coreDashboardConfigurationWidgetId)) {
            // set the owning side to null (unless already changed)
            if ($coreDashboardConfigurationWidgetId->getCoreDashboardConfigurationId() === $this) {
                $coreDashboardConfigurationWidgetId->setCoreDashboardConfigurationId(null);
            }
        }

        return $this;
    }

    public function getCoreOrganizationType(): ?CoreOrganizationType
    {
        return $this->core_organization_type;
    }

    public function setCoreOrganizationType(?CoreOrganizationType $core_organization_type): static
    {
        $this->core_organization_type = $core_organization_type;

        return $this;
    }
}
