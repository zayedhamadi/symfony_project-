<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\EleveType;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
#[Route('/eleve')]
final class EleveController extends AbstractController
{
    #[Route(name: 'app_eleve_index', methods: ['GET'])]
    public function index(EleveRepository $eleveRepository, Request $request): Response
    {
      // Récupérer le terme de recherche depuis la requête
      $searchTerm = $request->query->get('search');
      $eleves = $eleveRepository->findAll(); // Récupérer tous les élèves  
      $nombreEleves = count($eleves); // Compter le nombre d'élèves  
      // Si un terme de recherche est présent, filtrer les élèves
      if ($searchTerm) {
          $eleves = $eleveRepository->findByNomAndPrenom($searchTerm);
      } else {
          // Sinon, afficher tous les élèves
          $eleves = $eleveRepository->findAll();
      }

      return $this->render('eleve/index.html.twig', [
          'eleves' => $eleves,
          'searchTerm' => $searchTerm, // Passer le terme de recherche à la vue
          'nombreEleves' => $nombreEleves, // Passer le nombre d'élèves au template  
      ]);
    }

   

//     #[Route('/new', name: 'app_eleve_new', methods: ['GET', 'POST'])]
// public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
// {
//     $eleve = new Eleve();
//     $form = $this->createForm(EleveType::class, $eleve);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         $entityManager->persist($eleve);
//         $entityManager->flush();

//         // Envoi d'un email au parent
//         $parentEmail = $eleve->getIdParent()->getEmail(); // Récupérer l'email du parent

//         // Vérifiez si l'élève a une classe associée avant de récupérer le nom
//         if ($eleve->getIdClasse()) {
//             $classeNom = $eleve->getIdClasse()->getNomClasse(); // Récupérer le nom de la classe
//         } else {
//             $classeNom = 'Inconnue'; // Valeur par défaut si aucune classe n'est associée
//         }

//         try {
//             $email = (new Email())
//                 ->from('noreply@example.com') // Remplacez par l'adresse de l'expéditeur
//                 ->to($parentEmail)
//                 ->subject('Inscription d\'un nouvel élève')
//                 ->text('Bonjour, un nouvel élève a été inscrit : ' . $eleve->getNom() . ' ' . $eleve->getPrenom() . '. Inscrit dans la classe : ' . $classeNom . '.');

//             // Envoyer l'email
//             $mailer->send($email);
//         } catch (\Exception $e) {
//             $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
//         }

//         return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
//     }

//     return $this->render('eleve/new.html.twig', [
//         'eleve' => $eleve,
//         'form' => $form,
//     ]);
// }


#[Route('/new', name: 'app_eleve_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator): Response
{
    $eleve = new Eleve();
    $form = $this->createForm(EleveType::class, $eleve);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persister l'élève dans la base de données pour obtenir un ID
        $entityManager->persist($eleve);
        $entityManager->flush();

        // Générer le QR code pour l'élève (après la persistance)
        try {
            // Générer l'URL absolue
            $data = $urlGenerator->generate('app_eleve_show', ['id' => $eleve->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            // Remplacer 127.0.0.1 par une adresse IP accessible ou un domaine public
            $data = str_replace('http://127.0.0.1:8000', 'http://192.168.1.100:8000', $data); // Exemple pour le réseau local
            // OU
            // $data = str_replace('http://127.0.0.1:8000', 'https://votredomaine.com', $data); // Exemple pour la production

            $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($data)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->build();

            // Convertir le QR code en URI de données
            $qrCodeDataUri = $qrCode->getDataUri();

            // Ajouter l'URI du QR code à l'objet Eleve
            $eleve->setQrCodeDataUri($qrCodeDataUri);

            // Mettre à jour l'élève dans la base de données avec le QR code
            $entityManager->persist($eleve);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la génération du QR Code : ' . $e->getMessage());
        }

        // Envoi d'un email au parent
        $parentEmail = $eleve->getIdParent()->getEmail(); // Récupérer l'email du parent

        // Vérifiez si l'élève a une classe associée avant de récupérer le nom
        if ($eleve->getIdClasse()) {
            $classeNom = $eleve->getIdClasse()->getNomClasse(); // Récupérer le nom de la classe
        } else {
            $classeNom = 'Inconnue'; // Valeur par défaut si aucune classe n'est associée
        }

        try {
            $email = (new Email())
                ->from('noreply@example.com') // Remplacez par l'adresse de l'expéditeur
                ->to($parentEmail)
                ->subject('Inscription d\'un nouvel élève')
                ->text('Bonjour, un nouvel élève a été inscrit : ' . $eleve->getNom() . ' ' . $eleve->getPrenom() . '. Inscrit dans la classe : ' . $classeNom . '.');

            // Envoyer l'email
            $mailer->send($email);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('eleve/new.html.twig', [
        'eleve' => $eleve,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_eleve_show', methods: ['GET'])]
    public function show(Eleve $eleve): Response
    {
        if (!$eleve) {
            throw $this->createNotFoundException('Élève non trouvé');
        }
       
        return $this->render('eleve/show.html.twig', [
            'eleve' => $eleve,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_eleve_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eleve_delete', methods: ['POST'])]
    public function delete(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eleve->getId(), $request->request->get('_token'))) {
            $entityManager->remove($eleve);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
    }
}