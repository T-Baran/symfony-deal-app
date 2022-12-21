<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\DealRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin_dashboard/index.html.twig');
    }

    #[Route('/deals', name: 'admin_dashboard_deals')]
    public function deals(DealRepository $dealRepository): Response
    {
        $deals = $dealRepository->findAll();
        return $this->render('admin_dashboard/deals.html.twig', [
            'deals' => $deals,
        ]);
    }

    #[Route('/comments', name: 'admin_dashboard_comments')]
    public function comments(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();
        return $this->render('admin_dashboard/comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/users', name: 'admin_dashboard_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin_dashboard/users.html.twig', [
            'users' => $users,
        ]);
    }
}
