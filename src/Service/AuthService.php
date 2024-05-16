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
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $plaintextPassword = $decoded->password;
        $fullname = $decoded->fullname;
        $username = $decoded->username;
        $roles = $decoded->roles;
        $isAdmin = $decoded->isAdmin;

        $createdAt = new \DateTime(); 
        $updatedAt = new \DateTime();

        $user = new User();
        $admin = new AdminAdditionnal();
        $userAdd = new UserAdditionnal();
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $emailExist = $this->userRepository->findOneByEmail($email);

        if($emailExist) {
            return new JsonResponse([
                'status' => false,
                'message' => 'This email already exists'
            ]);
        } else {
            $user->setPassword($hashedPassword)
                 ->setEmail($email)
                 ->setUsername($username)
                 ->setFullname($fullname)
                 ->setCreatedAt($createdAt)
                 ->setUpdatedAt($updatedAt)
                 ->setIsAdmin($isAdmin)
                 ->setRoles($roles);
            $em->persist($user);
            $em->flush();
            
            if ($isAdmin) {
                $admin->setPassword($hashedPassword)
                      ->setEmail($email)
                      ->setUsername($username)
                      ->setFullname($fullname)
                      ->setCreatedAt($createdAt)
                      ->setUpdatedAt($updatedAt)
                      ->setIsAdmin($isAdmin)
                      ->setRoles($roles);
                $em->persist($admin);
                $em->flush();

            } else {
                $userAdd->setPassword($hashedPassword)
                        ->setEmail($email)
                        ->setUsername($username)
                        ->setFullname($fullname)
                        ->setCreatedAt($createdAt)
                        ->setUpdatedAt($updatedAt)
                        ->setIsAdmin($isAdmin)
                        ->setRoles($roles);
                $em->persist($userAdd);
                $em->flush();
            }
            
            return new JsonResponse(['message' => 'Registered Successfully']);
        }
    }      
}
