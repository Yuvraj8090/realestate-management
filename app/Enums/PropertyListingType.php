<?php

namespace App\Enums;

enum PropertyListingType: string
{
    case Rent = 'rent';
    case Sale = 'sale';
    case ShortStay = 'short_stay';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $type): string => $type->value,
            self::cases(),
        );
    }
}
