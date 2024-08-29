<?php

namespace App\DataFixtures;

use App\Entity\PaymentRecipe;
use App\Entity\PaymentRecord;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentRecordFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < PaymentRecipeFixtures::PAYMENTS_RECIPE_COUNT; ++$i) {
            $recipe = $this->getPaymentRecipe(PaymentRecipeFixtures::getRefMask($i));

            if (0 === random_int(0, 100) % 7) {
                $this->splitPayment($recipe, $manager);
            } else {
                $this->fullPayment($recipe, $manager);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PaymentRecipeFixtures::class,
        ];
    }

    private function getPaymentRecipe(string $refString): PaymentRecipe
    {
        if (!$this->hasReference($refString, PaymentRecipe::class)) {
            throw new \RuntimeException('DataFixtures for '.$refString.' reference dont exists');
        }

        $reference = $this->getReference($refString, PaymentRecipe::class);

        if (!$reference instanceof PaymentRecipe) {
            throw new \RuntimeException('DataFixtures reference is not correct instance for '.$refString);
        }

        return $reference;
    }

    private function fullPayment(PaymentRecipe $recipe, ObjectManager $manager): void
    {
        $payment = new PaymentRecord();
        $payment->setPaymentDate($recipe->getMaturityDate())
            ->setAmount($recipe->getPayableAmount())
            ->setPaymentRecipe($recipe)
        ;
        $recipe->setPaidAmount($recipe->getPayableAmount())
            ->setPaymentDate($recipe->getMaturityDate());

        $manager->persist($payment);
    }

    private function splitPayment(PaymentRecipe $recipe, ObjectManager $manager): void
    {
        $payableParts = $this->generateRandomNumbers($recipe->getPayableAmount(), random_int(1, 5));
        foreach ($payableParts as $key => $part) {
            if (0 === $key) {
                $paymentDate = $recipe->getMaturityDate();
            } else {
                $paymentDate = $recipe->getMaturityDate()->modify('+'.($key * 2).' days');
            }
            $payment = new PaymentRecord();
            $payment->setPaymentDate($paymentDate)
                ->setAmount((float) $part)
                ->setPaymentRecipe($recipe)
            ;

            $manager->persist($payment);
        }
        $recipe->setPaidAmount($recipe->getPayableAmount())
            ->setPaymentDate($recipe->getMaturityDate());
    }

    private function generateRandomNumbers(float $max, int $count): array
    {
        $numbers = [];

        for ($i = 1; $i < $count; ++$i) {
            $random = mt_rand(0, $max / ($count - $i));
            $numbers[] = $random;
            $max -= $random;
        }

        $numbers[] = $max;

        shuffle($numbers);

        return $numbers;
    }
}
