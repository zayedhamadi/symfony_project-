<?php

namespace App\Controller;

use App\Entity\Enum\EtatCompte;
use App\Entity\User;
use DateTime;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class LoginController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function home(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/login', name: 'app_login')]
    public function login(
        AuthenticationUtils          $authenticationUtils,
        RefreshTokenManagerInterface $refreshTokenManager,
        JWTTokenManagerInterface     $jwtManager,
        SessionInterface             $session
    ): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            $token = $jwtManager->create($user);
            $userId = $user->getId();

            $refreshToken = $refreshTokenManager->create();
            $refreshToken->setRefreshToken();
            $refreshToken->setUsername($user->getUserIdentifier());
            $refreshToken->setValid(new DateTime('+1 month'));

            $refreshTokenManager->save($refreshToken);
            $session->set('jwt_token', $token);
            $session->set('user_id', $userId);
            $session->set('refresh_token', $refreshToken->getRefreshToken());

            return $this->redirectToRoute('app_testing_login');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername() ?: '';

        return $this->render('login/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }


    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}