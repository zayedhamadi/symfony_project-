<?php

// src/Controller/EnseignantDashboardController.php
namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnseignantDashboardController extends AbstractController
{
    #[Route('/enseignant/dashboard', name: 'enseignant_dashboard')]
    public function index(QuizRepository $quizRepository, QuestionRepository $questionRepository): Response
    {
        // Fetch all quizzes and questions (without filtering by teacher)
        $quizzes = $quizRepository->findAll();
        $questions = $questionRepository->findAll();

        // Calculate statistics
        $totalQuizzes = count($quizzes);
        $totalQuestions = count($questions);

        return $this->render('dashboard_enseignant/index.html.twig', [
            'quizzes' => $quizzes,
            'questions' => $questions,
            'totalQuizzes' => $totalQuizzes,
            'totalQuestions' => $totalQuestions,
        ]);
    }
}
