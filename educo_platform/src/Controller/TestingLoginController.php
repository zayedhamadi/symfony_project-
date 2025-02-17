<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TestingLoginController extends AbstractController
{
    #[Route('/testing/login', name: 'app_testing_login')]
    public function index(): Response
    {
        return $this->render('testing_login/index.html.twig', [
            'controller_name' => 'TestingLoginController',
        ]);
    }
}
