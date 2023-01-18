<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Deal;
use App\Form\CommentType;
use App\Service\CommentManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
    public function __construct(private CommentManager $commentManager, private Security $security)
    {
    }

    #[Route('/comment/create/{id}', name: 'comment_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Deal $deal, Request $request): Response
    {
        $this->save($request,null,$deal);
        return $this->redirectToRoute('deal_show', [
            'id' => $deal->getId(),
        ]);

    }

    #[Route('/comment/edit/{id}', name: 'comment_edit', methods: ['POST'])]
    public function edit(Comment $comment, Request $request): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $comment);
        $req = $request->request->all();
        $this->save($request,$comment);
        return $this->redirect($req['comment']['referer']);
    }

    #[Route('/comment/delete/{id}', name: 'comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $comment);
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('comment', $submittedToken)) {
            $this->commentManager->delete($comment);
            $this->addFlash('success', 'delete.comment');
        } else {
            $this->addFlash('failure', "again.later");
        }
        return $this->redirect($request->request->get('referer'));
    }

    private function printErrors(FormInterface $form): void
    {
        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('failure', $error->getMessage());
        }
    }

    private function save(Request $request, ?Comment $comment, ?Deal $deal=null)
    {
        $req = $request->request->all();
        if ($this->isCsrfTokenValid('comment', $req['comment']['token'])) {
            if($deal !== null){
                $comment = new Comment();
                $comment->setUser($this->security->getUser());
                $deal->addComment($comment);
            }
            $form = $this->createForm(CommentType::class, $comment);
            $form->submit($req['comment']);
            if($form->isSubmitted() && $form->isValid()){
                if($deal !== null){
                    $this->commentManager->save($comment, $deal);
                    $this->addFlash('success', 'add.comment');
                }else{
                    $this->commentManager->save($comment);
                    $this->addFlash('success', 'update.comment');
                }
            }else{
                $this->printErrors($form);
            }
        } else {
            $this->addFlash('failure', "again.later");
        }
    }
 }
