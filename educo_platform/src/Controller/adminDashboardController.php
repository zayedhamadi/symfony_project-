<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class adminDashboardController extends AbstractController
{
    #[Route('/testing/login', name: 'app_dashboardAdmin')]
    public function indexDashboardAdmin(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();

        $userId = $session->get('user_id');

        return $this->render('Admin/index.html.twig', [
            'user_id' => $userId,
        ]);
    }

    #[Route('/admin/statistics', name: 'app_admin_statistics')]
    public function statistics(UserRepository $userRepository)
    {
        $genderStats = $userRepository->countUsersByGender();

        dump($genderStats);

        return $this->render('Admin/index.html.twig', [
            'genderStats' => $genderStats
        ]);
    }

}
