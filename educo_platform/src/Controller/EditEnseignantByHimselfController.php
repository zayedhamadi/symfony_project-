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

class EditEnseignantByHimselfController extends AbstractController
{
    #[Route('/edit-enseignant-by-himself', name: 'EnseignantControllerr')]
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

        return $this->render('user/EnseignantProfile.html.twig', [
            'user' => $user
        ]);
    }
    #[Route('/user/editEnseignant/{id}', name: 'app_user_edittEnseignant')]
    public function editEnseignantByHimself(
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

            return $this->redirectToRoute('EnseignantControllerr');
        }

        return $this->render('user/editEnseignantByHimself.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


}
