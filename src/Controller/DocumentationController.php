<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DocumentationController extends AbstractController
{
    /**
     * Affiche la page de documentation de l'application.
     */
    #[Route('/documentation', name: 'documentation')]
    public function index(): Response
    {
        return $this->render('documentation/index.html.twig', [
            'controller_name' => 'DocumentationController',
        ]);
    }
}
