<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig_Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     *
     * @var Twig_Environment
     */
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return new Response($this->twig->render('Exception/error403.html.twig', []), 403);
    }
}
