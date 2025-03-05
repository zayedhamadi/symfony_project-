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
use App\Repository\UserRepository;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'reclamation', methods: ['GET'])]
    public function index(ReclamationRepository $repository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $repository->findAll(),
        ]);
    }
    #[Route('/mes-reclamations', name: 'mes_reclamations', methods: ['GET'])]
public function mesReclamations(Request $request,ReclamationRepository $repository,UserRepository $userRepository): Response
{
    $session = $request->getSession();
        $userid = $session->get('user_id');
        $user = $userRepository->find($userid);

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réclamations.');
    }

    $reclamations = $repository->findBy(['user' => $user]);

    return $this->render('reclamation/mes_reclamations.html.twig', [
        'reclamations' => $reclamations,
    ]);
}
    #[Route('/ReclamationsParent', name: 'ReclamationsParent', methods: ['GET'])]
    public function ReclamationsParent(Request $request,ReclamationRepository $repository,UserRepository $userRepository): Response
    {
        $session = $request->getSession();
        $userid = $session->get('user_id');
        $user = $userRepository->find($userid);

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réclamations.');
        }

        $reclamations = $repository->findBy(['user' => $user]);

        return $this->render('reclamation/ReclamationsParent.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/ajouter', name: 'ajouter_reclamation', methods: ['GET','POST'])]
    public function ajouter(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        $session = $request->getSession();
        $userid = $session->get('user_id');
        $user = $userRepository->find($userid);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
    
        if ($form->isSubmitted()) {
            dump($form->getErrors());  // Debug validation errors
            
            if ($form->isValid()) {
                $reclamation->setDateDeCreation(new \DateTime()); 
                $reclamation->setStatut(Statut::EN_ATTENTE);
                $reclamation->setUser($user);
                
                $entityManager->persist($reclamation);
                $entityManager->flush();
                $this->addFlash('success', 'Votre réclamation a été envoyée avec succès !');
                return $this->redirectToRoute('mes_reclamations');
            }
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

    $form = $this->createForm(ReclamationType::class, $reclamation,[
        'only_statut' => true,]);
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




    #[Route('/parent_add_recl', name: 'parent_add_recl', methods: ['GET','POST'])]
    public function parent_add_recl(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        $session = $request->getSession();
        $userid = $session->get('user_id');
        $user = $userRepository->find($userid);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($form->isSubmitted()) {
            dump($form->getErrors());  // Debug validation errors

            if ($form->isValid()) {
                $reclamation->setDateDeCreation(new \DateTime());
                $reclamation->setStatut(Statut::EN_ATTENTE);
                $reclamation->setUser($user);

                $entityManager->persist($reclamation);
                $entityManager->flush();
                $this->addFlash('success', 'Votre réclamation a été envoyée avec succès !');
                return $this->redirectToRoute('ReclamationsParent');
            }
        }

        return $this->render('reclamation/parent_add_recl.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
