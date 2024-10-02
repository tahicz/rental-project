<?php

namespace App\Service;

use App\Entity\PaymentRecipe;
use App\Entity\RentalRecipe;
use App\Helper\PaymentHelper;
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
            $paymentDate = PaymentHelper::createPaymentDate($rentalRecipe->getValidityFrom(), $rentalRecipe->getMaturity());

            $lastPayment = $this->createPayment($rentalRecipe, $paymentDate);
        }

        for ($i = 0; $i < $paymentCount; ++$i) {
            $paymentDate = \DateTime::createFromImmutable($lastPayment->getMaturityDate());
            $paymentDate->modify('+1 month');

            if ($rentalRecipe->getValidityTo() < $paymentDate) {
                if (null === $rentalRecipe->getChild()) {
                    break;
                }

                $rentalRecipe = $rentalRecipe->getChild();
            }

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
