<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if($this->security->isGranted('ROLE_SUPER_ADMIN')){
            return true;
        }
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if(!$subject instanceof Comment){
            throw new \Exception('Wrong type passed');
        }

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EDIT => $user === $subject->getUser() || $this->security->isGranted('ROLE_EDITOR'),
            self::DELETE => $user === $subject->getUser() || $this->security->isGranted('ROLE_ADMIN'),
            default => false,
        };

    }
}
