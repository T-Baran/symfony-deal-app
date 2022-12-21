<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_menu')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/username', name: 'user_username', methods: ['POST'])]
    public function editUsername(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $user);
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('edit-username', $submittedToken)) {
            $user->setUsername($request->request->get('username'));
            $em->flush();
            $this->addFlash('success', 'update.username');
            return $this->redirectToRoute('user_menu');
        }
        $this->addFlash('failure', 'again.later');
        return $this->redirectToRoute('user_menu');
    }

    #[Route('/user/{id}/email', name: 'user_email', methods: ['POST'])]
    public function editEmail(User $user, Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $user);
        $submittedToken = $request->request->get('token');
        $submittedEmail = $request->request->get('email');
        if (!empty($userRepository->findByEmail($submittedEmail))) {
            $this->addFlash('failure', 'email.used');
        } elseif ($this->isCsrfTokenValid('edit-email', $submittedToken)) {
            $user->setEmail($request->request->get('email'));
            $em->flush();
            $this->addFlash('success', 'update.email');
        } else {
            $this->addFlash('failure', 'again.later');
        }
        return $this->redirectToRoute('user_menu');
    }

    #[Route('/user/{id}/password', name: 'user_password', methods: ['POST'])]
    public function editPassword(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $user);
        if ($request->request->get('password') !== $request->request->get('repeatedPassword')) {
            $this->addFlash('failure', "not.match.password");
            return $this->redirectToRoute('user_menu');
        }
        $submittedToken = $request->request->get('token');
        $submittedPassword = $request->request->get('password');

        if ($this->isCsrfTokenValid('edit-password', $submittedToken) && $passwordHasher->isPasswordValid($user, $request->request->get('oldPassword'))) {
            $hashedPassword = $passwordHasher->hashPassword($user, $submittedPassword);
            $user->setPassword($hashedPassword);
            $em->flush();
            $this->addFlash('success', 'update.password');
            return $this->redirectToRoute('user_menu');
        }
        $this->addFlash('failure', 'again.later');
        return $this->redirectToRoute('user_menu');
    }
}
