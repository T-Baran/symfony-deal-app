<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
#[Route('/vote')]
class VoteController extends AbstractController
{
    #[Route('/up/{id}', name: 'up_vote', methods: 'POST')]
    public function upVote(Deal $deal, VoteRepository $voteRepository, EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $hasUserUpVoted = $voteRepository->findHasUpVoted($user, $deal);
        $hasUserDownVoted = $voteRepository->findHasDownVoted($user, $deal);
        if (!empty($hasUserUpVoted)) {
            $this->addFlash('failure', 'vote.already');
        } elseif (!empty($hasUserDownVoted)) {
            $vote = $voteRepository->find($hasUserDownVoted[0]);
            $vote->setUpVote(true);
            $deal->setScore($deal->getScore() + 2);
            $em->flush();
            $this->addFlash('success', 'vote.successful');
        } else {
            $vote = new Vote();
            $vote->setUpVote(true);
            $vote->setDeal($deal);
            $vote->setUser($user);
            $deal->setScore($deal->getScore() + 1);
            $em->persist($vote);
            $em->flush();
            $this->addFlash('success', 'vote.successful');
        }
        return $this->redirect($request->request->get('referer'));
    }

    #[Route('/down/{id}', name: 'down_vote', methods: 'POST')]
    public function downVote(Deal $deal, VoteRepository $voteRepository, EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $hasUserUpVoted = $voteRepository->findHasUpVoted($user, $deal);
        $hasUserDownVoted = $voteRepository->findHasDownVoted($user, $deal);
        if (!empty($hasUserDownVoted)) {
            $this->addFlash('failure', 'vote.already');
        } elseif (!empty($hasUserUpVoted)) {
            $vote = $voteRepository->find($hasUserUpVoted[0]);
            $vote->setUpVote(false);
            $deal->setScore($deal->getScore() - 2);
            $em->flush();
            $this->addFlash('success', 'vote.successful');
        } else {
            $vote = new Vote();
            $vote->setUpVote(false);
            $vote->setDeal($deal);
            $vote->setUser($user);
            $deal->setScore($deal->getScore() - 1);
            $em->persist($vote);
            $em->flush();
            $this->addFlash('success', 'vote.successful');
        }
        return $this->redirect($request->request->get('referer'));
    }
}
