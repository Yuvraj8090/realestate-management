<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Company = 'company';
    case PropertyOwner = 'property_owner';
    case Broker = 'broker';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Company => 'Company / Firm',
            self::PropertyOwner => 'Property Owner',
            self::Broker => 'Broker / Agent',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases(),
        );
    }

    /**
     * @return list<self>
     */
    public static function registrationRoles(): array
    {
        return [
            self::Company,
            self::PropertyOwner,
            self::Broker,
        ];
    }
}
