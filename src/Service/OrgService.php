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

class OrgService {

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function serializeOrgs(array $orgs): array
    {
        $serializedOrgs = [];

        foreach ($orgs as $org) {
            $serializedOrgs[] = $this->serializeOrg($org);
        }

        return $serializedOrgs;
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

    // GET ORGS
    public function orglist () : JsonResponse
    {
        $em = $this->doctrine->getManager();
        $org_type_repo = $em->getRepository(CoreOrganizationType::class);
        $orgs = $org_type_repo->findAll();

        $jsonData = $this->serializeUsers($orgs);

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

    
}