<?php

namespace App\Helper;

class PaymentHelper
{
    private static ?\NumberFormatter $nf = null;

    public static function createPaymentDate(\DateTime|\DateTimeImmutable $paymentDate, int $maturity): \DateTime
    {
        if ($paymentDate instanceof \DateTimeImmutable) {
            $paymentDate = \DateTime::createFromImmutable($paymentDate);
        }

        $paymentDate->modify('first day of this month')
            ->modify('+'.($maturity - 1).' day');

        return $paymentDate;
    }

    public static function getFormatedCurrency(float $amount, string $currency): string|false
    {
        if (null === self::$nf) {
            self::$nf = new \NumberFormatter('cs_CZ', \NumberFormatter::CURRENCY);
        }

        return self::$nf->formatCurrency($amount, $currency);
    }
}
