<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserAdditionnal;
use App\Repository\UserAdditionnalRepository;
use App\Entity\DashboardConfiguration;
use App\Entity\CoreOrganization;
use App\Entity\CoreOrganizationType;
use App\Repository\DashboardConfigurationRepository;
use App\Repository\CoreOrganizationTypeRepository;
use App\Repository\CoreOrganizationRepository;


class DashboardService {

    private $doctrine;


    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getDashboardConfigurationsByUserAdditionnalId(int $userAdditionnalId): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $dashboardConfigRepository = $em->getRepository(DashboardConfiguration::class);

        // Find all dashboard configurations for the specified UserAdditionnal ID
        $dashboardConfigs = $dashboardConfigRepository->findBy(['core_user_additionnal' => $userAdditionnalId]);

        if (!$dashboardConfigs) {
            return new JsonResponse(['status' => false, 'message' => 'No dashboard configurations found'], 404);
        }

        // Serialize the dashboard configurations into an array
        $dashboardConfigsArray = array_map(function ($config) {
            return [
                'id' => $config->getId(),
                'is_default' => $config->isIsDefault()
            ];
        }, $dashboardConfigs);

        return new JsonResponse(['status' => true, 'dashboardConfigurations' => $dashboardConfigsArray]);
    }

    public function createDashboardConfigurationForUser(int $userAdditionnalId): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $userAdditionnal = $em->getRepository(UserAdditionnal::class)->find($userAdditionnalId);

        if (!$userAdditionnal) {
            return new JsonResponse(['status' => false, 'message' => 'UserAdditionnal not found'], 404);
        }

        $dashboardConfiguration = new DashboardConfiguration();
        $dashboardConfiguration->setIsDefault(true);  // Set default status to true
        $dashboardConfiguration->setCoreUserAdditionnalId($userAdditionnal);

        $em->persist($dashboardConfiguration);
        $em->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Dashboard configuration created and assigned to UserAdditionnal successfully',
            'data' => ['dashboardConfigurationId' => $dashboardConfiguration->getId()]
        ]);
    }

    public function updateDashboardConfiguration(int $dashboardConfigurationId, int $organizationId, int $organizationTypeId): JsonResponse
    {
        $em = $this->doctrine->getManager();

        $dashboardConfiguration = $em->getRepository(DashboardConfiguration::class)->find($dashboardConfigurationId);
        $organization = $em->getRepository(CoreOrganization::class)->find($organizationId);
        $organizationType = $em->getRepository(CoreOrganizationType::class)->find($organizationTypeId);

        if (!$dashboardConfiguration) {
            return new JsonResponse(['status' => false, 'message' => 'Dashboard configuration not found'], 404);
        }

        if (!$organization) {
            return new JsonResponse(['status' => false, 'message' => 'Core organization not found'], 404);
        }

        if (!$organizationType) {
            return new JsonResponse(['status' => false, 'message' => 'Core organization type not found'], 404);
        }

        // Set the new organization and organization type
        $dashboardConfiguration->setCoreOrganizationId($organization);
        $dashboardConfiguration->setCoreOrganizationType($organizationType);

        $em->persist($dashboardConfiguration);
        $em->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Dashboard configuration updated successfully'
        ]);
    }
}