<?php

namespace App\Service;

use App\Entity\Deal;
use Doctrine\ORM\EntityManagerInterface;

class DealManager
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(Deal $data1)
    {
        $this->em->persist($data1);
        $this->em->flush();
    }

    public function delete(Deal $data1)
    {
        $this->em->remove($data1);
        $this->em->flush();
    }
}