<?php

declare(strict_types=1);

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class OpinionController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return new Response(
            '<html><body>Home Page</body></html>'
        );
    }
}
