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
                ->setFeeAmount($item['fee_amount'])
                ->setPaymentFrequency($item['payment_frequency'])
                ->setBillable($item['billable'])
                ->setRentRecipe($rentalRecipe);

            if (isset($item['validity_from'])) {
                $fee->setValidityFrom(new \DateTimeImmutable($item['validity_from']));
            }

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
     *   'fee_amount':float,
     *   'payment_frequency':string,
     *   'billable':bool,
     *   'validity_from':string|null,
     *   'ref':string
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'description' => AdditionalFeeEnum::MUNICIPAL_WASTE->value,
            'fee_amount' => 700.0,
            'payment_frequency' => PaymentFrequencyEnum::ANNUALLY->value,
            'billable' => false,
            'validity_from' => null,
            'ref' => self::ADDITIONAL_FEE_1,
        ];

        yield [
            'description' => AdditionalFeeEnum::WATER_AND_SEWAGE->value,
            'fee_amount' => 1500.0,
            'payment_frequency' => PaymentFrequencyEnum::MONTHLY->value,
            'billable' => true,
            'validity_from' => null,
            'ref' => self::ADDITIONAL_FEE_2,
        ];
        yield [
            'description' => AdditionalFeeEnum::WATER_AND_SEWAGE->value,
            'fee_amount' => 800.0,
            'payment_frequency' => PaymentFrequencyEnum::MONTHLY->value,
            'billable' => true,
            'validity_from' => '2021-06-25',
            'ref' => self::ADDITIONAL_FEE_3,
        ];
        yield [
            'description' => AdditionalFeeEnum::MUNICIPAL_WASTE->value,
            'fee_amount' => 1000.0,
            'payment_frequency' => PaymentFrequencyEnum::ANNUALLY->value,
            'billable' => false,
            'validity_from' => '2022-05-25',
            'ref' => self::ADDITIONAL_FEE_4,
        ];
        yield [
            'description' => AdditionalFeeEnum::WATER_AND_SEWAGE->value,
            'fee_amount' => 1200.0,
            'payment_frequency' => PaymentFrequencyEnum::MONTHLY->value,
            'billable' => true,
            'validity_from' => '2023-08-25',
            'ref' => self::ADDITIONAL_FEE_5,
        ];
    }
}
