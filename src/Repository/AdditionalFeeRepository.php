<?php

namespace App\Repository;

use App\Entity\AdditionalFee;
use App\Entity\RentalRecipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Ulid;

/**
 * @extends ServiceEntityRepository<AdditionalFee>
 */
class AdditionalFeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalFee::class);
    }

    public function getLastFee(string $description, RentalRecipe $recipe, Ulid $id): ?AdditionalFee
    {
        $qb = $this->createQueryBuilder('f')
            ->setCacheable(false)
            ->select('f')
            ->where('f.description = :description')
            ->andWhere('f.rentRecipe = :rentrecipe')
            ->andWhere('f.id != :id')
            ->setParameter('description', $description, 'string')
            ->setParameter('rentrecipe', $recipe->getId(), 'ulid')
            ->setParameter('id', $id, 'ulid')
            ->orderBy('f.validityFrom', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $qb->getQuery()->getOneOrNullResult();
        if ($result instanceof AdditionalFee) {
            return $result;
        } else {
            return null;
        }
    }
}
