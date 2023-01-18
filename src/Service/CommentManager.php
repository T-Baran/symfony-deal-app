<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Deal;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(Comment $data1, ?Deal $data2=null)
    {
        if($data2!==null){
            $this->em->persist($data2);
        }
        $this->em->persist($data1);
        $this->em->flush();
    }
    public function delete(Comment $data1)
    {
        $this->em->remove($data1);
        $this->em->flush();
    }
}