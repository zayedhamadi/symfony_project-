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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class UserrController extends AbstractController
{
    private $manager;

    private $user;

    private $userRepository;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, UserRepository $user)
    {
        $this->manager = $manager;

        $this->user = $user;
        $this->userRepository = $userRepository;
    }

    #[Route('/userCreate', name: 'user_create', methods: 'POST')]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, ParameterBagInterface $params): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];

        if ($this->userRepository->findOneBy(['email' => $email])) {
            return new JsonResponse(['status' => false, 'message' => 'Cet email existe déjà'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        $projectDir = $params->get('kernel.project_dir');
        $imagePath = $projectDir . '/public/images/educo.jpg';

        if (!file_exists($imagePath)) {
            throw new \Exception("L'image educo.jpg n'a pas été trouvée dans le dossier public/images.");
        }

        $htmlContent = "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Bienvenue chez Educo</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; }
            .container { max-width: 600px; background: #fff; padding: 20px; border-radius: 10px; margin: 20px auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
            .header img { max-width: 150px; margin-bottom: 20px; }
            h1 { color: #007BFF; }
            .info { background: #f8f8f8; padding: 15px; border-radius: 8px; margin: 20px 0; }
            .btn { display: inline-block; background: #007BFF; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: bold; }
            .footer { font-size: 14px; color: #888; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='cid:educo_logo' alt='Educo Logo'>
            </div>
            <h1>Bienvenue sur Educo !</h1>
            <p>Merci de vous être inscrit. Voici vos informations de connexion :</p>
            <div class='info'>
                <p><strong>Email :</strong> $email</p>
                <p><strong>Mot de passe :</strong> $password</p>
            </div>
            <p>Nous vous recommandons de modifier votre mot de passe dès votre première connexion.</p>
            <a href='https://127.0.0.1:8000/login' class='btn'>Se connecter</a>
            <div class='footer'>
                <p>&copy; " . date("Y") . " Educo. Tous droits réservés.</p>
            </div>
        </div>
    </body>
    </html>
    ";


        $emailMessage = (new Email())
            ->from('educolearning@educo.com')
            ->to($email)
            ->subject('Bienvenue sur Educo')
            ->html($htmlContent)
            ->attachFromPath($imagePath, 'educo_logo', 'image/jpeg');

        $mailer->send($emailMessage);

        return new JsonResponse(['status' => true, 'message' => 'Utilisateur créé avec succès et email envoyé'], 201);
    }

    #[Route('/api/getAllUsers', name: 'get_allusers', methods: 'GET')]
    public function getAllUsers(): Response
    {
        $users = $this->user->findAll();

        return $this->json($users, 200);
    }
}