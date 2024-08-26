<?php

namespace App\Enum;

use App\Enum\Traits\EnumToArray;

enum AdditionalFeeEnum: string
{
    use EnumToArray;
    case AQUEOUS = 'aqueous'; // vodné
    case WATER_AND_SEWAGE = 'water-and-sewage'; // vodné a stočné
    case ELECTRICITY = 'electricity'; // elektřina
    case GAS = 'gas'; // plyn
    case MUNICIPAL_WASTE = 'municipal-waste'; // komunální odpad
}
