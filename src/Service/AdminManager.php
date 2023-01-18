<?php

namespace App\Service;

use App\DTO\UpdateUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AdminManager
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function roleTransferAndSave(User $user, UpdateUser $updateUser)
    {
         $user->setRoles([]);
         $user->setRoles([$updateUser->roles]);
         $this->em->persist($user);
         $this->em->flush();
    }

    public function userDelete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}