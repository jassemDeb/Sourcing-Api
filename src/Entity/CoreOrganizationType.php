<?php

namespace App\Entity;

use App\Repository\CoreOrganizationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoreOrganizationTypeRepository::class)]
class CoreOrganizationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToMany(targetEntity: CoreOrganization::class, inversedBy: 'coreOrganizationTypes')]
    private Collection $core_organization;

    #[ORM\OneToMany(mappedBy: 'core_organization_type', targetEntity: DashboardWidget::class)]
    private Collection $dashboardWidgets;

    #[ORM\OneToMany(mappedBy: 'core_organization_type', targetEntity: DashboardConfiguration::class)]
    private Collection $dashboardConfigurations;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_At = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_At = null;

    public function __construct()
    {
        $this->core_organization = new ArrayCollection();
        $this->dashboardWidgets = new ArrayCollection();
        $this->dashboardConfigurations = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, CoreOrganization>
     */
    public function getCoreOrganizationId(): Collection
    {
        return $this->core_organization;
    }

    public function addCoreOrganizationId(CoreOrganization $coreOrganizationId): static
    {
        if (!$this->core_organization->contains($coreOrganizationId)) {
            $this->core_organization->add($coreOrganizationId);
        }

        return $this;
    }

    public function removeCoreOrganizationId(CoreOrganization $coreOrganizationId): static
    {
        $this->core_organization->removeElement($coreOrganizationId);

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
            $dashboardWidget->setCoreOrganizationTypeId($this);
        }

        return $this;
    }

    public function removeDashboardWidget(DashboardWidget $dashboardWidget): static
    {
        if ($this->dashboardWidgets->removeElement($dashboardWidget)) {
            // set the owning side to null (unless already changed)
            if ($dashboardWidget->getCoreOrganizationTypeId() === $this) {
                $dashboardWidget->setCoreOrganizationTypeId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DashboardConfiguration>
     */
    public function getDashboardConfigurations(): Collection
    {
        return $this->dashboardConfigurations;
    }

    public function addDashboardConfiguration(DashboardConfiguration $dashboardConfiguration): static
    {
        if (!$this->dashboardConfigurations->contains($dashboardConfiguration)) {
            $this->dashboardConfigurations->add($dashboardConfiguration);
            $dashboardConfiguration->setCoreOrganizationType($this);
        }

        return $this;
    }

    public function removeDashboardConfiguration(DashboardConfiguration $dashboardConfiguration): static
    {
        if ($this->dashboardConfigurations->removeElement($dashboardConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($dashboardConfiguration->getCoreOrganizationType() === $this) {
                $dashboardConfiguration->setCoreOrganizationType(null);
            }
        }

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_At;
    }

    public function setCreatedAt(\DateTimeImmutable $created_At): static
    {
        $this->created_At = $created_At;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_At;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_At): static
    {
        $this->updated_At = $updated_At;

        return $this;
    }
}
