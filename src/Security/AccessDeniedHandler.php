<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // Store the message in the session
        $request->getSession()->getFlashBag()->add('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        
        // Redirect to home page or a dedicated access denied page
        return new RedirectResponse($this->router->generate('app_access_denied'));
    }
}
