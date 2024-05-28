<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CoreOrganization;
use App\Entity\CoreOrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CoreOrganizationTypeRepository;
use App\Entity\UserAdditionnal;
use App\Repository\CoreOrganizationRepository;
use App\Repository\UserAdditionnalRepository;


class OrgService {

    private $doctrine;
    private $coreOrganizationRepository;
    private $userAdditionalRepository;

    public function __construct(ManagerRegistry $doctrine, CoreOrganizationRepository $coreOrganizationRepository, UserAdditionnalRepository $userAdditionnalRepository)
    {
        $this->doctrine = $doctrine;
        $this->coreOrganizationRepository = $coreOrganizationRepository;
        $this->userAdditionnalRepository = $userAdditionnalRepository;
    }

    private function serializeOrgs(array $orgs): array
    {
        $serializedOrgs = [];

        foreach ($orgs as $org) {
            $serializedOrgs[] = $this->serializeOrg($org);
        }

        return $serializedOrgs;
    }

    private function serializeOrgsByName(array $orgs): array
    {
        $orgData = [];
        foreach ($orgs as $org) {
            $types = [];
            foreach ($org->getCoreOrganizationTypes() as $type) {
                $types[] = [
                    'typeId' => $type->getId(),
                    'typeName' => $type->getName(),
                ];
            }
            $orgData[] = [
                'id' => $org->getId(),
                'name' => $org->getName(),
                'types' => $types // Include types in the response
            ];
        }
        return $orgData;
    }

    private function serializeOrg(CoreOrganizationType $org): array
    {
        $createdAt = $org->getCreatedAt();
        $createdAtDate = $createdAt->format('Y-m-d');

        $updatedAt = $org->getUpdatedAt();
        $updatedAtDate = $updatedAt->format('Y-m-d');

        return [
            'id' => $org->getId(),
            'name' => $org->getName(),
            'type' => $org->getType(),
            'enabled' => $org->isEnabled(),
            'created_at' => $createdAtDate ,
            'updated_at' => $updatedAtDate

        ];
    }

    // ADD ORG
    public function addorg(Request $request): JsonResponse {
        $em = $this->doctrine->getManager();
        $decoded = json_decode($request->getContent());
    
        $name = $decoded->name;
        $type = $decoded->type;
        $enabled = $decoded->enabled;
        $created_at_string = $decoded->created_at;
    
        $created_at = new \DateTimeImmutable($created_at_string);
        $updated_at = new \DateTimeImmutable();
    
        $org = new CoreOrganization();
        $org_type = new CoreOrganizationType();
    
        $org_type->setName($name)
                 ->setType($type)
                 ->setEnabled($enabled)
                 ->setCreatedAt($created_at)
                 ->setUpdatedAt($updated_at);
        $em->persist($org_type);
        $em->flush();
    
        $org->setName($name)
            ->addCoreOrganizationType($org_type);
        $em->persist($org);
        $em->flush();
    
        return new JsonResponse(['message' => 'Organization added']);
    }

    // GET ALL ORGS (type)
    public function orglist () : JsonResponse
    {
        $em = $this->doctrine->getManager();
        $org_type_repo = $em->getRepository(CoreOrganizationType::class);
        $orgs = $org_type_repo->findAll();

        $jsonData = $this->serializeOrgs($orgs);

        return new JsonResponse($jsonData);

    }

    // GET ORG
    public function orglistById($id): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $org_type_repo = $em->getRepository(CoreOrganizationType::class);
        $org = $org_type_repo->find($id);

        if (!$org) {
            return new JsonResponse(['error' => 'Organization not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonData = $this->serializeOrg($org);

        return new JsonResponse($jsonData);
    }

    // GET ORGS (name)
    public function orgNameList(): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $orgRepository = $em->getRepository(CoreOrganization::class);
        $orgs = $orgRepository->findAll();

        $jsonData = $this->serializeOrgsByName($orgs);

        return new JsonResponse($jsonData);
    }


    

    // DELETE ORG
    public function deleteOrgById($id_type): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $org_type_repo = $em->getRepository(CoreOrganizationType::class);
        $org_type = $org_type_repo->find($id_type);

        if (!$org_type) {
            return new JsonResponse(['error' => 'Organization not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $org = $org_type->getCoreOrganizationId()->first();
        if (!$org) {
            return new JsonResponse(['error' => 'Associated organization not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->remove($org);
        $em->remove($org_type);
        $em->flush();

        return new JsonResponse(['message' => 'Organization and associated entity deleted successfully'], JsonResponse::HTTP_OK);


    }

    // UPDATE ORG
    public function updateOrg($id_type,  Request $request): JsonResponse
    {
        
        $em = $this->doctrine->getManager();
        $org_type_repo = $em->getRepository(CoreOrganizationType::class);
        $org_type = $org_type_repo->find($id_type);

        if (!$org_type) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Organization not found',
            ]);
        }

        $decoded = json_decode($request->getContent());
        $name = $decoded->name;
        $type = $decoded->type;
        $enabled = $decoded->enabled;
        $created_at_string = $decoded->created_at;

        $created_at = new \DateTimeImmutable($created_at_string);
        $updated_at = new \DateTimeImmutable();

        // Update CoreOrganizationType entity
        $org_type->setName($name)
            ->setType($type)
            ->setEnabled($enabled)
            ->setCreatedAt($created_at)
            ->setUpdatedAt($updated_at);

        // Update associated CoreOrganization entity, if exists
        $org = $org_type->getCoreOrganizationId()->first();
        if ($org) {
            $org->setName($name);
            // Add any other properties you want to update for CoreOrganization
            $em->persist($org);
        }

        $em->persist($org_type);
        $em->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'Organization and associated entity updated successfully'
        ]);
    }

    // ASSIGN ORG TO USER 
    public function assignUserToOrganization($userAdditionnalId, $organizationId): JsonResponse
    {
        $em = $this->doctrine->getManager();
    
        $organization = $em->getRepository(CoreOrganization::class)->find($organizationId);
        $userAdditionnal = $em->getRepository(UserAdditionnal::class)->find($userAdditionnalId);
    
        if (!$organization || !$userAdditionnal) {
            return new JsonResponse(['status' => false, 'message' => 'Organization or User Additionnal not found'], 404);
        }
    
        // Remove all current organization links before adding a new one
        foreach ($userAdditionnal->getCoreOrganizations() as $existingOrg) {
           $userAdditionnal->removeCoreOrganization($existingOrg);
            $existingOrg->removeUserAdditionnalId($userAdditionnal);
            $em->persist($existingOrg); // Persist changes to detach the existing organizations 
        }
    
        // Add the new organization to the userAdditionnal
        $userAdditionnal->addCoreOrganization($organization);
        $organization->addUserAdditionnalId($userAdditionnal);
    
        $em->persist($userAdditionnal);
        $em->persist($organization);
        $em->flush();
    
        return new JsonResponse(['status' => true, 'message' => 'User Additionnal successfully assigned to the new organization'], 200);
    }
    
    // GET ORG OF A USER
    public function getOrganizationsByUserAdditionnalId($userAdditionnalId): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $userAdditionnal = $em->getRepository(UserAdditionnal::class)->find($userAdditionnalId);
    
        if (!$userAdditionnal) {
            return new JsonResponse(['status' => false, 'message' => 'User Additionnal not found'], 404);
        }
    
        $organizations = $userAdditionnal->getCoreOrganizations();
        $orgData = [];
    
        foreach ($organizations as $organization) {
            $orgData[] = [
                'id' => $organization->getId(),
                'name' => $organization->getName()
            ];
        }
    
        return new JsonResponse(['status' => true, 'data' => $orgData], 200);
    }
     
    
    

    
}