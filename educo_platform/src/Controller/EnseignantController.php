<?php


namespace App\Controller;

use App\Repository\MatiereRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EnseignantController extends AbstractController
{
//    #[Route('/dashboard/enseignant', name: 'enseigant_app')]
//    public function index(): Response
//    {
//        return $this->render('professor.html.twig', [
//            'controller_name' => 'AboutController',
//        ]);
//    }
    #[Route('/dashboard/enseignant/{id}', name: 'enseigant_app')]
    public function index(int $id, UserRepository $userRepository, MatiereRepository $matiereRepository): Response
    {
        // Find the user with the given ID (ensure it's an enseignant)
        $enseignant = $userRepository->findOneBy(['id' => $id, 'role' => 'enseignant']);

        if (!$enseignant) {
            throw $this->createNotFoundException('Enseignant not found');
        }

        // Get the matieres related to this enseignant
        $matieres = $matiereRepository->findBy(['idEnsg' => $enseignant]);
        $matiereCount = count($matieres);

        // Render the dashboard view for the enseignant
        return $this->render('professor.html.twig', [
            'enseignant' => $enseignant,
            'matiereCount' => $matiereCount,
            'matieres' => $matieres,
        ]);
    }
}
