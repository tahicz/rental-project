<?php

namespace App\DataFixtures;

use App\Entity\RentalRecipe;
use App\Entity\RentalRecipePayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RentalRecipePaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $i => $item) {
            $payment = new RentalRecipePayment();
            $payment->setAmount($item['amount'])
                ->setMaturity($item['maturity'])
                ->setNote($item['note'])
                ->setValidityFrom($item['validity_from'])
                ->setRentalRecipe($this->getRecipeByRef($item['recipe']));
            if (null !== $item['validity_to']) {
                $payment->setValidityTo($item['validity_to']);
            }
            $manager->persist($payment);
            $this->setReference(self::getRefMask($i), $payment);
        }
        $manager->flush();
    }

    public static function getRefMask(int $i): string
    {
        return sprintf(__CLASS__.'_%d', $i);
    }

    /**
     * @return \Generator<int, array{
     *     'amount':float,
     *     'maturity':int,
     *     'validity_from': \DateTimeImmutable,
     *     'validity_to': \DateTimeImmutable|null,
     *     'recipe': string,
     *     'note': string
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'amount' => 10_441.67,
            'maturity' => 25,
            'validity_from' => new \DateTimeImmutable('2020-05-20'),
            'validity_to' => new \DateTimeImmutable('2022-05-24'),
            'recipe' => RentalRecipeFixtures::RENTAL_RECIPE_1,
            'note' => 'Začátek nájmu',
        ];

        yield [
            'amount' => 11_924.39,
            'maturity' => 25,
            'validity_from' => new \DateTimeImmutable('2022-05-25'),
            'validity_to' => null,
            'recipe' => RentalRecipeFixtures::RENTAL_RECIPE_1,
            'note' => 'Inflace',
        ];

        yield [
            'amount' => 10_000,
            'maturity' => 15,
            'validity_from' => new \DateTimeImmutable('2020-05-20'),
            'validity_to' => new \DateTimeImmutable('2022-05-24'),
            'recipe' => RentalRecipeFixtures::RENTAL_RECIPE_2,
            'note' => 'Začátek nájmu',
        ];

        yield [
            'amount' => 13_000,
            'maturity' => 15,
            'validity_from' => new \DateTimeImmutable('2022-05-25'),
            'validity_to' => null,
            'recipe' => RentalRecipeFixtures::RENTAL_RECIPE_2,
            'note' => 'Inflace',
        ];

        yield [
            'amount' => 15_000,
            'maturity' => 15,
            'validity_from' => new \DateTimeImmutable('2024-05-20'),
            'validity_to' => null,
            'recipe' => RentalRecipeFixtures::RENTAL_RECIPE_3,
            'note' => 'Začátek nájmu',
        ];
    }

    public function getDependencies(): array
    {
        return [
            RentalRecipeFixtures::class,
        ];
    }

    private function getRecipeByRef(string $rentalRecipeRef): RentalRecipe
    {
        if (!$this->hasReference($rentalRecipeRef, RentalRecipe::class)) {
            throw new \RuntimeException('DataFixtures for '.$rentalRecipeRef.' reference dont exists');
        }

        $reference = $this->getReference($rentalRecipeRef, RentalRecipe::class);

        if (!$reference instanceof RentalRecipe) {
            throw new \RuntimeException('DataFixtures reference is not correct instance for '.$rentalRecipeRef);
        }

        return $reference;
    }
}
