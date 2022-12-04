<?php

namespace App\Controller;

use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/user/dashboard')]
class UserDashboardController extends AbstractController
{
    #[Route('/', name: 'user_dashboard')]
    public function index(): Response
    {
        return $this->render('user_dashboard/index.html.twig', [
            'controller_name' => 'UserDashboardController',
        ]);
    }
    #[Route('/deals', name:'user_dashboard_deals')]
    public function deal(DealRepository $dealRepository, UserInterface $user): Response
    {

//        dd($user->getId());
        $deals = $dealRepository->findBy([
            'user'=>$user->getId(),
        ]);

        return $this->render('user_dashboard/deals.html.twig',[
            'deals'=>$deals,
        ]);
    }
}
