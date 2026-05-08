<?php

namespace App\Enums;

enum PropertyListingSource: string
{
    case Company = 'company';
    case Owner = 'owner';
    case Broker = 'broker';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $source): string => $source->value,
            self::cases(),
        );
    }
}
