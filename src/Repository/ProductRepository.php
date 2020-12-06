<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /** @return Product[] */
    public function findRandomByWeatherConditionCode(string $conditionCode, int $limit = null): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.weatherConditions', 'c')
            ->select('p')
            ->where('c.code = :code')
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->setParameter('code', $conditionCode)
            ->getQuery()
            ->getResult()
            ;
    }
}
