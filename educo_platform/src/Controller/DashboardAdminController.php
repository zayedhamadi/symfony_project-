<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardAdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    public function index(): Response
    {
        return $this->render('backoffice.html.twig', [
            'controller_name' => 'DashboardAdminController',
        ]);
    }
}

