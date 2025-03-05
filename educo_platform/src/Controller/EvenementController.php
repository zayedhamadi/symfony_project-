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
use App\Service\SmsService;
use App\Entity\InscriptionEvenement;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Twilio\Rest\Client;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;




class EvenementController extends AbstractController
{
    #[Route('/evenement/filter-by-type', name: 'event_filter_by_type')]
public function filterEventsByType(Request $request, EvenementRepository $eventRepository, NormalizerInterface $normalizer): JsonResponse
{
    // Récupérer le type sélectionné depuis la requête
    $eventType = $request->query->get('type');

    // Récupérer les événements filtrés par type
    $events = $eventRepository->findByType($eventType);

    // Normaliser les données en JSON
    $jsonContent = $normalizer->normalize($events, 'json', ['groups' => 'events']);

    foreach ($jsonContent as &$event) {
        if (isset($event['dateDebut'])) {
            $event['dateDebut'] = (new \DateTime($event['dateDebut']))->format('Y-m-d H:i');
        }
        if (isset($event['dateFin'])) {
            $event['dateFin'] = $event['dateFin'] ? (new \DateTime($event['dateFin']))->format('Y-m-d H:i') : null;
        }
        if ($event['nombrePlaces'] === null) {
            $event['nombrePlaces'] = "";
        }}
    return new JsonResponse($jsonContent);
}

    #[Route('/evenement/search', name: 'event_search')]
    public function searchEvent(Request $request, NormalizerInterface $normalizer, EvenementRepository $eventRepository): JsonResponse
    {
        $searchValue = $request->get('searchValue');
        $events = $eventRepository->findEventByTitre($searchValue);
    
        // Normaliser les événements avec le groupe 'events'
        $jsonContent = $normalizer->normalize($events, 'json', ['groups' => 'events']);
    
        // Formater les dates manuellement
        foreach ($jsonContent as &$event) {
            if (isset($event['dateDebut'])) {
                $event['dateDebut'] = (new \DateTime($event['dateDebut']))->format('Y-m-d H:i');
            }
            if (isset($event['dateFin'])) {
                $event['dateFin'] = $event['dateFin'] ? (new \DateTime($event['dateFin']))->format('Y-m-d H:i') : null;
            }
            if ($event['nombrePlaces'] === null) {
                $event['nombrePlaces'] = "";
            }
        }

    
        return new JsonResponse($jsonContent);
    }

    #[Route('/evenement', name: 'evenement_index', methods: ['GET'])]
public function index(EvenementRepository $evenementRepository): Response
{
    // Récupérer tous les événements
    $evenements = $evenementRepository->findAll();

    // Récupérer les types d'événements disponibles
    $eventTypes = EventType::cases(); // Si EventType est une énumération
    $eventTypes = array_map(fn($type) => $type->value, $eventTypes); // Convertir en tableau de valeurs

    return $this->render('evenement/index.html.twig', [
        'evenements' => $evenements,
        'eventTypes' => $eventTypes, // Passer les types disponibles
        'selectedType' => null, // Aucun type sélectionné par défaut
    ]);
}
    #[Route('/evenement/liste', name: 'evenement_liste', methods: ['GET'])]
public function liste(EvenementRepository $evenementRepository): Response
{
    // Récupérer tous les événements
    $evenements = $evenementRepository->findAll();

    // Récupérer les types d'événements disponibles
    $eventTypes = EventType::cases(); // Si EventType est une énumération
    $eventTypes = array_map(fn($type) => $type->value, $eventTypes); // Convertir en tableau de valeurs

    return $this->render('evenement/liste.html.twig', [
       'evenements' => $evenements,
        'eventTypes' => $eventTypes, // Passer les types disponibles
        'selectedType' => null, // Aucun type sélectionné par défaut
    ]);
}

#[Route('/evenement/new', name: 'evenement_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    
    $evenement = new Evenement();

    
    $form = $this->createForm(EvenementType::class, $evenement);

    
    $form->handleRequest($request);

    
    if ($form->isSubmitted()) {
        
        if (!$form->isValid()) {
            
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } else {
           
            $entityManager->persist($evenement);
            $entityManager->flush();

            
            $this->addFlash('success', 'L\'événement a été créé avec succès.');

            
            return $this->redirectToRoute('evenement_index');
        }
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

    #[Route('/evenement/{id}/delete', name: 'evenement_delete', methods: ['GET'])]
public function delete(
    int $id, 
    EntityManagerInterface $entityManager, 
    EvenementRepository $evenementRepository
): Response {
    // Récupérer l'événement à supprimer
    $evenement = $evenementRepository->find($id);

    if ($evenement) {
        // Récupérer les inscriptions liées à cet événement
        $inscriptions = $entityManager->getRepository(InscriptionEvenement::class)->findBy([
            'evenement' => $evenement
        ]);

        // Récupérer les parents concernés
        $parentsContactes = [];
        foreach ($inscriptions as $inscription) {
            $enfant = $inscription->getEnfant();
            $parent = $enfant->getIdParent(); // Si c'est la méthode correcte pour obtenir le parent

            if ($parent && $parent->getNumTel()) {
                $numeroParent = $parent->getNumTel();

                // Formater le numéro de téléphone pour ajouter l'identifiant international
                $numeroParent = $this->formatPhoneNumber($numeroParent);

                // Envoyer le SMS seulement si le parent n'a pas déjà été contacté
                if (!in_array($numeroParent, $parentsContactes)) {
                    $message = "L'événement '{$evenement->getTitre()}' a été annulé. Nous sommes désolés et vous remercions pour votre compréhension";

                    // Initialiser le client Twilio
                    $sid = 'ACcd3ce9565fdb4475a530755d657a0f8e'; // Votre SID Twilio
                    $token = '710d10826fa8cc9b64878d9504b2a7de'; // Votre token Twilio
                    $fromNumber = '+19035641839'; // Votre numéro Twilio

                    $twilio = new Client($sid, $token);

                    try {
                        // Envoyer le SMS via Twilio
                        $twilio->messages->create(
                            $numeroParent, // to
                            [
                                "body" => $message,
                                "from" => $fromNumber
                            ]
                        );

                        $this->addFlash('success', "SMS envoyé à $numeroParent");
                    } catch (\Exception $e) {
                        $this->addFlash('error', "Erreur lors de l'envoi du SMS à $numeroParent : " . $e->getMessage());
                    }

                    $parentsContactes[] = $numeroParent; // Ajouter le numéro pour éviter les doublons
                }
            }
        }

        // Supprimer l'événement
        $entityManager->remove($evenement);
        $entityManager->flush();
        $this->addFlash('success', 'Événement supprimé avec succès');
    } else {
        $this->addFlash('error', 'Événement non trouvé');
    }

    return $this->redirectToRoute('evenement_index');
}

/**
 * Formate un numéro de téléphone pour ajouter l'identifiant international +216.
 */
private function formatPhoneNumber(string $phoneNumber): string
{
    // Supprimer les espaces et les caractères non numériques
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Si le numéro commence par 0, le remplacer par +216
    if (strpos($phoneNumber, '0') === 0) {
        $phoneNumber = '+216' . substr($phoneNumber, 1);
    }

    // Si le numéro ne commence pas par +216, l'ajouter
    if (strpos($phoneNumber, '+216') !== 0) {
        $phoneNumber = '+216' . $phoneNumber;
    }

    return $phoneNumber;
}
}
