<?php

declare(strict_types=1);

namespace App\Enum\Traits;

use function Symfony\Component\Translation\t;

trait EnumToArray
{
    /**
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * @return array<int, int|string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, int|string>
     */
    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    /**
     * @return array<int|string, int|string>
     */
    public static function choices(): array
    {
        return array_combine(self::values(), self::values());
    }

    public static function translateableChoices(): array
    {
        $values = self::values();
        $values = array_map(function ($value) {
            return t(static::class.'.'.$value);
        }, $values);

        return array_combine($values, self::values());
    }
}
