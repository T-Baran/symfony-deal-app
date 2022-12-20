<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/user/dashboard')]
class UserDashboardController extends AbstractController
{
    #[Route('/', name: 'user_dashboard')]
    public function index(): Response
    {
        return $this->render('user_dashboard/index.html.twig');
    }
    #[Route('/deals', name:'user_dashboard_deals')]
    public function deals(DealRepository $dealRepository, UserInterface $user): Response
    {
        $deals = $dealRepository->findBy([
            'user'=>$user->getId(),
        ]);
        return $this->render('user_dashboard/deals.html.twig',[
            'deals'=>$deals,
        ]);
    }
    #[Route('/comments', name:'user_dashboard_comments')]
    public function comments(CommentRepository $commentRepository, UserInterface $user): Response
    {
        $comments = $commentRepository->findBy([
            'user'=>$user->getId(),
        ]);
        return $this->render('user_dashboard/comments.html.twig',[
            'comments'=>$comments,
        ]);
    }
}
