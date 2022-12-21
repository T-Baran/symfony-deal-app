<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Form\DealType;
use App\Repository\DealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DealsController extends AbstractController
{
    use TargetPathTrait;

    #[Route('/{page<\d+>}', name: 'deals')]
    public function index(DealRepository $dealRepository, int $page = 1): Response
    {
//        dd($dealRepository->queryAll());
        $querybuilder = $dealRepository->queryAll();
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($querybuilder)
        );
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);
        return $this->render('deals/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/create', name: 'deal_create')]
    public function create(EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $deal = new Deal();
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $deal->setScore(0);
            $deal->setUser($security->getUser());
            $em->persist($deal);
            $em->flush();
            $this->addFlash('success', 'add.deal');
            return $this->redirectToRoute('deals');
        }
        return $this->renderForm('deals/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'deal_edit')]
    public function edit(EntityManagerInterface $em, Deal $deal, Request $request): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $deal);
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($deal);
            $em->flush();
            $this->addFlash('success', 'update.deal');
            return $this->redirect($request->request->get('referer'));
        }
        return $this->renderForm('deals/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/show/random', name: 'deal_random')]
    public function getRandom(DealRepository $dealRepository): Response
    {
        return $this->render('deals/index.html.twig', [
            'deals' => $dealRepository->findOneRandom(),
        ]);
    }

    #[Route('/show/{id}', name: 'deal_show')]
    public function show(Deal $deal): Response
    {
        $comments = $deal->getComments();
        return $this->render('deals/show.html.twig', [
            'deal' => $deal,
            'comments' => $comments,
        ]);
    }

    #[Route('/delete/{id}', name: 'deal_delete', methods: ['POST'])]
    public function delete(Deal $deal, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $deal);
        $em->remove($deal);
        $em->flush();
        $this->addFlash('success', 'delete.deal');
        if (str_contains($request->request->get('referer'), 'show')) {
            return $this->redirect('/');
        }
        return $this->redirect($request->request->get('referer'));
    }

    #[Route('/up/{id}', name: 'up_vote', methods: 'POST')]
    public function upVote(Deal $deal, EntityManagerInterface $em, Request $request): Response
    {
        $deal->setScore($deal->getScore() + 1);
        $em->flush();
        return $this->redirect($request->request->get('referer'));
    }

    #[Route('/down/{id}', name: 'down_vote', methods: 'POST')]
    public function downVote(Deal $deal, EntityManagerInterface $em, Request $request): Response
    {
        $deal->setScore($deal->getScore() - 1);
        $em->flush();
        return $this->redirect($request->request->get('referer'));
    }
}
