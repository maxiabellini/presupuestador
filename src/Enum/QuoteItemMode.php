<?php
namespace App\Enum;

enum QuoteItemMode: string
{
    case RECIPE = 'recipe';
    case CUSTOM = 'custom';
    case MODIFIED_RECIPE = 'modified_recipe';

    public function label(): string { return match ($this) { self::RECIPE => 'Receta predeterminada', self::CUSTOM => 'Desde cero', self::MODIFIED_RECIPE => 'Receta con modificaciones' }; }
}
