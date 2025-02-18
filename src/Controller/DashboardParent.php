<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardParent extends AbstractController
{
    #[Route('/dashboard/parent', name: 'app_parent')]
    public function index(): Response
    {
        return $this->render('dashboardparent.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
