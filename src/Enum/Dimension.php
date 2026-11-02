<?php

namespace App\Enum;

enum Dimension: string
{
    case MASS = 'mass';
    case VOLUME = 'volume';
    case COUNT = 'count';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::MASS => 'Masa', self::VOLUME => 'Volumen', self::COUNT => 'Cantidad', self::CUSTOM => 'Personalizada',
        };
    }
}
