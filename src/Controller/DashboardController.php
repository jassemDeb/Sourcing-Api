<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DashboardService;
 
#[Route('/api', name: 'api_')]
class DashboardController extends AbstractController
{
    private $dashService;

    public function __construct(DashboardService $dashService)
    {
        $this->dashService = $dashService;
    }

    #[Route('/get_dashByUser/{userAdditionnalId}', name: 'get_dashByUser', methods: 'get')]
    public function getDashboardConfigurationsByUserAdditionnalId($userAdditionnalId): JsonResponse
    {
        return $this->dashService->getDashboardConfigurationsByUserAdditionnalId($userAdditionnalId);
    }

    #[Route('/create_dash/{userAdditionnalId}', name: 'create_dash', methods: 'post')]
    public function createDashboardConfigurationForUser($userAdditionnalId): JsonResponse
    {
        return $this->dashService->createDashboardConfigurationForUser($userAdditionnalId);
    }

    #[Route('/update_dash/{dashboardConfigurationId}/{organizationId}/{organizationTypeId}', name: 'update_dash', methods: 'put')]
    public function updateDashboardConfiguration($dashboardConfigurationId, $organizationId, $organizationTypeId): JsonResponse
    {
        return $this->dashService->updateDashboardConfiguration($dashboardConfigurationId, $organizationId, $organizationTypeId);
    }
}