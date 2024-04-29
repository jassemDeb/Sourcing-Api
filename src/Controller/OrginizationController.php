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


#[Route('/api', name: 'api_')]
class OrginizationController extends AbstractController
{
    #[Route('/addorg', name: 'addorg', methods: 'post')]
    public function addorg(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $em = $doctrine->getManager();
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



        return $this->json(['message' => 'Organization added']);
    }

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function serializeUsers(array $orgs): array
    {
        $serializedOrgs = [];

        foreach ($orgs as $org) {
            $serializedOrgs[] = $this->serializeUser($org);
        }

        return $serializedOrgs;
    }

    private function serializeUser(CoreOrganizationType $org): array
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

    #[Route('/orgs', name: 'orglist', methods: 'get')]
    public function orglist () : JsonResponse
    {
        $org_type_repo = $this->entityManager->getRepository(CoreOrganizationType::class);
        $orgs = $org_type_repo->findAll();

        $jsonData = $this->serializeUsers($orgs);

        return new JsonResponse($jsonData);

    }
    #[Route('/orgbyid/{id}', name: 'orglistid', methods: 'get')]
    public function orglistById ($id) : JsonResponse
    {
        $org_type_repo = $this->entityManager->getRepository(CoreOrganizationType::class);
        $org = $org_type_repo->find($id);

        $jsonData = $this->serializeUser($org);

        return new JsonResponse($jsonData);

    }

    #[Route('/deleteOrg/{id_type}', name: 'delete_org' ,methods: 'delete')]
    public function deleteOrgById(Request $request, $id_type): JsonResponse
    {
        $org_type_repo = $this->entityManager->getRepository(CoreOrganizationType::class);
        $org_type = $org_type_repo->find($id_type);

        if (!$org_type) {
            return new JsonResponse(['error' => 'Organization not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $org = $org_type->getCoreOrganizationId()->first();
        if (!$org) {
            return new JsonResponse(['error' => 'Associated organization not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($org);
        $this->entityManager->remove($org_type);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Organization and associated entity deleted successfully'], JsonResponse::HTTP_OK);


    }

    #[Route('/updateOrg/{id_type}', name: 'update_org', methods: 'put')]
    public function updateOrg($id_type,  Request $request): JsonResponse{
        
        $org_type_repo = $this->entityManager->getRepository(CoreOrganizationType::class);
        $org_type = $org_type_repo->find($id_type);

        if (!$org_type) {
            return $this->json([
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
            $this->entityManager->persist($org);
        }

        $this->entityManager->persist($org_type);
        $this->entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Organization and associated entity updated successfully'
        ]);
    }
}
