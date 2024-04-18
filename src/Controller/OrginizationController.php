<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CoreOrganization;
use App\Entity\CoreOrganizationType;

#[Route('/api', name: 'api_')]
class OrginizationController extends AbstractController
{
    #[Route('/addorg', name: 'addorg', methods: 'post')]
    public function index(ManagerRegistry $doctrine, Request $request): JsonResponse
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
}
