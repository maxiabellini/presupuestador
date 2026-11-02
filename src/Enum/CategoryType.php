<?php
namespace App\Enum;

enum CategoryType: string
{
    case INGREDIENT = 'ingredient';
    case MATERIAL = 'material';

    public function label(): string { return $this === self::INGREDIENT ? 'Ingrediente' : 'Material / embalaje'; }
}
