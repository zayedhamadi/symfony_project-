<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class EditParentBYHimselfController extends AbstractController
{

    #[Route('/testtt-controllerrrrr', name: 'testtttController')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $session = $request->getSession();
        $userid = $session->get('user_id');

        if (!$userid) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->find($userid);

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }

        return $this->render('user/ProfilUSerrHimself.html.twig', [
            'user' => $user
        ]);
    }



    #[Route('/user/edit/{id}', name: 'app_user_editt')]
    public function editUserByHimself(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                $user->setImage($newFilename);
            }
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword && $plainPassword !== '') {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->flush();

            return $this->redirectToRoute('testtttController');
        }

        return $this->render('user/editUserByHimself.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


}
