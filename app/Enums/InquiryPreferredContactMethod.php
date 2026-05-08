<?php

namespace App\Enums;

enum InquiryPreferredContactMethod: string
{
    case Email = 'email';
    case Phone = 'phone';
    case Whatsapp = 'whatsapp';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $method): string => $method->value,
            self::cases(),
        );
    }
}
