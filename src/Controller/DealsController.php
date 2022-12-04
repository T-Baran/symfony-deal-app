<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Form\DealType;
use App\Repository\CommentRepository;
use App\Repository\DealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DealsController extends AbstractController
{
    use TargetPathTrait;

    #[Route('/', name: 'deals')]
    public function index(DealRepository $dealRepository): Response
    {
        $deals = $dealRepository->findAll();
        return $this->render('deals/index.html.twig', [
            'deals' => $deals,
        ]);
    }

    #[Route('/create', name:'deal_create')]
    public function create(EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $deal = new Deal();
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $deal->setScore(0);
            $deal->setUser($security->getUser());
            $em->persist($deal);
            $em->flush();
            return $this->redirectToRoute('deals');
        }
        return $this->renderForm('deals/create.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name:'deal_edit')]
    public function edit( EntityManagerInterface $em, Deal $deal, Request $request): Response
    {
//        dd($request->getSession());
//        $url = $this->getTargetPath($request->getSession(), 'main');
//        if(!$url){
//            $url = $this->generateUrl('deals');
//        }
        $this->denyAccessUnlessGranted('EDIT', $deal);
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($deal);
            $em->flush();
//            return $this->redirect($url);
            return $this->redirectToRoute('deal_show',[
                'id'=>$deal->getId(),
            ]);
        }
        return $this->renderForm('deals/edit.html.twig',[
            'form' => $form,
        ]);
    }
    #[Route('/show/{id}', name:'deal_show')]
    public function show(Deal $deal, Request $request, CommentRepository $commentRepository): Response
    {
        $comments = $deal->getComments();
        return $this->render('deals/show.html.twig',[
            'deal' => $deal,
            'comments' => $comments,
        ]);
    }
    #[Route('/delete/{id}', name:'deal_delete', methods: ['POST'])]
    public function delete(Deal $deal, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $deal);
        $em->remove($deal);
        $em->flush();
        return $this->redirect('/');
    }



}
