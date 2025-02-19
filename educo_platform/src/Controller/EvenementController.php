<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Enum\EventType;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/evenement/new', name: 'evenement_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
         $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/evenement/{id}/edit', name: 'evenement_edit', methods: ['GET','POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException('evenement non trouvée');
        }
    
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'evenement modifiée avec succès !');
 
            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('evenement/edit.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }

    #[Route('/evenement/{id}/delete', name: 'evenement_delete' , methods :['GET'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($id);
        if ($evenement) {
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'evenement supprimée avec succès');
        } else {
            $this->addFlash('error', 'evenement non trouvée');
        }

        return $this->redirectToRoute('evenement_index');
    }
}
