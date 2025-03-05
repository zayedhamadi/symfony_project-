<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\StatistiqueService;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatistiqueController extends AbstractController
{
    private StatistiqueService $statistiqueService;

    #[Route('/stats', name: 'stats_dashboard')]
    public function statsDashboard(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('user/Statistique.html.twig');
    }


    public function __construct(StatistiqueService $statistiqueService)
    {
        $this->statistiqueService = $statistiqueService;
    }

    #[Route('/stats/users-by-role', name: 'stats_users_by_role')]
    public function usersByRole(): JsonResponse
    {
        return new JsonResponse($this->statistiqueService->getUsersByRole());
    }


    #[Route('/stats/users-by-gender', name: 'stats_users_by_gender')]
    public function usersByGender(): JsonResponse
    {
        return new JsonResponse($this->statistiqueService->getUsersByGender());
    }

    #[Route('/stats/reclamations-by-month', name: 'stats_reclamations_by_month')]
    public function reclamationsByMonth(): JsonResponse
    {
        return new JsonResponse($this->statistiqueService->getReclamationsByMonth());
    }
}