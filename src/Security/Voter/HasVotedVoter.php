<?php

namespace App\Security\Voter;

use App\Repository\VoteRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class HasVotedVoter extends Voter
{
    public const UPVOTE = 'UPVOTE';
    public const DOWNVOTE = 'DOWNVOTE';
    private $voteRepository;

    public function __construct(VoteRepository $voteRepository)
    {
        $this->voteRepository = $voteRepository;

    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::UPVOTE, self::DOWNVOTE])
            && $subject instanceof \App\Entity\Deal;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (empty($this->voteRepository->findHasVoted($user, $subject))) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::UPVOTE:
                $case = $this->voteRepository->findHasUpVoted($user, $subject);
                if (!empty($case)) {
                    return true;
                }
                break;
            case self::DOWNVOTE:
                $case = $this->voteRepository->findHasDownVoted($user, $subject);
                if (!empty($case)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
