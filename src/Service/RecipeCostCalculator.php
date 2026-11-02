<?php

namespace App\Service;

use App\Entity\Recipe;

final class RecipeCostCalculator
{
    public function __construct(private readonly UnitConverter $converter) {}

    public function calculate(Recipe $recipe): string
    {
        return number_format($this->calculateValue($recipe, []), 2, '.', '');
    }

    private function calculateValue(Recipe $recipe, array $path): float
    {
        $key = $recipe->getId() ? 'id:'.$recipe->getId() : 'object:'.spl_object_id($recipe);
        if (isset($path[$key])) {
            throw new \DomainException('Dependencia circular detectada en la receta "'.$recipe->getName().'".');
        }
        $path[$key] = true;
        $total = 0.0;

        foreach ($recipe->getIngredients() as $line) {
            if (!$line->getIngredient() || !$line->getUnit()) continue;
            $total += (float) $this->converter->ingredientCost($line->getIngredient(), $line->getQuantity(), $line->getUnit());
        }

        foreach ($recipe->getComponents() as $component) {
            $child = $component->getComponentRecipe();
            if (!$child || !$component->getUnit() || !$child->getYieldUnit()) continue;
            $usedYield = (float) $this->converter->convert($component->getQuantity(), $component->getUnit(), $child->getYieldUnit());
            $total += $this->calculateValue($child, $path) * $usedYield / (float) $child->getYieldQuantity();
        }

        return $total;
    }

    public function refresh(Recipe $recipe): void
    {
        $recipe->setCalculatedCost($this->calculate($recipe));
        $recipe->setCostCalculatedAt(new \DateTimeImmutable());
    }
}
