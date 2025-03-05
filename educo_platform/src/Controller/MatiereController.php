<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\CoursType;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MatiereController extends AbstractController
{

    #[Route('/matiere/new', name: 'app_matiere_new', methods: ['GET', 'POST'])]
    //#[IsGranted('ROLE_ADMIN')] // Restrict access to admins only
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matiere = new Matiere();
        $enseignant = $matiere->getIdEnsg(); // Get the enseignant (teacher) from the Matiere entity
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($matiere);
            $entityManager->flush();
            $this->addFlash('success', 'Subject added successfully!');
            return $this->redirectToRoute('app_matiere_new');

        }

        return $this->render('matiere/new.html.twig', [
            'matiere' => $matiere,
            'form' => $form,
        ]);
    }

    private MatiereRepository $matiereRepository;

    public function __construct(MatiereRepository $matiereRepository)
    {
        $this->matiereRepository = $matiereRepository;
    }
    #[Route('/matieres', name: 'app_matiere_list')]
    public function index(): Response
    {
        // Fetch all matieres, including their associated enseignants
        $matieres = $this->matiereRepository->findAllWithEnseignant();

        // Group matieres by teacher (idEnsg)
        $groupedMatieres = [];
        foreach ($matieres as $matiere) {
            $teacherName = $matiere->getIdEnsg() ? $matiere->getIdEnsg()->getNom() : 'Aucun enseignant';
            if (!isset($groupedMatieres[$teacherName])) {
                $groupedMatieres[$teacherName] = [];
            }
            $groupedMatieres[$teacherName][] = $matiere;
        }

        return $this->render('matiere/list.html.twig', [
            'groupedMatieres' => $groupedMatieres,
        ]);
//        $matieres = $this->matiereRepository->findAllWithEnseignant();
//
//        return $this->render('matiere/list.html.twig', [
//            'matieres' => $matieres,
//        ]);
    }

    #[Route('/matiere/{id}/edit', name: 'app_matiere_edit')]
    public function edit(Request $request, Matiere $matiere, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatiereType::class, $matiere, [
            'matiere_id' => $matiere->getId(), // Pass Matiere ID to the form
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the Matiere with selected users
            $entityManager->flush();

            return $this->redirectToRoute('app_matiere_list');
        }

        return $this->render('matiere/edit.html.twig', [
            'matiere' => $matiere,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/matiere/{id}/delete', name: 'app_matiere_delete', methods: ['POST'])]
    public function delete(Request $request, Matiere $matiere, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matiere->getId(), $request->request->get('_token'))) {
            $entityManager->remove($matiere);
            $entityManager->flush();

            $this->addFlash('success', 'Matière supprimée avec succès.');
        }

        return $this->redirectToRoute('app_matiere_list');
    }
}