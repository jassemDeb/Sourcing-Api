<?php

namespace App\Service;

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

class UserService {

    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    private function serializeUsers(array $users): array
    {
        $serializedUsers = [];

        foreach ($users as $user) {
            $serializedUsers[] = $this->serializeUser($user);
        }

        return $serializedUsers;
    }

    private function serializeUser(UserAdditionnal $user): array
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
            'created_at' => $createdAtDate ,
            'updated_at' => $updatedAtDate

        ];
    }

    private function serializeUserAdditionnal(UserAdditionnal $userAdditionnal): array
    {
        return [
            'id' => $userAdditionnal->getId(),
            'isActive' => $userAdditionnal->isIsActive(),
            'username' => $userAdditionnal->getUser()->getUsername(), // Assuming there is a User entity linked
            // add other fields as necessary
        ];
    }

    // GET ALL USERS
    public function getAll() : JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(UserAdditionnal::class);
        $users = $userRepository->findAll();

        $jsonData = $this->serializeUsers($users);

        return new JsonResponse($jsonData);
    }

    // GET USER BY ID
    public function getUserById($id): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(UserAdditionnal::class);
        $user = $userRepository->findOneBy(['id' => $id]);
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Serialize the user to JSON
        $jsonData = $this->serializeUser($user);

        // Return a JSON response
        return new JsonResponse($jsonData);
    }

  // DELETE USER
  public function deleteUserById(int $id): JsonResponse
    {
        $this->entityManager->getConnection()->beginTransaction(); // Start a transaction

        try {
            // Retrieve the UserAdditionnal entity
            $userAdditionnal = $this->entityManager->getRepository(UserAdditionnal::class)->find($id);
            if (!$userAdditionnal) {
                $this->entityManager->getConnection()->rollback();
                return new JsonResponse(['status' => false, 'message' => 'UserAdditionnal not found'], 404);
            }
                    // Remove associated Dashboard Configurations
            $dashboardConfigurations = $userAdditionnal->getDashboardConfigurationId();
            foreach ($dashboardConfigurations as $config) 
            {
                $this->entityManager->remove($config); // Delete each dashboard configuration
            }

            // Retrieve associated User from the UserAdditionnal entity
            $user = $userAdditionnal->getUser();
            if ($user) {
                // Remove the User entity first to avoid foreign key constraint violation
                $this->entityManager->remove($user);
            }

            // Now remove the UserAdditionnal entity
            $this->entityManager->remove($userAdditionnal);
            $this->entityManager->flush(); // Apply the changes to the database
            $this->entityManager->getConnection()->commit(); // Commit the transaction

            return new JsonResponse(['message' => 'User and UserAdditionnal deleted successfully'], 200);
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback(); // Rollback the transaction on error
            error_log('Failed to delete User and UserAdditionnal: ' . $e->getMessage());
            return new JsonResponse(['status' => false, 'message' => 'Failed to delete User and UserAdditionnal: ' . $e->getMessage()], 500);
        }
    }
  
    // UPDATE USER
public function update($id, $request): JsonResponse 
    {

        $userAdditionnalRepository = $this->entityManager->getRepository(UserAdditionnal::class);
        $userAdd = $userAdditionnalRepository->find($id);

        $user = $userAdd->getUser();

        if (!$userAdd) {
            return new JsonResponse([
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

        $updatedAt = new \DateTime();

        $hashedPassword = !empty($plaintextPassword) ? $this->passwordHasher->hashPassword($userAdd, $plaintextPassword) : $userAdd->getPassword();

        //UPDATE USER ADDITIONNAL
        $userAdd->setPassword($hashedPassword)
            ->setEmail($email)
            ->setUsername($username)
            ->setFullname($fullname)
            ->setUpdatedAt($updatedAt)
            ->setRoles($roles);

        $this->entityManager->persist($userAdd);

        //UPDATE USER
        $user->setPassword($hashedPassword)
            ->setEmail($email)
            ->setUsername($username)
            ->setFullname($fullname)
            ->setUpdatedAt($updatedAt)
            ->setRoles($roles);

        $this->entityManager->persist($user);



        $this->entityManager->flush();

        return new JsonResponse([
            'status' => true,
            'message' => 'User updated successfully'
        ]);
    }

// GET USER'S ORGANIZATION 
public function getUserOrganizations($id): JsonResponse
{
    $userAdditionnalRepository = $this->entityManager->getRepository(UserAdditionnal::class);
    $userAdd = $userAdditionnalRepository->find($id);

    if (!$userAdd) {
        return new JsonResponse(['status' => false, 'message' => 'User Additionnal not found'], 404);
    }

    $organizations = $userAdd->getCoreOrganizations();
    $orgData = [];

    foreach ($organizations as $organization) {
        $orgData[] = [
            'id' => $organization->getId(),
            'name' => $organization->getName()
        ];
    }

    return new JsonResponse(['status' => true, 'data' => $orgData]);
}

// GET USER BY USERNAME
public function getUserByUsername(string $username): JsonResponse
{
    $entityManager = $this->entityManager;
    $userAdditionnalRepository = $entityManager->getRepository(UserAdditionnal::class);
    
    $userAdditionnal = $userAdditionnalRepository->findOneBy(['username' => $username]);

    
    if (!$userAdditionnal) {
        return new JsonResponse(['status' => false, 'message' => 'User Additionnal not found'], 404);
    }
    
    // Assuming you have a method to serialize your UserAdditionnal data
    $userData = $this->serializeUserAdditionnal($userAdditionnal);

    return new JsonResponse(['status' => true, 'data' => $userData], 200);
}


}