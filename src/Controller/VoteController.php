<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\VoteManager;
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

    public function __construct(private VoteManager $voteManager)
    {
    }

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
            $this->setTrueAndAdd($deal, $vote,2);
        } else {
            $vote = $this->fillData($deal, $user);
            $this->setTrueAndAdd($deal, $vote, 1);
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
            $this->setFalseAndSub($deal, $vote, 2);
        } else {
            $vote = $this->fillData($deal, $user);
            $this->setFalseAndSub($deal, $vote, 1);
        }
        return $this->redirect($request->request->get('referer'));
    }

    public function setTrueAndAdd(Deal $deal, Vote $vote, int $value)
    {
        $vote->setUpVote(true);
        $deal->setScore($deal->getScore() + $value);
        $this->voteManager->save($vote);
    }

    public function setFalseAndSub(Deal $deal, Vote $vote, int $value)
    {
        $vote->setUpVote(false);
        $deal->setScore($deal->getScore() - $value);
        $this->voteManager->save($vote);
    }

    public function fillData(Deal $deal, User $user):Vote
    {
        $vote = new Vote();
        $vote->setDeal($deal);
        $vote->setUser($user);
        return $vote;
    }

}
