<?php

namespace App\DataFixtures;

use App\Entity\Income;
use App\Entity\PaymentRecipe;
use App\Entity\PaymentRecord;
use App\Exception\FixtureReferenceNotFoundException;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentRecordFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            PaymentRecipeFixtures::class,
            IncomeFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $incomeI = 0;
        for ($recipeI = 0; $recipeI < PaymentRecipeFixtures::PAYMENTS_RECIPE_COUNT; ++$recipeI) {
            try {
                $recipe = $this->getRecipe($recipeI);
                while ($recipe->getPaidAmount() < $recipe->getPayableAmount()) {
                    $income = $this->getIncome($incomeI);

                    $record = new PaymentRecord();
                    $record->setIncome($income)
                        ->setPaymentRecipe($recipe)
                        ->setAmount($income->getAmount())
                        ->setReceivedOn($income->getIncomeDate());

                    $manager->persist($record);
                    $manager->refresh($recipe);
                    $manager->flush();
                    ++$incomeI;
                }
            } catch (FixtureReferenceNotFoundException $e) {
            } finally {
                $manager->flush();
            }
        }
    }

    private function getIncome(int $i): Income
    {
        $incomeRef = IncomeFixtures::getRefMask($i);
        if (!$this->hasReference($incomeRef, Income::class)) {
            throw new FixtureReferenceNotFoundException('DataFixtures for '.$incomeRef.' reference dont exists');
        }

        $reference = $this->getReference($incomeRef, Income::class);

        if (!$reference instanceof Income) {
            throw new FixtureReferenceNotFoundException('DataFixtures reference is not correct instance for '.$incomeRef);
        }

        return $reference;
    }

    private function getRecipe(int $i): PaymentRecipe
    {
        $paymentRecipeRef = PaymentRecipeFixtures::getRefMask($i);
        if (!$this->hasReference($paymentRecipeRef, PaymentRecipe::class)) {
            throw new \RuntimeException('DataFixtures for '.$paymentRecipeRef.' reference dont exists');
        }

        $reference = $this->getReference($paymentRecipeRef, PaymentRecipe::class);

        if (!$reference instanceof PaymentRecipe) {
            throw new \RuntimeException('DataFixtures reference is not correct instance for '.$paymentRecipeRef);
        }

        return $reference;
    }
}
