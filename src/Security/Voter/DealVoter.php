<?php

namespace App\Security\Voter;

use App\Entity\Deal;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DealVoter extends Voter
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
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Deal;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!$subject instanceof Deal) {
            throw new \Exception('Wrong type passed');
        }
        return match ($attribute) {
            self::EDIT => $user === $subject->getUser() || $this->security->isGranted('ROLE_EDITOR'),
            self::DELETE => $user === $subject->getUser() || $this->security->isGranted('ROLE_ADMIN'),
            default => false
        };

    }
}
