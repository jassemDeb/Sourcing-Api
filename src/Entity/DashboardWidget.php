<?php

namespace App\Entity;

use App\Repository\DashboardWidgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardWidgetRepository::class)]
class DashboardWidget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $is_default = null;

    #[ORM\Column(length: 255)]
    private ?string $description_en = null;

    #[ORM\Column(length: 255)]
    private ?string $description_fr = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_url = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_type = null;

    #[ORM\Column(length: 255)]
    private ?string $transaction_type = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_visibility = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_conditions = null;

    #[ORM\ManyToOne(inversedBy: 'dashboardWidgets')]
    private ?CoreOrganizationType $core_organization_type = null;

    #[ORM\OneToMany(mappedBy: 'dashboard_widget', targetEntity: DashboardConfigurationWidget::class)]
    private Collection $dashboard_configuration_widget;

    #[ORM\ManyToOne(inversedBy: 'dashboardWidgets')]
    private ?CoreOrganization $core_organization = null;

    public function __construct()
    {
        $this->dashboard_configuration_widget = new ArrayCollection();
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

    public function getDescriptionEn(): ?string
    {
        return $this->description_en;
    }

    public function setDescriptionEn(string $description_en): static
    {
        $this->description_en = $description_en;

        return $this;
    }

    public function getDescriptionFr(): ?string
    {
        return $this->description_fr;
    }

    public function setDescriptionFr(string $description_fr): static
    {
        $this->description_fr = $description_fr;

        return $this;
    }

    public function getWidgetUrl(): ?string
    {
        return $this->widget_url;
    }

    public function setWidgetUrl(string $widget_url): static
    {
        $this->widget_url = $widget_url;

        return $this;
    }

    public function getWidgetType(): ?string
    {
        return $this->widget_type;
    }

    public function setWidgetType(string $widget_type): static
    {
        $this->widget_type = $widget_type;

        return $this;
    }

    public function getTransactionType(): ?string
    {
        return $this->transaction_type;
    }

    public function setTransactionType(string $transaction_type): static
    {
        $this->transaction_type = $transaction_type;

        return $this;
    }

    public function getWidgetVisibility(): ?string
    {
        return $this->widget_visibility;
    }

    public function setWidgetVisibility(string $widget_visibility): static
    {
        $this->widget_visibility = $widget_visibility;

        return $this;
    }

    public function getWidgetConditions(): ?string
    {
        return $this->widget_conditions;
    }

    public function setWidgetConditions(string $widget_conditions): static
    {
        $this->widget_conditions = $widget_conditions;

        return $this;
    }

    public function getCoreOrganizationTypeId(): ?CoreOrganizationType
    {
        return $this->core_organization_type;
    }

    public function setCoreOrganizationTypeId(?CoreOrganizationType $core_organization_type): static
    {
        $this->core_organization_type = $core_organization_type;

        return $this;
    }

    /**
     * @return Collection<int, DashboardConfigurationWidget>
     */
    public function getDashboardConfigurationWidgetId(): Collection
    {
        return $this->dashboard_configuration_widget;
    }

    public function addDashboardConfigurationWidgetId(DashboardConfigurationWidget $dashboardConfigurationWidgetId): static
    {
        if (!$this->dashboard_configuration_widget->contains($dashboardConfigurationWidgetId)) {
            $this->dashboard_configuration_widget->add($dashboardConfigurationWidgetId);
            $dashboardConfigurationWidgetId->setDashboardWidgetId($this);
        }

        return $this;
    }

    public function removeDashboardConfigurationWidgetId(DashboardConfigurationWidget $dashboardConfigurationWidgetId): static
    {
        if ($this->dashboard_configuration_widget->removeElement($dashboardConfigurationWidgetId)) {
            // set the owning side to null (unless already changed)
            if ($dashboardConfigurationWidgetId->getDashboardWidgetId() === $this) {
                $dashboardConfigurationWidgetId->setDashboardWidgetId(null);
            }
        }

        return $this;
    }

    public function getCoreOrganization(): ?CoreOrganization
    {
        return $this->core_organization;
    }

    public function setCoreOrganization(?CoreOrganization $core_organization): static
    {
        $this->core_organization = $core_organization;

        return $this;
    }
}
