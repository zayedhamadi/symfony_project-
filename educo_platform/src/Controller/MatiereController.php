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
#[Route('/matiere')]


class MatiereController extends AbstractController
{

    #[Route('/new', name: 'app_matiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($matiere);
            $entityManager->flush();
            $this->addFlash('success', 'Subject added successfully!');
            return $this->redirectToRoute('app_matiere_list');

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
    #[Route('/show', name: 'app_matiere_list')]
    public function index(Request $request): Response
    {
        // Fetch all matieres, including their associated enseignants
        $matieres = $this->matiereRepository->findAllWithEnseignant();
        // i used the trim to make it accepts the space
        $search = trim($request->query->get('search', ''));

        // Group matieres by teacher (idEnsg)
        $groupedMatieres = [];
        foreach ($matieres as $matiere) {
            $teacher = $matiere->getIdEnsg();
            $teacherName = $teacher ? $matiere->getIdEnsg()->getNom() : 'Aucun enseignant';

            if ($search && stripos($teacherName, $search) === false) {
                continue; // Skip non-matching teachers
            }
            if (!isset($groupedMatieres[$teacherName])) {
                $groupedMatieres[$teacherName] = [];
            }
            $groupedMatieres[$teacherName][] = $matiere;
        }

        return $this->render('matiere/list.html.twig', [
            'groupedMatieres' => $groupedMatieres,
            'search' => $search, // Pass search term to template

        ]);

    }

    #[Route('/{id}/edit', name: 'app_matiere_edit')]
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
    #[Route('/{id}/delete', name: 'app_matiere_delete', methods: ['POST'])]
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