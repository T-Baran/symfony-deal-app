<?php

namespace App\DTO;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


class UpdateUser
{
    #[Assert\Email(message: 'email.not.valid')]
    public ?string $email = null;

    public ?string $username = null;

    public ?string $oldPlainPassword = null;

    public ?string $newPlainPassword = null;

    public ?string $roles = null;
}
