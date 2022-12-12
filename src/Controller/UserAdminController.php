<?php

namespace App\Controller;

use App\Entity\User;
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
    #[Route('/user/{id}/role', name: 'user_role', methods: ['POST'])]
    public function editRole(User $user, EntityManagerInterface $em, Request $request): Response
    {
        $submittedToken = $request->request->get('token');
        if($this->isCsrfTokenValid('edit-role-user', $submittedToken)){
            $user->setRoles([$request->request->get('ROLE')]);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Role changed');
        }else{
            $this->addFlash('failure', 'Please try again later');
        }
        return $this->redirect($request->request->get('referer'));
    }
    #[Route('/user/{id}/delete',name: 'user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $em, Request$request )
    {
        $submittedToken = $request->request->get('token');
        if($this->isCsrfTokenValid('delete-user', $submittedToken)){
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User deleted');
        }else{
            $this->addFlash('failure', 'Please try again later');
        }
        return $this->redirect($request->request->get('referer'));
    }
}
