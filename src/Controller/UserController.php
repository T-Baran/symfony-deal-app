<?php

namespace App\Controller;

use App\DTO\UpdateUser;
use App\Form\UserEmailType;
use App\Form\UserPasswordType;
use App\Form\UserUsernameType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    public function __construct(private UserManager $userManager, private Security $security, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/user', name: 'user_menu')]
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        $updateUser = $this->prepareData();
        $formEmail = $this->createForm(UserEmailType::class, $updateUser, [
            'action' => $this->generateUrl('user_email'),
        ]);
        $formUsername = $this->createForm(UserUsernameType::class, $updateUser, [
            'action' => $this->generateUrl('user_username'),
        ]);
        $formPassword = $this->createForm(UserPasswordType::class, $updateUser, [
            'action' => $this->generateUrl('user_password'),
        ]);

        return $this->renderForm('user/index.html.twig', [
            'user' => $user,
            'formEmail' => $formEmail,
            'formUsername' => $formUsername,
            'formPassword' => $formPassword
        ]);
    }

    #[Route('/user/username', name: 'user_username', methods: ['POST'])]
    public function editUsername(Request $request): Response
    {
        $this->validatePermission();
        return $this->saveCredentials($request, "username");
    }

    #[Route('/user/email', name: 'user_email', methods: ['POST'])]
    public function editEmail(Request $request): Response
    {
        $this->validatePermission();
        return $this->saveCredentials($request, "email");
    }

    #[Route('/user/password', name: 'user_password', methods: ['POST'])]
    public function editPassword(Request $request): Response
    {
        $this->validatePermission();
        return $this->saveCredentials($request, "password");
    }

    private function saveCredentials(Request $request, string $subject): Response
    {
        $updateUser = new UpdateUser();
        switch($subject){
            case "email":
                $form = $this->createForm(UserEmailType::class, $updateUser);
                break;
            case "username":
                $form = $this->createForm(UserUsernameType::class, $updateUser);
                break;
            case "password":
                $form = $this->createForm(UserPasswordType::class, $updateUser);
                break;
            default:
                return $this->redirectToRoute('user_menu');
        }
//        if ($subject === "email") {
//            $form = $this->createForm(UserEmailType::class, $updateUser);
//        } elseif ($subject === "username") {
//            $form = $this->createForm(UserUsernameType::class, $updateUser);
//        } else {
//            $form = $this->createForm(UserPasswordType::class, $updateUser);
//        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($subject === "password") {
                $oldPassword = $updateUser->oldPlainPassword;
                if (!$this->validatePassword($oldPassword)) {
                    $this->addFlash('failure', 'password.wrong');
                    return $this->redirectToRoute('user_menu');
                }
            }
            $this->userManager->transferAndSave($updateUser, $subject);
            switch($subject){
                case "email":
                    $this->addFlash('success', "update.email");
                    break;
                case "username":
                    $this->addFlash('success', "update.username");
                    break;
                case "password":
                    $this->addFlash('success', 'update.password');
                    break;
            }
//            if ($subject === "email") {
//                $this->addFlash('success', "update.email");
//            } elseif ($subject === "username") {
//                $this->addFlash('success', "update.username");
//            } else {
//                $this->addFlash('success', 'update.password');
//            }
        } else {
            $this->printErrors($form);
        }
        return $this->redirectToRoute('user_menu');
    }

    private function printErrors(FormInterface $form): void
    {
        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('failure', $error->getMessage());
        }
    }

    private function prepareData(): UpdateUser
    {
        $updateUser = new UpdateUser();
        $user = $this->security->getUser();
        $updateUser->email = $user->getEmail();
        $updateUser->username = $user->getUsername();
        return $updateUser;
    }

    public function validatePermission(): void
    {
        $user = $this->security->getUser();
        $this->denyAccessUnlessGranted('EDIT', $user);
    }

    private function validatePassword($password): bool
    {
        $user = $this->security->getUser();
        return $this->passwordHasher->isPasswordValid($user, $password);
    }
}
