<?php

namespace App\Security;

use http\Env\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    use TargetPathTrait;

    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token):\Symfony\Component\HttpFoundation\Response
    {
        $user = $token->getUser();
        if ($user->isSuspendu()) {
            return new RedirectResponse($this->router->generate('user_suspended_route'));
        }

        // Récupérer la cible de redirection prévue, ou utiliser une redirection par défaut
        $targetPath = $this->getTargetPath($request->getSession(), 'main');
        if ($targetPath !== null) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('home'));
    }
}