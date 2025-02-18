<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/gestion/classe')]
final class GestionClasseController extends AbstractController
{
    #[Route(name: 'app_gestion_classe_index', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository): Response
    {
        return $this->render('gestion_classe/index.html.twig', [
            'classes' => $classeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestion_classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classe);
            $entityManager->flush();
    
            // Récupérer les enseignants associés à la classe
            foreach ($classe->getIdUser() as $enseignant) {
                $email = (new Email())
                    ->from('admin@votre-ecole.com')
                    ->to($enseignant->getEmail())
                    ->subject('Nouvelle Classe Assignée')
                    ->text("Bonjour " . $enseignant->getNom() . ",\n\nUne nouvelle classe vous a été attribuée : " . $classe->getNomClasse());
                
                $mailer->send($email);
            }
    
            return $this->redirectToRoute('app_gestion_classe_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('gestion_classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_gestion_classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        return $this->render('gestion_classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestion_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_classe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gestion_classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestion_classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($classe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestion_classe_index', [], Response::HTTP_SEE_OTHER);
    }
}
