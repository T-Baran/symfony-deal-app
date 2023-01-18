<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Form\DealType;
use App\Repository\DealRepository;
use App\Service\DealManager;
use App\Service\FileUploader;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DealsController extends AbstractController
{

    public function __construct(private DealRepository $dealRepository, private FileUploader $fileUploader, private DealManager $dealManager, private Security $security)
    {
    }

    #[Route('/{page<\d+>}/{subject}', name: 'deals')]
    public function index(Request $request, int $page = 1, string $subject = null): Response
    {
        $querybuilder = $this->getQuery($request->query->get('q'), $subject);
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($querybuilder)
        );
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);
        return $this->render('deals/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/create', name: 'deal_create')]
    public function create(Request $request): Response
    {
        $deal = new Deal();
        $form = $this->createForm(DealType::class, $deal);

        return $this->addUpdate($deal, $request, true) ?? $this->renderForm('deals/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'deal_edit')]
    public function edit(Deal $deal, Request $request): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $deal);
        $form = $this->createForm(DealType::class, $deal);

        return $this->addUpdate($deal, $request, false) ?? $this->renderForm('deals/edit.html.twig', [
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
    public function delete(Deal $deal, Request $request): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $deal);
        $this->dealManager->delete($deal);
        $this->addFlash('success', 'delete.deal');
        if (str_contains($request->request->get('referer'), 'show')) {
            return $this->redirect('/');
        }
        return $this->redirect($request->request->get('referer'));
    }

    private function getQuery($query, $subject): QueryBuilder
    {
        if ($query) {
            $search = $this->dealRepository->findByQuery($query);
        } else {
            $search = $this->dealRepository->queryAll();
        }
        match ($subject) {
            'p' => $querybuilder = $this->dealRepository->sortByPriceAsc($search),
            'pd' => $querybuilder = $this->dealRepository->sortByPriceDesc($search),
            'd' => $querybuilder = $this->dealRepository->sortByDiscountAsc($search),
            'dd' => $querybuilder = $this->dealRepository->sortByDiscountDesc($search),
            'v' => $querybuilder = $this->dealRepository->sortByVotesDesc($search),
            default => $querybuilder = $search,
        };
        return $querybuilder;
    }

    private function fillFields(Deal $deal, $photoFile, bool $isNew): Deal
    {
        if ($isNew) {
            $deal->setScore(0);
            $deal->setUser($this->security->getUser());
        }
        $deal->setDiscount((1 - round($deal->getPrice() / $deal->getPriceBefore(), 2)) * 100);
        if ($photoFile) {
            if (!$isNew) {
                $this->fileUploader->delete($deal->getPhotoFilename());
            }
            $photoFilename = $this->fileUploader->upload($photoFile);
            $deal->setPhotoFilename($photoFilename);
        }
        return $deal;
    }

    private function addUpdate(Deal $deal, Request $request, bool $isNew)
    {
        $form = $this->createForm(DealType::class, $deal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            if (!$isNew && !str_contains($request->request->get('referer'), '/edit/')) {
                $session->set('referer', $request->request->get('referer'));
            }
            if ($deal->getPrice() > $deal->getPriceBefore()) {
                $this->addFlash('failure', 'before.lower');
                if ($isNew) {
                    return $this->redirectToRoute('deal_create');
                }
                return $this->redirectToRoute('deal_edit', ['id' => $deal->getId()]);
            }
            $deal = $this->fillFields($deal, $form->get('photoFilename')->getData(), $isNew);
            $this->dealManager->save($deal);
            if (!$isNew) {
                $this->addFlash('success', 'update.deal');
                return $this->redirect($session->get('referer'));
            }
            $this->addFlash('success', 'add.deal');
            return $this->redirectToRoute('deals');
        }
    }
}
