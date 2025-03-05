<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Quiz;
use App\Entity\Note;  // Assurez-vous que cet import est présent en haut du contrôleur

use App\Form\QuizType;
use App\Service\PdfExtractorService;
use App\Service\QuizGeneratorService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\EmailNotification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Mailer\MailerInterface;  
use Symfony\Component\Mime\Email;  

#[Route('/quiz')]
class QuizController extends AbstractController
{
    #[Route('/', name: 'quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();


  // Debugging avant l'envoi du mail
  dump($quiz->getClasse()); // Vérifie que le quiz a une classe
  dump($quiz->getClasse()?->getEleves()); // Vérifie les élèves de la classe
  
  foreach ($quiz->getClasse()?->getEleves() as $eleve) {
      dump($eleve->getIdParent()); // Vérifie que l'élève a un parent
      dump($eleve->getIdParent()?->getEmail()); // Vérifie l'email du parent
  }
              // Logique d'envoi du mail
        foreach ($quiz->getClasse()?->getEleves() as $eleve) {
            $parentEmail = $eleve->getIdParent()?->getEmail();

            if ($parentEmail) {
                $email = (new Email())
                    ->from('noreply@example.com')
                    ->to($parentEmail)
                    ->subject('Nouveau Quiz Disponible')
                    ->text('Un nouveau quiz a été ajouté pour la classe ' . $quiz->getClasse()->getNomClasse());

                $mailer->send($email);
            }
        }

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/edit', name: 'quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quiz_index');
    }


}