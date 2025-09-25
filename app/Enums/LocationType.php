<?php

namespace App\Enums;

enum LocationType: int
{
    case QC_PENDING = 1;
    case STORAGE = 2;
    case REJECTION = 3;
    case DISPATCH = 4;

    public function label(): string
    {
        return match($this) {
            self::QC_PENDING => 'QC Pending',
            self::STORAGE => 'Storage',
            self::REJECTION => 'Rejection',
            self::DISPATCH => 'Dispatch',
        };
    }

    public static function labels(): array
    {
        return [
            self::QC_PENDING->value => self::QC_PENDING->label(),
            self::STORAGE->value    => self::STORAGE->label(),
            self::REJECTION->value  => self::REJECTION->label(),
            self::DISPATCH->value   => self::DISPATCH->label(),
        ];
    }
}

