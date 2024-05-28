<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\UserAdditionnal;
use App\Entity\AdminAdditionnal;

class AuthService
{
    private $doctrine;
    private $passwordHasher;
    private $userRepository;

    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }


    public function login(Request $request): JsonResponse 
    {
        $em = $this->doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $password = $decoded->password;

        $user = $this->userRepository->findOneByEmail($email);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found. Please check your credentials.']);
        }

        if ($this->passwordHasher->isPasswordValid($user, $password)) {
            $roles = $user->getRoles();
            $username = $user->getUsername();
            return new JsonResponse([
                'message' => 'Success',
                'role' => $roles,
                'username' => $username
            ]);

        } else {
            return new JsonResponse(['message' => 'Failed to login. Please check your credentials.']);
        }

    }

    public function register(Request $request): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $em->getConnection()->beginTransaction();  // Start transaction
    
        try {
            $decoded = json_decode($request->getContent(), true);
            $email = $decoded['email'] ?? null;
            $plaintextPassword = $decoded['password'] ?? null;
            $fullname = $decoded['fullname'] ?? null;
            $username = $decoded['username'] ?? null;
            $roles = $decoded['roles'] ?? [];
            $isAdmin = $decoded['isAdmin'] ?? false;
    
            if (empty($email) || empty($plaintextPassword) || empty($fullname) || empty($username)) {
                return new JsonResponse(['status' => false, 'message' => 'Required fields are missing'], 400);
            }
    
            if ($this->userRepository->findOneByEmail($email)) {
                return new JsonResponse(['status' => false, 'message' => 'This email already exists'], 409);
            }
    
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword)
                 ->setEmail($email)
                 ->setUsername($username)
                 ->setFullname($fullname)
                 ->setCreatedAt(new \DateTime())
                 ->setUpdatedAt(new \DateTime())
                 ->setRoles($roles)
                 ->setIsAdmin($isAdmin);
    
            $em->persist($user);
            $em->flush();  
    
            // Handle additional user details based on role
            if (in_array('SuperAdmin', $roles)) {
                $admin = new AdminAdditionnal();
                $admin->setEmail($email)
                      ->setPassword($hashedPassword)
                      ->setUsername($username)
                      ->setFullname($fullname)
                      ->setRoles($roles)
                      ->setCreatedAt(new \DateTime())
                      ->setUpdatedAt(new \DateTime())
                      ->setIsAdmin($isAdmin);
                $em->persist($admin);
                $em->flush();
            } else {
                $userAdd = new UserAdditionnal();
                $userAdd->setUser($user)
                        ->setPassword($hashedPassword)
                        ->setRoles($roles)
                        ->setIsActive(true)  // Example setting specific to UserAdditional
                        ->setEmail($email)
                        ->setUsername($username)
                        ->setFullname($fullname)
                        ->setCreatedAt(new \DateTime())
                        ->setUpdatedAt(new \DateTime());
                $em->persist($userAdd);
                $em->flush();
                $userId = $userAdd->getId();
            }
    
            $em->flush();
            $em->getConnection()->commit();  // Commit transaction
    
            return new JsonResponse(['status' => true, 'message' => 'Registered successfully', 'userId' => $userId], 201);
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();  // Rollback transaction on error
            return new JsonResponse(['status' => false, 'message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
    

}
