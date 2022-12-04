<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Deal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
    #[Route('/comment/create/{id}', name: 'comment_create', methods: ['POST'])]
    public function create($id, Request $request, Deal $deal, Security $security, EntityManagerInterface $em): Response
    {

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('create-comment', $submittedToken)) {
            $comment = new Comment();
            $comment->setUser($security->getUser());
            $comment->setContent($request->request->get('content'));
//            dd($security->getUser());
            $em->persist($comment);
            $deal->addComment($comment);
            $em->persist($deal);

            $em->flush();

            return $this->redirectToRoute('deal_show',[
                'id' => $id,
            ]);
        }
        return new Exception('Wrong CSRF Token');

    }
    #[Route('/comment/edit/{id}', name:'comment_edit', methods: ['POST'])]
    public function edit ($id, Request $request, Comment $comment, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('EDIT', $comment);

        $submittedToken = $request->request->get('token');
//        dd($this->isCsrfTokenValid('edit-comment', $submittedToken));
        if ($this->isCsrfTokenValid('edit-comment', $submittedToken)) {
            $comment->setContent($request->request->get('content'));
            $em->persist($comment);
            $em->flush();
//            return $this->redirectToRoute('deal_show',[
//                'id' => $id,
//            ]);
        }
        return $this->redirectToRoute('deal_show',[
            'id' => $comment->getDeal()->getId(),
        ]);

    }

    #[Route('/comment/delete/{id}', name: 'comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $comment);
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('deal_show',[
            'id'=>$comment->getDeal()->getId(),
        ]);
    }
}
