<?php

namespace App\Enums;

enum UserType: int
{
    case STANDARD_USER = 1;
    case ADMINISTRATOR = 2;

    public function label(): string
    {
        return match($this) {
            self::STANDARD_USER
                => 'Standard User',
            self::ADMINISTRATOR
                => 'Administrator',
        };
    }

    public function labels(): array
    {
        return [
            self::STANDARD_USER->value => self::STANDARD_USER->label(),
            self::ADMINISTRATOR->value  => self::ADMINISTRATOR->label(),
        ];
    }
}
