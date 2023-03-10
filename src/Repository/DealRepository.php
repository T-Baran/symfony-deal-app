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

    public function findOneRandom(): Deal
    {
        // get all tasks
        $deals = $this->findAll();
        $count = count($deals);
        $randomInt = random_int(0, $count-1);
        return $deals[$randomInt];
    }

    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder("q")
            ->orderBy('q.createdAt', 'DESC');
    }

    public function findByQuery($search): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.title LIKE :val OR q.description LIKE :val OR q.seller LIKE :val')
            ->setParameter('val', '%' . $search . '%')
            ->orderBy('q.createdAt', 'DESC');
    }

    public function sortByPriceAsc($query): QueryBuilder
    {
        return $query->orderBy('q.price', 'ASC');
    }

    public function sortByPriceDesc($query): QueryBuilder
    {
        return $query->orderBy('q.price', 'DESC');
    }

    public function sortByDiscountAsc($query): QueryBuilder
    {
        return $query->orderBy('q.discount', 'ASC');
    }

    public function sortByDiscountDesc($query): QueryBuilder
    {
        return $query->orderBy('q.discount', 'DESC');
    }

    public function sortByVotesDesc($query): QueryBuilder
    {
        return $query->orderBy('q.score', 'DESC');
    }
}
