<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealsController extends AbstractController
{
    #[Route('/', name: 'deals')]
    public function index(): Response
    {
        return $this->render('deals/index.html.twig', [
            'controller_name' => 'DealsController',
        ]);
    }
}
