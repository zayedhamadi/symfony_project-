<?php

namespace App\Controller;

use App\Entity\Enum\EtatCompte;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/user')]
final class UserController extends AbstractController
{

    #[Route('/getAll', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,
                        UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer,
                        ParameterBagInterface $params, SluggerInterface $slugger): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                        $user->setImage($newFilename);
                    } catch (FileException $e) {
                        throw new Exception("Erreur lors de l'upload de l'image.");
                    }
                }

                $plainPassword = $user->getPassword();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Utilisateur créé avec succès.');

                $projectDir = $params->get('kernel.project_dir');
                $imagePath = $projectDir . '/public/images/educo.jpg';

                if (!file_exists($imagePath)) {
                    throw new Exception("L'image educo.jpg n'a pas été trouvée dans le dossier public/images.");
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
                        <p><strong>Email :</strong> {$user->getEmail()}</p>
                        <p><strong>Mot de passe :</strong> {$plainPassword}</p>
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
                    ->to($user->getEmail())
                    ->subject('Bienvenue sur Educo')
                    ->html($htmlContent)
                    ->attachFromPath($imagePath, 'educo_logo', 'image/jpeg');

                $mailer->send($emailMessage);

                return $this->redirectToRoute('app_user_index');
            }

            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création de l\'utilisateur.');
            return $this->redirectToRoute('app_user_index');
        }
    }



    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $user->setImage($newFilename);
            }

            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }




    #[Route('/', name: 'app_user_list')]
    public function chercherUser(Request $request, UserRepository $userRepository)
    {
        $search = $request->query->get('search');

        if ($search) {
            $users = $userRepository->createQueryBuilder('u')
                ->where('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $users = $userRepository->findAll();
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }






}
