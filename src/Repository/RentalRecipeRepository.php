<?php

namespace App\Repository;

use App\Entity\RentalRecipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RentalRecipe>
 */
class RentalRecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RentalRecipe::class);
    }

    public function save(RentalRecipe $rentalRecipe): void
    {
        $this->persist($rentalRecipe);
        $this->flush();
    }

    public function persist(RentalRecipe $rentalRecipe): void
    {
        $this->getEntityManager()->persist($rentalRecipe);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
