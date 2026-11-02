<?php
namespace App\Enum;

enum ProfitMode: string
{
    case MARKUP = 'markup';
    case MARGIN = 'margin';

    public function label(): string { return $this === self::MARKUP ? 'Recargo sobre costo' : 'Margen sobre precio de venta'; }
}
