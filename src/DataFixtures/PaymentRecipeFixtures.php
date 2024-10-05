<?php

namespace App\DataFixtures;

use App\Entity\PaymentRecipe;
use App\Entity\RentalRecipe;
use App\Helper\PaymentHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentRecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public const PAYMENTS_RECIPE_COUNT = 50;

    public function load(ObjectManager $manager): void
    {
        $rentalRecipe = $this->getRentalRecipe(RentalRecipeFixtures::RENTAL_RECIPE_1);
        $payment = $rentalRecipe->getRecipePayment()->first();
        if (false === $payment) {
            return;
        }
        $paymentDate = PaymentHelper::createPaymentDate($payment->getValidityFrom(), $payment->getMaturity());

        for ($i = 0; $i < self::PAYMENTS_RECIPE_COUNT; ++$i) {
            if ($payment->getValidityTo() < $paymentDate && null !== $payment->getValidityTo()) {
                $payment = $rentalRecipe->getRecipePayment()->next();
                if (false === $payment) {
                    break;
                }
                $paymentDate = PaymentHelper::createPaymentDate($payment->getValidityFrom(), $payment->getMaturity());
            }
            $paymentRecipe = $this->createPaymentRecipe($rentalRecipe, $paymentDate);

            $manager->persist($paymentRecipe);
            $rentalRecipe->addPaymentPlan($paymentRecipe);

            $this->addReference(self::getRefMask($i), $paymentRecipe);

            $paymentDate = $paymentDate->modify('next month');
        }
        $manager->persist($rentalRecipe);

        $manager->flush();
    }

    public static function getRefMask(int $i): string
    {
        return sprintf(__CLASS__.'_%02d', $i);
    }

    /**
     * @return array<int, string>
     */
    public function getDependencies(): array
    {
        return [
            RentalRecipeFixtures::class,
            RentalRecipePaymentFixtures::class,
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

    private function createPaymentRecipe(
        RentalRecipe $rentalRecipe,
        \DateTime $paymentDate
    ): PaymentRecipe {
        $maturityDate = \DateTimeImmutable::createFromMutable($paymentDate);

        $payment = new PaymentRecipe();
        $payment->setPayableAmount(
            $rentalRecipe->getFullPaymentForMonth($maturityDate)
        )
            ->setMaturityDate($maturityDate)
            ->setRentalRecipe($rentalRecipe)
        ;

        return $payment;
    }
}
