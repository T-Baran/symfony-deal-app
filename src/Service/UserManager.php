<?php

namespace App\Service;

use App\DTO\UpdateUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserManager
{
    public function __construct(private EntityManagerInterface $em, private Security $security, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function transferAndSave(UpdateUser $updateUser, $subject)
    {
        $user = $this->security->getUser();
        switch($subject){
            case "email":
                $user->setEmail($updateUser->email);
                break;
            case "username":
                $user->setUsername($updateUser->username);
                break;
            case "password":
                $newPassword = $updateUser->newPlainPassword;
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                break;
        }
//        if ($subject === "email") {
//            $user->setEmail($updateUser->email);
//        } elseif ($subject === "username") {
//            $user->setUsername($updateUser->username);
//        } else {
//            $newPassword = $updateUser->newPlainPassword;
//            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
//            $user->setPassword($hashedPassword);
//        }
        $this->em->persist($user);
        $this->em->flush();
    }

    public function hashAndSave(User $user, String $plainPassword):void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $this->em->persist($user);
        $this->em->flush();
    }
}