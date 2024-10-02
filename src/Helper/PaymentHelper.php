<?php

namespace App\Helper;

class PaymentHelper
{
    public static function createPaymentDate(\DateTime|\DateTimeImmutable $paymentDate, int $maturity): \DateTime
    {
        if ($paymentDate instanceof \DateTimeImmutable) {
            $paymentDate = \DateTime::createFromImmutable($paymentDate);
        }

        $paymentDate->modify('first day of this month')
            ->modify('+'.($maturity - 1).' day');

        return $paymentDate;
    }
}
