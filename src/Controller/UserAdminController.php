<?php

namespace App\Controller;

use App\DTO\UpdateUser;
use App\Entity\User;
use App\Form\UserRoleType;
use App\Service\AdminManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class UserAdminController extends AbstractController
{

    public function __construct(private AdminManager $adminManager)
    {
    }

    #[Route('/user/{id}/role', name: 'user_role', methods: ['POST'])]
    public function editRole(User $user, Request $request): Response
    {
        $req = $request->request->all();
        if ($this->isCsrfTokenValid('edit-role-user', $req['updateUser']["token"])) {
            $updateUser = new UpdateUser();
            $form = $this->createForm(UserRoleType::class, $updateUser);
            $form->submit($req['updateUser']);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->adminManager->roleTransferAndSave($user, $updateUser);
                $this->addFlash('success', 'role.change');
            } else {
                $this->addFlash('failure', 'again.later');
            }
        }
        return $this->redirectToRoute('admin_dashboard_users');
    }

    #[Route('/user/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(User $user, Request $request)
    {
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-user', $submittedToken)) {
            $this->adminManager->userDelete($user);
            $this->addFlash('success', 'delete.user');
        } else {
            $this->addFlash('failure', 'again.later');
        }
        return $this->redirect($request->request->get('referer'));
    }
}
