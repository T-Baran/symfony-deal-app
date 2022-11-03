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
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $deal = new Deal();
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $deal->setScore(0);
            $em->persist($deal);
            $em->flush();

            return $this->redirectToRoute('deals');
        }
        return $this->renderForm('deals/create.html.twig',[
            'form' => $form,
        ]);
    }
}
