<?php

namespace App\DataFixtures;

use App\Entity\AdditionalFee;
use App\Entity\AdditionalFeePayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdditionalFeePaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $item) {
            $feePayment = new AdditionalFeePayment();
            $feePayment->setAmount($item['amount'])
                ->setNote($item['note'])
                ->setAdditionalFee($this->getAdditionalFeeByRef($item['ref']));

            if (null === $item['validity_from']) {
                $feePayment->setValidityFrom(null);
            } else {
                $feePayment->setValidityFrom(new \DateTimeImmutable($item['validity_from']));
            }

            $manager->persist($feePayment);
        }

        $manager->flush();
    }

    /**
     * @return \Generator<int, array{
     *     'amount':float,
     *     'note':string,
     *     'validity_from':string|null,
     *     'ref': string
     * }>
     */
    private function getData(): \Generator
    {
        yield [
            'amount' => 700.0,
            'note' => 'zacatek',
            'validity_from' => null,
            'ref' => AdditionalFeeFixtures::ADDITIONAL_FEE_1,
        ];

        yield [
            'amount' => 1_000.0,
            'note' => 'zdražení',
            'validity_from' => '2022-05-25',
            'ref' => AdditionalFeeFixtures::ADDITIONAL_FEE_1,
        ];

        yield [
            'amount' => 1_500.0,
            'note' => 'začátek',
            'validity_from' => null,
            'ref' => AdditionalFeeFixtures::ADDITIONAL_FEE_2,
        ];

        yield [
            'amount' => 800.0,
            'note' => 'snížení spotřeby',
            'validity_from' => '2021-06-25',
            'ref' => AdditionalFeeFixtures::ADDITIONAL_FEE_2,
        ];

        yield [
            'amount' => 1_200.0,
            'note' => 'zvýšení spotřeby',
            'validity_from' => '2023-08-25',
            'ref' => AdditionalFeeFixtures::ADDITIONAL_FEE_2,
        ];
    }

    private function getAdditionalFeeByRef(string $refString): AdditionalFee
    {
        if (!$this->hasReference($refString, AdditionalFee::class)) {
            throw new \RuntimeException('DataFixtures for '.$refString.' reference dont exists');
        }

        $reference = $this->getReference($refString, AdditionalFee::class);

        if (!$reference instanceof AdditionalFee) {
            throw new \RuntimeException('DataFixtures reference is not correct instance for '.$refString);
        }

        return $reference;
    }

    public function getDependencies(): array
    {
        return [
            AdditionalFeeFixtures::class,
        ];
    }
}
