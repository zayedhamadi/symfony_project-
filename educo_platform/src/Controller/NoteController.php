<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\QuizRepository;
use App\Repository\NoteRepository;


#[Route('/note')]
final class NoteController extends AbstractController
{
    #[Route('/stats', name: 'quiz_stats')]
    public function quizStats(QuizRepository $quizRepository, NoteRepository $noteRepository): Response
    {
        // Retrieve all quizzes
        $quizzes = $quizRepository->findAll();

        $stats = [];  // Initialize the stats array

        foreach ($quizzes as $quiz) {
            // Retrieve the notes for each quiz
            $notes = $noteRepository->findBy(['quiz' => $quiz]);

            // Calculate the statistics
            $totalEleves = count($notes);
            $totalReussite = count(array_filter($notes, fn($n) => $n->getScore() >= 10));
            $totalEchec = $totalEleves - $totalReussite;
            $moyenne = $totalEleves > 0 ? array_sum(array_map(fn($n) => $n->getScore(), $notes)) / $totalEleves : 0;

            // Add the statistics for this quiz to the stats array
            $stats[] = [
                'quiz' => $quiz,
                'totalEleves' => $totalEleves,
                'totalReussite' => $totalReussite,
                'totalEchec' => $totalEchec,
                'moyenne' => number_format($moyenne, 2),
            ];
        }

        // Check if stats contains data before passing to Twig
        if (empty($stats)) {
            // If no data is found, you can redirect or show a message
            return $this->render('note/stats.html.twig', [
                'stats' => null,  // Pass null if no data is available
            ]);
        }

        // Pass the stats to the view
        return $this->render('note/stats.html.twig', [
            'stats' => $stats,
        ]);
    }
}

