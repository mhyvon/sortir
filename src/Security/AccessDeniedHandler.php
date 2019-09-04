<?php


namespace App\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    /**
     * Handles an access denied failure.
     *
     * @return Response|null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $content='<img src="https://i1.wp.com/archersduroyrene.fr/wp-content/uploads/2015/10/acces-refuse-e1446194297940.jpg" alt="Accès refusé">';
        return new Response($content, 403);
    }
}