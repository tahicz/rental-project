<?php

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\RentalRecipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $rentalRecipe = $this->getRentalRecipe(RentalRecipeFixtures::RENTAL_RECIPE_1);
        $paymentDate = \DateTime::createFromImmutable($rentalRecipe->getValidityFrom());
        $paymentDate->modify('first day of this month')
            ->modify('+'.($rentalRecipe->getMaturity() - 1).' day');
        while ($paymentDate < new \DateTime('today +12months')) {
            $payment = new Payment();
            $payment->setAmount($rentalRecipe->getFullMonthlyRate())
                ->setMaturityDate(\DateTimeImmutable::createFromMutable($paymentDate))
                ->setRentalRecipe($rentalRecipe)
            ;

            $manager->persist($payment);
            $rentalRecipe->addPayment($payment);

            $paymentDate = $paymentDate->modify('next month');
        }

        $manager->flush();
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
