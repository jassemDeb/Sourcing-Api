<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\AdminAdditionnal;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Entity\UserAdditionnal;
use App\Repository\AdminAdditionnalRepository;
use App\Repository\UserAdditionnalRepository;

use App\Service\UserService;


#[Route('/api', name: 'api_')]
class UsersController extends AbstractController
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    #[Route('/users', name: 'getUsers', methods: 'get')]
    public function getAll() : JsonResponse
    {
        return $this->userService->getAll();
    }

    #[Route('/users/{id}', name: 'getUserbyId', methods: 'get')]
    public function getUserById($id): JsonResponse
    {
        return $this->userService->getUserById($id);
    }


    #[Route('/deleteUser/{id}', name: 'delete_user' ,methods: 'delete')]
    public function deleteUserById($id): JsonResponse
    {
        return $this->userService->deleteUserById($id);

    }

    #[Route('/update/{id}', name: 'update', methods: 'put')]
    public function update($id, Request $request): JsonResponse{
        
        return $this->userService->update($id, $request);

    }

    #[Route('/usersOrg/{id}', name: 'usersOrg', methods: 'get')]
    public function getUserOrganizations($id): JsonResponse
    {
        return $this->userService->getUserOrganizations($id);
    }

    #[Route('/userByUsername/{username}', name: 'userByUsername', methods: 'get')]
    public function getUserByUsername($username): JsonResponse
    {
        return $this->userService->getUserByUsername($username);
    }
}
