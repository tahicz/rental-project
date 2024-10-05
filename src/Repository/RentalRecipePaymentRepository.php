<?php

namespace App\Repository;

use App\Entity\RentalRecipePayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RentalRecipePayment>
 */
class RentalRecipePaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RentalRecipePayment::class);
    }

    public function save(RentalRecipePayment $payment): void
    {
        $this->persist($payment);
        $this->flush();
    }

    public function persist(RentalRecipePayment $payment): void
    {
        $this->getEntityManager()->persist($payment);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
