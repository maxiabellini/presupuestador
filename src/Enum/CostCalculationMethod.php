<?php
namespace App\Enum;

enum CostCalculationMethod: string
{
    case FIXED = 'fixed';
    case QUANTITY = 'quantity';
    case TIME = 'time';
    case DEPRECIATION = 'depreciation';

    public function label(): string { return match ($this) { self::FIXED => 'Importe fijo', self::QUANTITY => 'Por cantidad', self::TIME => 'Por hora/minuto', self::DEPRECIATION => 'Depreciación por uso' }; }
}
