<?php
namespace App\Security ;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler  implements AuthenticationSuccessHandlerInterface
{
    private $jwtManager;
    private $router;
    private $session;

    public function __construct(JWTTokenManagerInterface $jwtManager, RouterInterface $router, SessionInterface $session)
    {
        $this->jwtManager = $jwtManager;
        $this->router = $router;
        $this->session = $session;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        $jwtToken = $this->jwtManager->create($user);
        $userId = $user->getId();

        $this->session->set('jwt_token', $jwtToken);
        $this->session->set('user_id', $userId);

        return new RedirectResponse($this->router->generate('app_testing_login'));
    }
}

