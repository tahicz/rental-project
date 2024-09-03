<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BankAccountFixtures extends Fixture
{
    public const BANK_ACCOUNT_REFERENCE_1 = 'bank_account_1';
    public const BANK_ACCOUNT_REFERENCE_2 = 'bank_account_2';

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $item) {
            $bankAccount = new BankAccount();
            $bankAccount->setPrefix($item['prefix'])
                ->setBankCode($item['bank_code'])
                ->setAccountNumber($item['account_number']);

            $manager->persist($bankAccount);
            $this->addReference($item['ref'], $bankAccount);
        }
        $manager->flush();
    }

    /**
     * @return \Generator<int, array{
     *     'ref':string,
     *     'prefix':int|null,
     *     'account_number':int,
     *     'bank_code': int
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'ref' => self::BANK_ACCOUNT_REFERENCE_1,
            'prefix' => null,
            'account_number' => 123_456_789,
            'bank_code' => 1010,
        ];
        yield [
            'ref' => self::BANK_ACCOUNT_REFERENCE_2,
            'prefix' => 100,
            'account_number' => 123_123_321,
            'bank_code' => 147,
        ];
    }
}
