<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class VoteManager
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save($data1){
        $this->em->persist($data1);
        $this->em->flush();
    }
}