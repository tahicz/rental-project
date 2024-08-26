<?php

namespace App\Enum;

use App\Enum\Traits\EnumToArray;

enum PaymentFrequencyEnum: string
{
    use EnumToArray;

    case MONTHLY = 'monthly';
    case ANNUALLY = 'annually';
}
