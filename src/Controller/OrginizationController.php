<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CoreOrganization;
use App\Entity\CoreOrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CoreOrganizationTypeRepository;
use App\Service\OrgService;


#[Route('/api', name: 'api_')]
class OrginizationController extends AbstractController
{
    private $orgService;

    public function __construct(OrgService $orgService)
    {
        $this->orgService = $orgService;
    }

    #[Route('/addorg', name: 'addorg', methods: 'post')]
    public function addorg(Request $request): JsonResponse
    {
        return $this->orgService->addorg($request);
    }

    #[Route('/orgs', name: 'orglist', methods: 'get')]
    public function orglist () : JsonResponse
    {
        return $this->orgService->orglist();
    }

    #[Route('/orgsByName', name: 'orgsByName', methods: 'get')]
    public function orgNameList () : JsonResponse
    {
        return $this->orgService->orgNameList();
    }

    #[Route('/orgbyid/{id}', name: 'orglistid', methods: 'get')]
    public function orglistById ($id) : JsonResponse
    {
        return $this->orgService->orglistById($id);

    }

    #[Route('/deleteOrg/{id_type}', name: 'delete_org' ,methods: 'delete')]
    public function deleteOrgById($id_type): JsonResponse
    {
        return $this->orgService->deleteOrgById($id_type);
    }

    #[Route('/updateOrg/{id_type}', name: 'update_org', methods: 'put')]
    public function updateOrg($id_type,  Request $request): JsonResponse{
        
        return $this->orgService->updateOrg($id_type, $request);
    }

    #[Route('/assignUserToOrg/{userAdditionalId}/{organizationId}', name: 'assign_user_to_org', methods: 'put')]
    public function assignUserToOrganization($userAdditionalId, $organizationId): JsonResponse
    {
        return $this->orgService->assignUserToOrganization($userAdditionalId, $organizationId);
    }

    #[Route('/getOrgForUser/{userAdditionalId}', name: 'get_org_for_user', methods: 'get')]
    public function getOrganizationsByUserAdditionnalId($userAdditionalId): JsonResponse
    {
        return $this->orgService->getOrganizationsByUserAdditionnalId($userAdditionalId);
    }
}
