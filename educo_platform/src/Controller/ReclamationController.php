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
use App\Service\MailService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation/filter-by-statut', name: 'reclamation_filter_by_statut', methods: ['GET'])]
public function filterReclamationsByStatut(
    Request $request,
    ReclamationRepository $reclamationRepository,
    UserRepository $userRepository,
    NormalizerInterface $normalizer
): JsonResponse {
    // Récupérer l'utilisateur connecté
    $session = $request->getSession();
    $userId = $session->get('user_id');
    $user = $userRepository->find($userId);

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette fonctionnalité.');
    }

    // Récupérer le statut sélectionné
    $statut = $request->query->get('statut');

    // Vérifier si l'utilisateur est un administrateur
    $isAdmin = in_array('Admin', $user->getRoles());

    // Filtrer les réclamations
    if ($isAdmin) {
        // Pour l'administrateur : récupérer toutes les réclamations (filtrées par statut si nécessaire)
        $reclamations = $statut
            ? $reclamationRepository->findBy(['statut' => $statut])
            : $reclamationRepository->findAll();
    } else {
        // Pour les utilisateurs normaux : récupérer uniquement les réclamations de l'utilisateur connecté
        $reclamations = $statut
            ? $reclamationRepository->findBy(['user' => $user, 'statut' => $statut])
            : $reclamationRepository->findBy(['user' => $user]);
    }

    // Normaliser les données pour les renvoyer en JSON
    $jsonContent = $normalizer->normalize($reclamations, 'json', ['groups' => 'reclamation']);

    // Formater la date de création
    foreach ($jsonContent as &$reclamation) {
        if (isset($reclamation['dateDeCreation'])) {
            $reclamation['dateDeCreation'] = (new \DateTime($reclamation['dateDeCreation']))->format('d/m/Y');
        }
    }

    return new JsonResponse($jsonContent);
}
#[Route('/reclamation', name: 'reclamation', methods: ['GET'])]
public function index(ReclamationRepository $reclamationRepository, Request $request): Response
{
    
    $selectedStatut = $request->query->get('statut');

    
    $statuts = Statut::cases(); 
    $statuts = array_map(fn($statut) => $statut->value, $statuts); 

    
    $reclamations = $selectedStatut
        ? $reclamationRepository->findBy(['statut' => $selectedStatut])
        : $reclamationRepository->findAll();

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
        'statuts' => $statuts, 
        'selectedStatut' => $selectedStatut, // Passer le statut sélectionné
    ]);
}
#[Route('/mes-reclamations', name: 'mes_reclamations', methods: ['GET'])]
public function mesReclamations(Request $request, ReclamationRepository $repository, UserRepository $userRepository): Response
{
    $session = $request->getSession();
    $userid = $session->get('user_id');
    $user = $userRepository->find($userid);

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos réclamations.');
    }

    // Récupérer le statut sélectionné (s'il existe)
    $selectedStatut = $request->query->get('statut');

    // Récupérer les réclamations de l'utilisateur, filtrées par statut si nécessaire
    $reclamations = $selectedStatut
        ? $repository->findBy(['user' => $user, 'statut' => $selectedStatut])
        : $repository->findBy(['user' => $user]);

    // Récupérer tous les statuts disponibles
    $statuts = Statut::cases(); // Récupère tous les cas de l'enum Statut
    $statuts = array_map(fn($statut) => $statut->value, $statuts); // Convertit en tableau de valeurs

    return $this->render('reclamation/mes_reclamations.html.twig', [
        'reclamations' => $reclamations,
        'statuts' => $statuts,
        'selectedStatut' => $selectedStatut,
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

        // Récupérer le statut sélectionné (s'il existe)
    $selectedStatut = $request->query->get('statut');

    // Récupérer les réclamations de l'utilisateur, filtrées par statut si nécessaire
    $reclamations = $selectedStatut
        ? $repository->findBy(['user' => $user, 'statut' => $selectedStatut])
        : $repository->findBy(['user' => $user]);
        // Récupérer tous les statuts disponibles
    $statuts = Statut::cases(); // Récupère tous les cas de l'enum Statut
    $statuts = array_map(fn($statut) => $statut->value, $statuts); // Convertit en tableau de valeurs

        return $this->render('reclamation/ReclamationsParent.html.twig', [
            'reclamations' => $reclamations,
            'statuts' => $statuts, 
            'selectedStatut' => $selectedStatut,
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
public function modifier(
    int $id, 
    Request $request, 
    EntityManagerInterface $entityManager, 
    ReclamationRepository $repository, 
    MailService $mailService
): Response {
    $reclamation = $repository->find($id);

    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    $form = $this->createForm(ReclamationType::class, $reclamation, [
        'only_statut' => true,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Mise à jour du statut
        //$reclamation->setStatut(Statut::TRAITEE); // Utilise l'Enum Statut
        $entityManager->flush(); 

        // Vérifie si le statut est bien "Traitée" avant d'envoyer l'email
        if ($reclamation->getStatut() === Statut::TRAITEE) {
            $mailService->envoyerEmail(
                $reclamation->getUser()->getEmail(),
                "Votre réclamation a été traitée",
                "Bonjour, votre réclamation (#{$reclamation->getTitre()}) a été traitée.  La réponse détaillée vous a été envoyée par e-mail. Merci de vérifier votre boîte de réception."
            );
        }

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
