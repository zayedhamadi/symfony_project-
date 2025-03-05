<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\InscriptionEvenement;
use App\Form\InscriptionEvenementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\EleveRepository;
use App\Controller\EvenementController;

#[Route('/inscription')]
class InscriptionEvenementController extends AbstractController
{
    #[Route('/reservations', name: 'reservations_evenement', methods: ['GET'])]
    public function afficherReservations(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {

    $session = $request->getSession();
    $userid = $session->get('user_id');
    if (!$userid) {
        $this->addFlash('danger', 'Vous devez être connecté pour voir vos réservations.');
        return $this->redirectToRoute('app_login'); 
    }
    $user = $userRepository->find($userid);
    if (!$user) {
        $this->addFlash('danger', 'Utilisateur non trouvé.');
        return $this->redirectToRoute('app_login');
    }
    $reservations = $entityManager->getRepository(InscriptionEvenement::class)->findBy([
        'enfant' => $user->getEleves()->toArray()  
    ]);
    return $this->render('inscription_evenement/reservations.html.twig', [
        'reservations' => $reservations
    ]);
    }




    #[Route('/{id}', name: 'inscription_evenement', methods: ['GET', 'POST'])]
    public function inscrire(
        Request $request,
        Evenement $evenement,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        
        $session = $request->getSession();
        $userid = $session->get('user_id');
    
        if (!$userid) {
            $this->addFlash('danger', 'Vous devez être connecté pour inscrire un enfant.');
            return $this->redirectToRoute('app_login');
        }
    
        
        $user = $userRepository->find($userid);
        if (!$user) {
            $this->addFlash('danger', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_login');
        }
    
        
        $enfants = $user->getEleves();
        if ($enfants->isEmpty()) {
            $this->addFlash('danger', 'Vous n\'avez pas d\'enfants enregistrés.');
            return $this->redirectToRoute('reservations_evenement');
        }
    
        
        $inscription = new InscriptionEvenement();
        $inscription->setEvenement($evenement);
        $inscription->setDateInscription(new \DateTime());
    
        
        $form = $this->createForm(InscriptionEvenementType::class, $inscription, [
            'enfants' => $enfants,
        ]);
        $form->handleRequest($request);
    
        // Si le formulaire est soumis mais invalide (captcha incorrect)
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le captcha est incorrect. Veuillez réessayer.');
            return $this->redirectToRoute('inscription_evenement', ['id' => $evenement->getId()]);
        }
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            
            $enfant = $form->get('enfant')->getData();
    
            
            if (!$enfant) {
                $this->addFlash('danger', 'Veuillez sélectionner un enfant.');
                return $this->redirectToRoute('inscription_evenement', ['id' => $evenement->getId()]);
            }
    
            
            $inscription->setEnfant($enfant);
    
            
            $dejaInscrit = $entityManager->getRepository(InscriptionEvenement::class)->findOneBy([
                'evenement' => $evenement,
                'enfant' => $enfant
            ]);
    
            if ($dejaInscrit) {
                $this->addFlash('warning', 'Cet enfant est déjà inscrit à cet événement.');
                return $this->redirectToRoute('reservations_evenement');
            }
    
           
            if ($evenement->isInscriptionRequise() && $evenement->getNombrePlaces() !== null) {
                $nombreInscriptions = $entityManager->getRepository(InscriptionEvenement::class)->count(['evenement' => $evenement]);
                if ($nombreInscriptions >= $evenement->getNombrePlaces()) {
                    $this->addFlash('danger', 'Il n\'y a plus de places disponibles pour cet événement.');
                    return $this->redirectToRoute('reservations_evenement');
                }
            }
    
            
            $entityManager->persist($inscription);
            $entityManager->flush();
    
            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('reservations_evenement');
        }
    
        return $this->render('inscription_evenement/new.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement
        ]);
    }


        #[Route('/reservations/{id}/supprimer', name: 'supprimer_reservation', methods: ['POST'])]
    public function supprimerReservation(
        InscriptionEvenement $inscription,
        EntityManagerInterface $entityManager
    ): Response {

        if (!$inscription) {
            $this->addFlash('danger', 'Réservation introuvable.');
            return $this->redirectToRoute('reservations_evenement');
        }

        $entityManager->remove($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation supprimée avec succès.');

        return $this->redirectToRoute('reservations_evenement');
    }


    #[Route('/reservattions/{id}/supprimer', name: 'supp_reservation', methods: ['POST'])]
    public function suppReservation(
        InscriptionEvenement $inscription,
        EntityManagerInterface $entityManager
    ): Response {
        // if (!$inscription) {
        //     $this->addFlash('danger', 'Réservation introuvable.');
        //     return $this->redirectToRoute('reservations_evenement');
        // }

        $entityManager->remove($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation supprimée avec succès.');

        
        return $this->redirectToRoute('evenement_index');
    }



    #[Route('/evenement/{id}/inscriptions', name: 'consulter_inscriptions', methods: ['GET'])]
    public function consulterInscriptions(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $inscriptions = $entityManager->getRepository(InscriptionEvenement::class)->findBy([
            'evenement' => $evenement
        ]);

        return $this->render('inscription_evenement/consulter.html.twig', [
            'evenement' => $evenement,
            'inscriptions' => $inscriptions
        ]);
    }



}