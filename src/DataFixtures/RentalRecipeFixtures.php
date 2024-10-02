<?php

namespace App\DataFixtures;

use App\Entity\RentalRecipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RentalRecipeFixtures extends Fixture
{
    public const RENTAL_RECIPE_1 = __CLASS__.'_1';
    public const RENTAL_RECIPE_1_1 = __CLASS__.'_1_1';
    public const RENTAL_RECIPE_1_2 = __CLASS__.'_1_2';

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $rentRecipe = new RentalRecipe();
            $rentRecipe->setBasicRent($data['basic_rent'])
                ->setMaturity($data['maturity'])
                ->setValidityFrom($data['validity_from']);

            if (null === $data['parent']) {
                $rentRecipe->setParent($data['parent']);
            } else {
                /** @var RentalRecipe $parent */
                $parent = $this->getReference($data['parent'], RentalRecipe::class);
                $parent->setChild($rentRecipe)
                    ->setValidityTo($data['validity_from']->modify('-1day'));
                $manager->persist($parent);

                $rentRecipe->setParent($parent);
            }

            $manager->persist($rentRecipe);
            $this->addReference($data['ref'], $rentRecipe);
        }

        $manager->flush();
    }

    /**
     * @return \Generator<int, array{
     *     'basic_rent':float,
     *     'maturity':int,
     *     'validity_from':\DateTimeImmutable,
     *     'parent': null|string,
     *     'ref': string
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'basic_rent' => 10_441.67,
            'maturity' => 25,
            'validity_from' => new \DateTimeImmutable('2020-05-20'),
            'parent' => null,
            'ref' => self::RENTAL_RECIPE_1,
        ];

        yield [
            'basic_rent' => 11_924.39,
            'maturity' => 25,
            'validity_from' => new \DateTimeImmutable('2022-05-25'),
            'parent' => self::RENTAL_RECIPE_1,
            'ref' => self::RENTAL_RECIPE_1_1,
        ];

        yield [
            'basic_rent' => 13_668.15,
            'maturity' => 25,
            'validity_from' => new \DateTimeImmutable('2024-10-25'),
            'parent' => self::RENTAL_RECIPE_1_1,
            'ref' => self::RENTAL_RECIPE_1_2,
        ];
    }
}
