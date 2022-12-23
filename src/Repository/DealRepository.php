<?php

namespace App\Repository;

use App\Entity\Deal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Deal>
 *
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    public function save(Deal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Deal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneRandom(): array
    {
        // get all tasks
        $deals = $this->findAll();
        $count = count($deals);
        $randomInt = random_int(0, $count);
        return [$deals[$randomInt]];
    }

    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder("q");
    }

    public function findAllSortByPriceAsc(): QueryBuilder
    {
        return $this->createQueryBuilder("d")
            ->orderBy('d.price', 'ASC');
    }

    public function findAllSortByPriceDesc(): QueryBuilder
    {
        return $this->createQueryBuilder("d")
            ->orderBy('d.price', 'DESC');
    }

    public function findAllSortByDiscountAsc(): QueryBuilder
    {
        return $this->createQueryBuilder("d")
            ->orderBy('d.discount', 'ASC');
    }

    public function findAllSortByDiscountDesc(): QueryBuilder
    {
        return $this->createQueryBuilder("d")
            ->orderBy('d.discount', 'DESC');
    }

    public function findAllSortByVotesDesc(): QueryBuilder
    {
        return $this->createQueryBuilder("d")
            ->orderBy('d.score', 'DESC');
    }

    public function findByQuery($search): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.title LIKE :val OR q.description LIKE :val OR q.seller LIKE :val')
            ->setParameter('val', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
