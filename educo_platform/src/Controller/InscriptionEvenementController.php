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

#[Route('/inscription')]
class InscriptionEvenementController extends AbstractController
{
    #[Route('/{id}', name: 'inscription_evenement', methods: ['GET', 'POST'])]
    public function inscrire(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $enfants = $user->getEleves(); // Récupérer les enfants du parent connecté

        if (!$enfants) {
            $this->addFlash('danger', 'Vous n\'avez pas d\'enfants enregistrés.');
            return $this->redirectToRoute('evenement_index');
        }

        $inscription = new InscriptionEvenement();
        $inscription->setEvenement($evenement); // 🔹 Associer l'événement
        $inscription->setDateInscription(new \DateTime()); // 🔹 Ajouter la date d'inscription

        // Création du formulaire avec la liste des enfants
        $form = $this->createForm(InscriptionEvenementType::class, $inscription, [
            'enfants' => $enfants,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'enfant est déjà inscrit
            $enfant = $inscription->getEnfant();
            $dejaInscrit = $entityManager->getRepository(InscriptionEvenement::class)->findOneBy([
                'evenement' => $evenement,
                'enfant' => $enfant
            ]);

            if ($dejaInscrit) {
                $this->addFlash('warning', 'Cet enfant est déjà inscrit à cet événement.');
                return $this->redirectToRoute('evenement_index');
            }

            // Vérifier s'il y a encore des places disponibles
            if ($evenement->isInscriptionRequise() && $evenement->getNombrePlaces() !== null) {
                $nombreInscriptions = $entityManager->getRepository(InscriptionEvenement::class)->count(['evenement' => $evenement]);
                if ($nombreInscriptions >= $evenement->getNombrePlaces()) {
                    $this->addFlash('danger', 'Il n\'y a plus de places disponibles pour cet événement.');
                    return $this->redirectToRoute('evenement_index');
                }
            }

            // Enregistrer l'inscription
            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('evenement_index');
        }

        return $this->render('inscription_evenement/new.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement
        ]);
    }
}
