<?php

namespace App\DataFixtures;

use App\Entity\RentalRecipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RentalRecipeFixtures extends Fixture
{
    public const RENTAL_RECIPE_1 = __CLASS__.'_1';
    public const RENTAL_RECIPE_2 = __CLASS__.'_2';
    public const RENTAL_RECIPE_3 = __CLASS__.'_3';

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $rentRecipe = new RentalRecipe();
            $rentRecipe
                ->setValidityFrom($data['validity_from']);

            $manager->persist($rentRecipe);
            $this->addReference($data['ref'], $rentRecipe);
        }

        $manager->flush();
    }

    /**
     * @return \Generator<int, array{
     *     'validity_from':\DateTimeImmutable,
     *     'ref': string
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'validity_from' => new \DateTimeImmutable('2020-05-20'),
            'ref' => self::RENTAL_RECIPE_1,
        ];
        yield [
            'validity_from' => new \DateTimeImmutable('2020-05-20'),
            'ref' => self::RENTAL_RECIPE_2,
        ];
        yield [
            'validity_from' => new \DateTimeImmutable('2024-05-20'),
            'ref' => self::RENTAL_RECIPE_3,
        ];
    }
}
