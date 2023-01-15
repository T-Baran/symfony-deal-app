<?php

namespace App\Controller;

use App\Form\UserEmailType;
use App\Form\UserPasswordType;
use App\Form\UserUsernameType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_menu')]
    public function index(Security $security, Request $request, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();
        $formEmail = $this->createForm(UserEmailType::class, $user, [
            'action' => $this->generateUrl('user_email'),
            'method' => 'PATCH'
        ]);
        $formUsername = $this->createForm(UserUsernameType::class, $user, [
            'action' => $this->generateUrl('user_username'),
            'method' => 'PATCH'
        ]);
        $formPassword = $this->createForm(UserPasswordType::class, $user, [
            'action' => $this->generateUrl('user_password'),
            'method' => 'PATCH'
        ]);

        return $this->renderForm('user/index.html.twig', [
            'user' => $user,
            'formEmail' => $formEmail,
            'formUsername' => $formUsername,
            'formPassword' => $formPassword
        ]);
    }

    #[Route('/user/username', name: 'user_username', methods: ['POST'])]
    public function editUsername(Security $security, Request $request, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();
        $this->denyAccessUnlessGranted('EDIT', $user);
        $formUsername = $this->createForm(UserUsernameType::class, $user);
        $formUsername->handleRequest($request);
        if ($formUsername->isSubmitted() && $formUsername->isValid()) {
            $em->flush();
            $this->addFlash('success', 'update.username');
        } else {
            $this->printErrors($formUsername);
        }
        return $this->redirectToRoute('user_menu');
    }

    #[Route('/user/email', name: 'user_email', methods: ['POST'])]
    public function editEmail(Security $security, Request $request, EntityManagerInterface $em): Response
    {
        $user = $security->getUser();
        $this->denyAccessUnlessGranted('EDIT', $user);
        $formEmail = $this->createForm(UserEmailType::class, $user);
        $formEmail->handleRequest($request);
        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $em->flush();
            $this->addFlash('success', 'update.email');
        } else {
            $this->printErrors($formEmail);
        }
        return $this->redirectToRoute('user_menu');
    }

    #[Route('/user/password', name: 'user_password', methods: ['POST'])]
    public function editPassword(Security $security, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $security->getUser();
        $this->denyAccessUnlessGranted('EDIT', $user);
        $formPassword = $this->createForm(UserPasswordType::class, $user);
        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $oldPassword = $formPassword['oldPlainPassword']->getData();
            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                $newPassword = $formPassword['newPlainPassword']->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $em->flush();
                $this->addFlash('success', 'update.password');
            } else {
                $this->addFlash('failure', 'password.wrong');
            }
        } else {
            $this->printErrors($formPassword);

        }
        return $this->redirectToRoute('user_menu');
    }

    public function printErrors(FormInterface $form): void
    {
        foreach ($form->getErrors() as $error) {
            $this->addFlash('failure', $error->getMessage());
        }
    }
}
