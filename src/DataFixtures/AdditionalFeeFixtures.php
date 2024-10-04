<?php

namespace App\DataFixtures;

use App\Entity\AdditionalFee;
use App\Entity\RentalRecipe;
use App\Enum\AdditionalFeeEnum;
use App\Enum\PaymentFrequencyEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdditionalFeeFixtures extends Fixture implements DependentFixtureInterface
{
    public const ADDITIONAL_FEE_1 = __CLASS__.'_1';
    public const ADDITIONAL_FEE_2 = __CLASS__.'_2';
    public const ADDITIONAL_FEE_3 = __CLASS__.'_3';
    public const ADDITIONAL_FEE_4 = __CLASS__.'_4';
    public const ADDITIONAL_FEE_5 = __CLASS__.'_5';

    public function load(ObjectManager $manager): void
    {
        $rentalRecipe = $this->getRentalRecipe(RentalRecipeFixtures::RENTAL_RECIPE_1);

        foreach ($this->getData() as $item) {
            $fee = new AdditionalFee();
            $fee->setDescription($item['description'])
                ->setPaymentFrequency($item['payment_frequency'])
                ->setBillable($item['billable'])
                ->setRentRecipe($rentalRecipe);

            $rentalRecipe->addAdditionalFee($fee);

            $this->addReference($item['ref'], $fee);
            $manager->persist($fee);
            $manager->flush();
        }
    }

    /**
     * @return array<int, string>
     */
    public function getDependencies(): array
    {
        return [
            RentalRecipeFixtures::class,
        ];
    }

    private function getRentalRecipe(string $refString): RentalRecipe
    {
        if (!$this->hasReference($refString, RentalRecipe::class)) {
            throw new \RuntimeException('DataFixtures for '.$refString.' reference dont exists');
        }

        $reference = $this->getReference($refString, RentalRecipe::class);

        if (!$reference instanceof RentalRecipe) {
            throw new \RuntimeException('DataFixtures reference is not correct instance for '.$refString);
        }

        return $reference;
    }

    /**
     * @return \Generator<int, array{
     *   'description':string,
     *   'payment_frequency':string,
     *   'billable':bool,
     *   'ref':string
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'description' => AdditionalFeeEnum::MUNICIPAL_WASTE->value,
            'payment_frequency' => PaymentFrequencyEnum::ANNUALLY->value,
            'billable' => false,
            'ref' => self::ADDITIONAL_FEE_1,
        ];

        yield [
            'description' => AdditionalFeeEnum::WATER_AND_SEWAGE->value,
            'payment_frequency' => PaymentFrequencyEnum::MONTHLY->value,
            'billable' => true,
            'ref' => self::ADDITIONAL_FEE_2,
        ];
    }
}
