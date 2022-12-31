<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function save(Vote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findHasVoted($user, $deal): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->andWhere('v.deal = :deal')
            ->setParameter('user', $user)
            ->setParameter('deal', $deal)
            ->getQuery()
            ->getResult();
    }

    public function findHasUpVoted($user, $deal): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->andWhere('v.deal = :deal')
            ->andWhere('v.UpVote = true')
            ->setParameter('user', $user)
            ->setParameter('deal', $deal)
            ->getQuery()
            ->getResult();
    }

    public function findHasDownVoted($user, $deal): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->andWhere('v.deal = :deal')
            ->andWhere('v.UpVote = false')
            ->setParameter('user', $user)
            ->setParameter('deal', $deal)
            ->getQuery()
            ->getResult();
    }
}
