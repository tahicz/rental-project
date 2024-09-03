<?php

namespace App\DataFixtures;

use App\Entity\PaymentRecipe;
use App\Entity\RentalRecipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentRecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public const PAYMENTS_RECIPE_COUNT = 50;

    public function load(ObjectManager $manager): void
    {
        $rentalRecipe = $this->getRentalRecipe(RentalRecipeFixtures::RENTAL_RECIPE_1);
        $paymentDate = \DateTime::createFromImmutable($rentalRecipe->getValidityFrom());
        $paymentDate->modify('first day of this month')
            ->modify('+'.($rentalRecipe->getMaturity() - 1).' day');
        for ($i = 0; $i < self::PAYMENTS_RECIPE_COUNT; ++$i) {
            $payment = new PaymentRecipe();
            $payment->setPayableAmount($rentalRecipe->getFullMonthlyRate())
                ->setMaturityDate(\DateTimeImmutable::createFromMutable($paymentDate))
                ->setRentalRecipe($rentalRecipe)
            ;

            $manager->persist($payment);
            $rentalRecipe->addPayment($payment);

            $this->addReference(self::getRefMask($i), $payment);

            $paymentDate = $paymentDate->modify('next month');
        }

        $manager->flush();
    }

    public static function getRefMask(int $i): string
    {
        return sprintf(__CLASS__.'_%02d', $i);
    }

    /**
     * @return array<int, string>
     */
    public function getDependencies()
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
}
