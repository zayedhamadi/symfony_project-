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

#[Route('/eleve')]  
final class EleveController extends AbstractController  
{  
    #[Route(name: 'app_eleve_index', methods: ['GET'])]  
    public function index(EleveRepository $eleveRepository): Response  
    {  
        return $this->render('eleve/index.html.twig', [  
            'eleves' => $eleveRepository->findAll(),  
        ]);  
    }

        #[Route('/elvvv',name: 'showeleve', methods: ['GET'])]
    public function showeleve(EleveRepository $eleveRepository): Response
    {
        return $this->render('eleve/consultermoneleve.html.twig', [
            'eleves' => $eleveRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_eleve_new', methods: ['GET', 'POST'])]  
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response  
    {  
        $eleve = new Eleve();  
        $form = $this->createForm(EleveType::class, $eleve);  
        $form->handleRequest($request);  
    
        if ($form->isSubmitted() && $form->isValid()) {  
            $entityManager->persist($eleve);  
            $entityManager->flush();  
    
            // Envoi d'un email au parent  
            $parentEmail = $eleve->getIdParent()->getEmail(); // Récupérer l'email du parent  
    
            // Vérifiez si l'élève a une classe associée avant de récupérer le nom  
            if ($eleve->getIdClasse()) {  
                $classeNom = $eleve->getIdClasse()->getNomClasse(); // Récupérer le nom de la classe  
            } else {  
                $classeNom = 'Inconnue'; // Valeur par défaut si aucune classe n'est associée  
            }  
    
            $email = (new Email())  
                ->from('noreply@example.com') // Remplacez par l'adresse de l'expéditeur  
                ->to($parentEmail)  
                ->subject('Inscription d\'un nouvel élève')  
                ->text('Bonjour, un nouvel élève a été inscrit : ' . $eleve->getNom() . ' ' . $eleve->getPrenom() . '. Inscrit dans la classe : ' . $classeNom . '.');  
    
            // Envoyer l'email  
            $mailer->send($email);  
    
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