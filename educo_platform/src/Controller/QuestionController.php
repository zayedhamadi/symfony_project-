<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuestionType;  // Correct namespace for the form type

use App\Repository\QuestionRepository;
use App\Repository\QuizRepository; // Correct use statement
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/', name: 'question_index', methods: ['GET'])]
    public function index(QuestionRepository $questionRepository, QuizRepository $quizRepository): Response
    {
        // Fetch all quizzes (assuming you have a Quiz repository)
        $quizzes = $quizRepository->findAll();
        $questions = $questionRepository->findAll();

    
        return $this->render('question/index.html.twig', [
            'quizzes' => $quizzes,
            'questions' => $questions, 
        ]);
    }

    #[Route('/question/new/{quizId}', name: 'question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $quizId): Response
{
    // Fetch the quiz by ID
    $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);

    // Check if the quiz exists
    if (!$quiz) {
        throw $this->createNotFoundException('Quiz not found');
    }

    $question = new Question();
    // Set the quiz for the question
    $question->setQuiz($quiz);

    $form = $this->createForm(QuestionType::class, $question);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($question);
        $entityManager->flush();

        return $this->redirectToRoute('question_index');
    }

    return $this->render('question/new.html.twig', [
        'question' => $question,
        'form' => $form->createView(),
    ]);
}

    

    #[Route('/{id}', name: 'question_show', methods: ['GET'])]
    public function show(Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/{id}/edit', name: 'question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('question_index');
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index');
    }
}