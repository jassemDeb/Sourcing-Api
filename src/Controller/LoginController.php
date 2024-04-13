<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Repository\UserRepository;


#[Route('/api', name: 'api_')]
class LoginController extends AbstractController
{
    #[Route('/login', name: 'login', methods: 'post')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $password = $decoded->password;

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return $this->json([
                'message' => 'User not found. Please check your credentials.',
            ]);
        }

        if (password_verify($password, $user->getPassword())) {
            $roles = $user->getRoles();
            $username = $user->getUsername();
            return $this->json([
                'message' => 'Success',
                'role' => $roles,
                'username' => $username
                
            ]);

        } else {
            return $this->json([
                'message' => 'Failed to login. Please check your credentials.',
            ]);
        }

    }
}
