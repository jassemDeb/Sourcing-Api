<?php

namespace App\Entity;

use App\Repository\DashboardConfigurationWidgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardConfigurationWidgetRepository::class)]
class DashboardConfigurationWidget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_fr = null;

    #[ORM\Column(length: 255)]
    private ?string $name_en = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_style = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_width = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_height = null;

    #[ORM\Column(length: 255)]
    private ?string $widget_rank = null;

    #[ORM\ManyToOne(inversedBy: 'dashboard_configuration_widget')]
    private ?DashboardWidget $dashboard_widget = null;

    #[ORM\ManyToOne(inversedBy: 'core_dashboard_configuration_widget')]
    private ?DashboardConfiguration $core_dashboard_configuration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameFr(): ?string
    {
        return $this->name_fr;
    }

    public function setNameFr(string $name_fr): static
    {
        $this->name_fr = $name_fr;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): static
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getWidgetStyle(): ?string
    {
        return $this->widget_style;
    }

    public function setWidgetStyle(string $widget_style): static
    {
        $this->widget_style = $widget_style;

        return $this;
    }

    public function getWidgetWidth(): ?string
    {
        return $this->widget_width;
    }

    public function setWidgetWidth(string $widget_width): static
    {
        $this->widget_width = $widget_width;

        return $this;
    }

    public function getWidgetHeight(): ?string
    {
        return $this->widget_height;
    }

    public function setWidgetHeight(string $widget_height): static
    {
        $this->widget_height = $widget_height;

        return $this;
    }

    public function getWidgetRank(): ?string
    {
        return $this->widget_rank;
    }

    public function setWidgetRank(string $widget_rank): static
    {
        $this->widget_rank = $widget_rank;

        return $this;
    }

    public function getDashboardWidgetId(): ?DashboardWidget
    {
        return $this->dashboard_widget;
    }

    public function setDashboardWidgetId(?DashboardWidget $dashboard_widget): static
    {
        $this->dashboard_widget = $dashboard_widget;

        return $this;
    }

    public function getCoreDashboardConfigurationId(): ?DashboardConfiguration
    {
        return $this->core_dashboard_configuration;
    }

    public function setCoreDashboardConfigurationId(?DashboardConfiguration $core_dashboard_configuration): static
    {
        $this->core_dashboard_configuration = $core_dashboard_configuration;

        return $this;
    }
}
