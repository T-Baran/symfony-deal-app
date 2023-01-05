<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Form\DealType;
use App\Repository\DealRepository;
use App\Service\FileUploader;
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


    #[Route('/{page<\d+>}/{subject}', name: 'deals')]
    public function index(DealRepository $dealRepository, Request $request, int $page = 1, string $subject = null): Response
    {
        if ($query = $request->query->get('q')) {
            $search = $dealRepository->findByQuery($query);
        } else {
            $search = $dealRepository->queryAll();
        }
        match ($subject) {
            'p' => $querybuilder = $dealRepository->sortByPriceAsc($search),
            'pd' => $querybuilder = $dealRepository->sortByPriceDesc($search),
            'd' => $querybuilder = $dealRepository->sortByDiscountAsc($search),
            'dd' => $querybuilder = $dealRepository->sortByDiscountDesc($search),
            'v' => $querybuilder = $dealRepository->sortByVotesDesc($search),
            default => $querybuilder = $search,
        };
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
    public function create(EntityManagerInterface $em, Request $request, Security $security, FileUploader $fileUploader): Response
    {
        $deal = new Deal();
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($deal->getPrice()>$deal->getPriceBefore()){
                $this->addFlash('failure', 'before.lower');
                return $this->redirectToRoute('deals');
            }
            $deal->setScore(0);
            $deal->setUser($security->getUser());
            $deal->setDiscount((1 - round($deal->getPrice() / $deal->getPriceBefore(), 2)) * 100);
            $photoFile = $form->get('photoFilename')->getData();
            if ($photoFile) {
                $photoFilename = $fileUploader->upload($photoFile);
                $deal->setPhotoFilename($photoFilename);
            }
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
    public function edit(EntityManagerInterface $em, Deal $deal, Request $request, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $deal);
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($deal->getPrice()>$deal->getPriceBefore()){
                $this->addFlash('failure', 'before.lower');
                return $this->redirectToRoute('deals');
            }
            $deal->setDiscount((1 - round($deal->getPrice() / $deal->getPriceBefore(), 2)) * 100);
            $photoFile = $form->get('photoFilename')->getData();
            if ($photoFile) {
                $fileUploader->delete($deal->getPhotoFilename());
                $photoFilename = $fileUploader->upload($photoFile);
                $deal->setPhotoFilename($photoFilename);
            };
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
        $deal = $dealRepository->findOneRandom()->getId();
        return $this->redirectToRoute('deal_show', ['id' => $deal]);
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
}
