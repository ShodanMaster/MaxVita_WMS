<?php

namespace App\Enums;

enum PermissionLevel: int
{
    case TOP_LEVEL = 1;
    case ORGANIZATION_LEVEL = 2;
    case LOCATION_LEVEL = 3;

    public function label(): string
    {
        return match($this) {
            self::TOP_LEVEL
                => 'Top Level',
            self::ORGANIZATION_LEVEL
                => 'Organization Level',
            self::LOCATION_LEVEL
                => 'Location Level',
        };
    }

    public function labels(): array
    {
        return [
            self::TOP_LEVEL->value => self::TOP_LEVEL->label(),
            self::ORGANIZATION_LEVEL->value  => self::ORGANIZATION_LEVEL->label(),
            self::LOCATION_LEVEL->value  => self::LOCATION_LEVEL->label(),
        ];
    }
}
