<?php

namespace App\Service;

use App\Entity\PaymentRecipe;
use App\Entity\RentalRecipe;
use App\Helper\PaymentHelper;
use App\Repository\PaymentRecipeRepository;

readonly class PaymentPlanner
{
    public function __construct(
        private PaymentRecipeRepository $paymentRecipeRepository
    ) {
    }

    public function planFuturePayments(RentalRecipe $rentalRecipe, int $paymentCount = 1): void
    {
        /** @var PaymentRecipe|null $lastPayment */
        $lastPayment = $this->paymentRecipeRepository->findOneBy(['rentalRecipe' => $rentalRecipe->getId()], ['id' => 'DESC']);
        if (null === $lastPayment) {
            $firstRecipePayment = $rentalRecipe->getRecipePayment()->first();
            if (false === $firstRecipePayment) {
                throw new \LogicException('No recipe payment set.');
            }

            $paymentDate = PaymentHelper::createPaymentDate($firstRecipePayment->getValidityFrom(), $firstRecipePayment->getMaturity());

            $lastPayment = $this->createPayment($rentalRecipe, $paymentDate);
        }

        for ($i = 0; $i < $paymentCount; ++$i) {
            $paymentDate = \DateTime::createFromImmutable($lastPayment->getMaturityDate());
            $paymentDate->modify('+1 month');

            $lastPayment = $this->createPayment($rentalRecipe, $paymentDate);
        }

        $this->paymentRecipeRepository->flush();
    }

    private function createPayment(RentalRecipe $rentalRecipe, \DateTimeInterface $paymentDate): PaymentRecipe
    {
        $paymentDateImmutable = \DateTimeImmutable::createFromInterface($paymentDate);

        $payment = new PaymentRecipe();
        $payment->setRentalRecipe($rentalRecipe)
            ->setMaturityDate($paymentDateImmutable)
            ->setPayableAmount($rentalRecipe->getFullPaymentForMonth($paymentDateImmutable));

        $this->paymentRecipeRepository->persist($payment);

        return $payment;
    }

    public function updatePaymentsRecipes(RentalRecipe $rentalRecipe, \DateTime $from): void
    {
        /** @var PaymentRecipe[] $payments */
        $payments = $this->paymentRecipeRepository->getPaymentsFrom($rentalRecipe, $from);
        foreach ($payments as $payment) {
            $payment->setPayableAmount($rentalRecipe->getFullPaymentForMonth($payment->getMaturityDate()));
            $this->paymentRecipeRepository->persist($payment);
        }
        $this->paymentRecipeRepository->flush();
    }
}
