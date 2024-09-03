<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\Income;
use App\Entity\PaymentRecipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IncomeFixtures extends Fixture implements DependentFixtureInterface
{
    private int $incomeNumber = 0;

    public static function getRefMask(int $incomeNumber): string
    {
        return sprintf('ref_%s_%02d', __CLASS__, $incomeNumber);
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < PaymentRecipeFixtures::PAYMENTS_RECIPE_COUNT; ++$i) {
            $recipe = $this->getPaymentRecipe(PaymentRecipeFixtures::getRefMask($i));

            if (0 === random_int(0, 100) % 7) {
                $this->splitIncome($recipe->getPayableAmount(), $recipe->getMaturityDate(), $manager);
            } else {
                $this->saveIncome($recipe->getPayableAmount(), $recipe->getMaturityDate(), $manager);
            }
        }
        $manager->flush();
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

    private function getBankAccount(string $refString): BankAccount
    {
        if (!$this->hasReference($refString, BankAccount::class)) {
            throw new \RuntimeException('DataFixtures for '.$refString.' reference dont exists');
        }

        $reference = $this->getReference($refString, BankAccount::class);

        if (!$reference instanceof BankAccount) {
            throw new \RuntimeException('DataFixtures reference is not correct instance for '.$refString);
        }

        return $reference;
    }

    private function saveIncome(float $amount, \DateTimeImmutable $maturityDate, ObjectManager $manager): void
    {
        $income = new Income();
        $income->setAmount($amount)
            ->setIncomeDate($this->generateIncomeDate($maturityDate))
            ->setMessage('Random message')
            ->setRecipientAccount($this->getBankAccount(BankAccountFixtures::BANK_ACCOUNT_REFERENCE_1))
            ->setSenderAccount($this->getBankAccount(BankAccountFixtures::BANK_ACCOUNT_REFERENCE_2))
            ->setVariableSymbol((int) $maturityDate->format('Ymd'));

        $manager->persist($income);
        $this->addReference(self::getRefMask($this->incomeNumber), $income);
        ++$this->incomeNumber;
    }

    private function splitIncome(float $amount, \DateTimeImmutable $maturityDate, ObjectManager $manager): void
    {
        $payableParts = $this->generateRandomNumbers($amount, random_int(1, 5));
        foreach ($payableParts as $key => $part) {
            if (0 === $key) {
                $paymentDate = $maturityDate;
            } else {
                $paymentDate = $maturityDate->modify('+'.($key * 2).' days');
            }
            $this->saveIncome($part, $paymentDate, $manager);
        }
    }

    /**
     * @return array<int, float>
     */
    private function generateRandomNumbers(float $max, int $count): array
    {
        $numbers = [];

        for ($i = 1; $i < $count; ++$i) {
            $random = mt_rand(0, (int) $max / ($count - $i));
            $numbers[] = $random;
            $max -= $random;
        }

        $numbers[] = $max;

        shuffle($numbers);

        return $numbers;
    }

    private function generateIncomeDate(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $modification = random_int(-3, 3);

        return $date->modify($modification.' day');
    }

    public function getDependencies(): array
    {
        return [
            BankAccountFixtures::class,
            PaymentRecipeFixtures::class,
        ];
    }
}
