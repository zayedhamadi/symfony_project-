<?php

namespace App\Controller;


use App\Entity\Enum\Rolee;
use App\Entity\User;
use DateTime;
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
            if ($user->getEtatCompte()?->name === 'inactive') {
                $this->addFlash('danger', 'Votre compte est inactif. Contactez l’administrateur.');
                return $this->redirectToRoute('app_login');
            }
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


            if (in_array(Rolee::Admin->value, $user->getRoles())) {
                return $this->redirectToRoute('admin_dashboard');
            } elseif (in_array(Rolee::Enseignant->value, $user->getRoles())) {
                return $this->redirectToRoute('EnseignantControllerr');
            } elseif (in_array(Rolee::Parent->value, $user->getRoles())) {
                return $this->redirectToRoute('testtttController');
            }
            return $this->redirectToRoute('app_dashboardAdmin');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername() ?: '';
        dump($session->getId());
        dump($session->get('jwt_token'));
        dump($session->get('user_id'));
        return $this->render('login/login.html.twig', [
            'last_email' => $lastEmail,
            'user_id' => $session->get('user_id'),
        ]);
    }


    #[Route('/logout', name: 'app_logout', methods: ['POST','GET'])]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        $session->invalidate();
        return $this->redirectToRoute('app_login');
    }

}