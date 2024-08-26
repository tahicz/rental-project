<?php

namespace App\DataFixtures;

use App\Entity\RentalRecipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RentalRecipeFixtures extends Fixture
{
    public const RENTAL_RECIPE_1 = __CLASS__.'_1';

    public function load(ObjectManager $manager): void
    {
        $rentRecipe = new RentalRecipe();
        $rentRecipe->setBasicRent(10441.67)
            ->setMaturity(25)
            ->setValidityFrom(new \DateTimeImmutable('2020-05-20'));

        $manager->persist($rentRecipe);
        $manager->flush();

        $this->addReference(self::RENTAL_RECIPE_1, $rentRecipe);
    }
}
