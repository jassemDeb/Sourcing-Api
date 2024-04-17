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


#[Route('/api', name: 'api_')]
class UsersController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/users', name: 'getUsers', methods: 'get')]
    public function getAll() : JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        $jsonData = $this->serializeUsers($users);

        return new JsonResponse($jsonData);
    }

    #[Route('/users/{id}', name: 'getUserbyId', methods: 'get')]
    public function getUserById($id): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Serialize the user to JSON
        $jsonData = $this->serializeUser($user);

        // Return a JSON response
        return new JsonResponse($jsonData);
    }

    private function serializeUsers(array $users): array
    {
        $serializedUsers = [];

        foreach ($users as $user) {
            $serializedUsers[] = $this->serializeUser($user);
        }

        return $serializedUsers;
    }

    private function serializeUser(User $user): array
    {
        $createdAt = $user->getCreatedAt();
        $createdAtDate = $createdAt->format('Y-m-d');

        $updatedAt = $user->getUpdatedAt();
        $updatedAtDate = $updatedAt->format('Y-m-d');

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'fullname' => $user->getFullname(),
            'username' => $user->getUsername(),
            'isAdmin' => $user->isIsAdmin(),
            'created_at' => $createdAtDate ,
            'updated_at' => $updatedAtDate

        ];
    }
    


    #[Route('/users', name: 'app_users')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsersController.php',
        ]);
    }

    #[Route('/deleteUser/{id}', name: 'delete_user' ,methods: 'delete')]
    public function deleteUserById(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);
        
        $email = $user->getEmail();
        $isAdmin = $user->isIsAdmin();

        $query = $entityManager->createQuery(
            'DELETE FROM App\Entity\AdminAdditionnal a WHERE a.email = :email'
        )->setParameter('email', $email);

        $query1 = $entityManager->createQuery(
            'DELETE FROM App\Entity\UserAdditionnal u WHERE u.email = :email'
        )->setParameter('email', $email);


        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($isAdmin){
            $entityManager->remove($user);
            $deletedRows = $query->execute();
            $entityManager->flush();
            return new JsonResponse(['message' => 'User deleted successfully'], JsonResponse::HTTP_OK);
        } else {

            $entityManager->remove($user);
            $deletedRows = $query1->execute();
            $entityManager->flush();
            return new JsonResponse(['message' => 'User deleted successfully'], JsonResponse::HTTP_OK);

        }

        

    }

    #[Route('/update/{id}', name: 'update', methods: 'put')]
    public function update($id, ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, AdminAdditionnalRepository $adminRepository): JsonResponse{
        
        $em = $doctrine->getManager();
        $user = $userRepository->find($id);
        $email_check = $user->getEmail();
        

        if (!$user) {
            return $this->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $plaintextPassword = $decoded->password;
        $fullname = $decoded->fullname;
        $username = $decoded->username;
        $roles = $decoded->roles;
        $isAdmin = $decoded->isAdmin;

        $createdAt = $user->getCreatedAt();
        $updatedAt = new \DateTime();


        if (!empty($plaintextPassword)) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
        } else {
            $hashedPassword = $user->getPassword();
        }

        

        $user->setPassword($hashedPassword)
             ->setEmail($email)
             ->setUsername($username)
             ->setFullname($fullname)
             ->setUsername($username)
             ->setCreatedAt($createdAt)
             ->setUpdatedAt($updatedAt)
             ->setIsAdmin($isAdmin)
             ->setRoles($roles);
        $em->persist($user);
        $em->flush();

        return $this->json([
            'status' => true,
            'message' => 'User updated successfully'
        ]);

    }
}
