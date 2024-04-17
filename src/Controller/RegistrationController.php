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
use App\Entity\UserAdditionnal;
use App\Entity\AdminAdditionnal;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
 
#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{




    #[Route('/register', name: 'register', methods: 'post')]
    public function index(MailerInterface $mailer, ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): JsonResponse
    {
        $em = $doctrine->getManager();
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
        $user_add = new UserAdditionnal();
        
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $email_exist = $userRepository->findOneByEmail($email);

        if($email_exist)
        {
            return $this->json([
                'status' => false,
                'message' => 'Ce email existe déjà',
            ]);
        } else {
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

            $id = $user->getId();
            if ($isAdmin) {
                $admin->setPassword($hashedPassword)
                 ->setEmail($email)
                 ->setUsername($username)
                 ->setFullname($fullname)
                 ->setUsername($username)
                 ->setCreatedAt($createdAt)
                 ->setUpdatedAt($updatedAt)
                 ->setIsAdmin($isAdmin)
                 ->setRoles($roles);
            $em->persist($admin);
            $em->flush();

            } else {
                $user_add->setPassword($hashedPassword)
                 ->setEmail($email)
                 ->setUsername($username)
                 ->setFullname($fullname)
                 ->setUsername($username)
                 ->setCreatedAt($createdAt)
                 ->setUpdatedAt($updatedAt)
                 ->setIsAdmin($isAdmin)
                 ->setRoles($roles);
            $em->persist($user_add);
            $em->flush();
            }
            

           // $email = (new Email())
           //  ->from('hello@example.com')
           //  ->to('you@example.com')
            // ->subject('Time for Symfony Mailer!')
           //  ->text('Sending emails is fun again!');

       // $mailer->send($email);

            return $this->json(['message' => 'Registered Successfully and Email is sended']);
        }
        
   
        
    }
}