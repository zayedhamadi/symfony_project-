<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class UserController extends AbstractController
{
    private $manager;

    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
        $this->manager=$manager;

        $this->user=$user;
    }


    //Création d'un utilisateur
    #[Route('/userCreate', name: 'user_create', methods:'POST')]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];

        if ($this->user->findOneByEmail($email)) {
            return new JsonResponse(['status' => false, 'message' => 'Cet email existe déjà'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Utilisateur créé avec succès'], 201);
    }



    //Liste des utilisateurs
    #[Route('/api/getAllUsers', name: 'get_allusers', methods:'GET')]
    public function getAllUsers(): Response
    {
        $users=$this->user->findAll();

        return $this->json($users,200);
    }
}