<?php

namespace App\Service;

use App\Entity\PaymentRecipe;
use App\Entity\RentalRecipe;
use App\Repository\PaymentRepository;

readonly class PaymentPlanner
{
    public function __construct(
        private PaymentRepository $paymentRepository
    ) {
    }

    public function planFuturePayments(RentalRecipe $rentalRecipe, int $paymentCount = 1): void
    {
        $lastPayment = $this->paymentRepository->findOneBy(['rentalRecipe' => $rentalRecipe->getId()], ['id' => 'DESC']);
        if (null === $lastPayment) {
            $paymentDate = \DateTime::createFromImmutable($rentalRecipe->getValidityFrom());
            $paymentDate->modify('first day of this month')
                ->modify('+'.($rentalRecipe->getMaturity() - 1).' day');

            $lastPayment = $this->createPayment($rentalRecipe, $paymentDate);
        }

        for ($i = 0; $i < $paymentCount; ++$i) {
            $paymentDate = \DateTime::createFromImmutable($lastPayment->getMaturityDate());
            $paymentDate->modify('+1 month');

            $lastPayment = $this->createPayment($rentalRecipe, $paymentDate);
        }

        $this->paymentRepository->flush();
    }

    private function createPayment(RentalRecipe $rentalRecipe, \DateTimeInterface $paymentDate): PaymentRecipe
    {
        $paymentDateImmutable = \DateTimeImmutable::createFromInterface($paymentDate);

        $payment = new PaymentRecipe();
        $payment->setRentalRecipe($rentalRecipe)
            ->setMaturityDate($paymentDateImmutable)
            ->setPayableAmount($rentalRecipe->getFullPaymentForMonth($paymentDateImmutable));

        $this->paymentRepository->persist($payment);

        return $payment;
    }
}
