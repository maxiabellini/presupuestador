<?php

namespace App\Service;

use App\Entity\Quote;
use App\Entity\QuoteItem;
use App\Entity\QuoteItemIngredient;
use App\Entity\Recipe;
use App\Enum\CategoryType;
use App\Enum\ProfitMode;

final class QuoteCalculator
{
    public function __construct(private readonly UnitConverter $converter) {}

    public function loadRecipeSnapshot(QuoteItem $item, Recipe $recipe): void
    {
        while (($line = $item->getIngredients()->first()) !== false) $item->removeIngredient($line);
        $item->setSourceRecipe($recipe)->setName($recipe->getName())->setYieldQuantity($recipe->getYieldQuantity())->setYieldUnit($recipe->getYieldUnit());
        $this->appendRecipe($item, $recipe, 1.0, []);
    }

    private function appendRecipe(QuoteItem $item, Recipe $recipe, float $scale, array $path): void
    {
        $key = $recipe->getId() ? 'id:'.$recipe->getId() : 'object:'.spl_object_id($recipe);
        if (isset($path[$key])) throw new \DomainException('Dependencia circular al copiar la receta.');
        $path[$key] = true;

        foreach ($recipe->getIngredients() as $source) {
            if (!$source->getIngredient() || !$source->getUnit()) continue;
            $snapshot = (new QuoteItemIngredient())
                ->setIngredient($source->getIngredient())
                ->setUnit($source->getUnit())
                ->setQuantity((string) ((float) $source->getQuantity() * $scale))
                ->setUnitCostSnapshot($source->getIngredient()->getNormalizedUnitCost());
            $snapshot->setLineCost($this->converter->ingredientCost($source->getIngredient(), $snapshot->getQuantity(), $source->getUnit()));
            $item->addIngredient($snapshot);
        }

        foreach ($recipe->getComponents() as $component) {
            $child = $component->getComponentRecipe();
            if (!$child || !$component->getUnit() || !$child->getYieldUnit()) continue;
            $used = (float) $this->converter->convert($component->getQuantity(), $component->getUnit(), $child->getYieldUnit());
            $this->appendRecipe($item, $child, $scale * $used / (float) $child->getYieldQuantity(), $path);
        }
    }

    public function calculate(Quote $quote, bool $useCurrentPrices = false): void
    {
        $ingredientTotal = $materialTotal = 0.0;
        foreach ($quote->getItems() as $item) {
            $itemIngredient = $itemMaterial = 0.0;
            foreach ($item->getIngredients() as $line) {
                if (!$useCurrentPrices && $line->getIngredient() && $line->getUnit() && (float) $line->getLineCost() === 0.0) {
                    $line->setIngredient($line->getIngredient())->setUnit($line->getUnit());
                    $line->setUnitCostSnapshot($line->getIngredient()->getNormalizedUnitCost());
                    $line->setLineCost($this->converter->ingredientCost($line->getIngredient(), $line->getQuantity(), $line->getUnit()));
                }
                if ($useCurrentPrices && $line->getIngredient() && $line->getUnit()) {
                    $line->setIngredient($line->getIngredient());
                    $line->setUnit($line->getUnit());
                    $line->setUnitCostSnapshot($line->getIngredient()->getNormalizedUnitCost());
                    $line->setLineCost($this->converter->ingredientCost($line->getIngredient(), $line->getQuantity(), $line->getUnit()));
                }
                $lineTotal = (float) $line->getLineCost() * (float) $item->getQuantity();
                if ($line->getCategoryType() === CategoryType::MATERIAL->value) $itemMaterial += $lineTotal; else $itemIngredient += $lineTotal;
            }
            $item->setIngredientCost($this->money($itemIngredient));
            $item->setMaterialCost($this->money($itemMaterial));
            $item->setTotalCost($this->money($itemIngredient + $itemMaterial));
            $ingredientTotal += $itemIngredient;
            $materialTotal += $itemMaterial;
        }

        $labor = $additional = 0.0;
        foreach ($quote->getAdditionalCosts() as $cost) {
            if (!$cost->isEnabled()) { $cost->setAmount('0'); continue; }
            if ($useCurrentPrices && $cost->getSourceCost()) $cost->setSourceCost($cost->getSourceCost());
            $amount = (float) $cost->getRateSnapshot() * (float) $cost->getQuantity();
            $cost->setAmount($this->money($amount));
            if ($cost->isLabor()) $labor += $amount; else $additional += $amount;
        }

        $total = $ingredientTotal + $materialTotal + $labor + $additional;
        $percentage = (float) $quote->getProfitPercentage() / 100;
        $suggested = $quote->getProfitMode() === ProfitMode::MARGIN
            ? ($percentage >= 1 ? 0 : $total / (1 - $percentage))
            : $total * (1 + $percentage);
        $final = $quote->getManualFinalPrice() !== null ? (float) $quote->getManualFinalPrice() : $suggested;
        $margin = $final > 0 ? (($final - $total) / $final) * 100 : 0;

        $quote->setIngredientCost($this->money($ingredientTotal))->setMaterialCost($this->money($materialTotal))
            ->setLaborCost($this->money($labor))->setAdditionalCostTotal($this->money($additional))
            ->setTotalCost($this->money($total))->setSuggestedPrice($this->money($suggested))
            ->setResultingMargin(number_format($margin, 3, '.', ''))->setCalculatedAt(new \DateTimeImmutable());
    }

    private function money(float $value): string { return number_format($value, 2, '.', ''); }
}
