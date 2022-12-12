<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_menu')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        dd($user);
        return $this->render('user/index.html.twig', [
            'user'=>$user,
        ]);
    }
}
