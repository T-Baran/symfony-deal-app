<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Form\DealType;
use App\Repository\DealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DealsController extends AbstractController
{
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
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($deal);
            $em->flush();

            return $this->redirectToRoute('deals');
        }
        return $this->renderForm('deals/edit.html.twig',[
            'form' => $form,
        ]);
    }
    #[Route('/show/{id}', name:'deal_show')]
    public function show(Deal $deal): Response
    {
        return $this->render('deals/show.html.twig',[
            'deal' => $deal,
        ]);
    }

}
