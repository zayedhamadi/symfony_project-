<?php

namespace App\Controller;

use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Entity\Enum\Statut;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'reclamation', methods: ['GET'])]
    public function index(ReclamationRepository $repository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $repository->findAll(),
        ]);
    }
    #[Route('/ajouter', name: 'ajouter_reclamation', methods: ['GET','POST'])]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Debugging
            dump($form->getErrors());
            dump($reclamation); // Vérifie les données avant la persistance
        
            $reclamation->setDateDeCreation(new \DateTime()); 
            $reclamation->setStatut(Statut::EN_ATTENTE);
            
            
            $entityManager->persist($reclamation);
            $entityManager->flush();
        
            $this->addFlash('success', 'Votre réclamation a été envoyée avec succès !');
            return $this->redirectToRoute('reclamation');
        }
        

        return $this->render('reclamation/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/modifier/{id}', name: 'modifier_reclamation', methods: ['GET', 'POST'])]
    public function modifier(int $id, Request $request, EntityManagerInterface $entityManager, ReclamationRepository $repository): Response
    {
    $reclamation = $repository->find($id);

    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush(); 
        $this->addFlash('success', 'Réclamation modifiée avec succès !');
        return $this->redirectToRoute('reclamation');
    }
    return $this->render('reclamation/modifier.html.twig', [
        'form' => $form->createView(),
        'reclamation' => $reclamation,
    ]);
}

    #[Route('/supprimer/{id}', name: 'supprimer_reclamation', methods: ['GET'])]
    public function supprimer(int $id, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find($id);
        if ($reclamation) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation supprimée avec succès');
        } else {
            $this->addFlash('error', 'Réclamation non trouvée');
        }
    
        return $this->redirectToRoute('reclamation');
    }

}
