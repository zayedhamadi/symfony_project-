<?php
namespace App\Security;

use App\Entity\Enum\Rolee;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private RouterInterface $router;


    public function __construct(JWTTokenManagerInterface $jwtManager, RouterInterface $router)
    {
        $this->jwtManager = $jwtManager;
        $this->router = $router;
    }

    /**
     * Méthode exécutée après une authentification réussie.
     * Cette méthode redirige l'utilisateur en fonction de son rôle.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();
        $jwt = $this->jwtManager->create($user);


        if (in_array(Rolee::Admin->value, $user->getRoles())) {
            $url = $this->router->generate('admin_dashboard');
        } elseif (in_array(Rolee::Enseignant->value, $user->getRoles())) {
            $url = $this->router->generate('EnseignantControllerr');
        } elseif (in_array(Rolee::Parent->value, $user->getRoles())) {
            $url = $this->router->generate('testtttController');
        } else {
            $url = $this->router->generate('app_dashboardAdmin');
        }

        return new RedirectResponse($url);
    }
}
