<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\Unit;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

final class UnitConverter
{
    public function convert(string $quantity, Unit $from, Unit $to, ?Ingredient $ingredient = null): string
    {
        if ($from === $to || $from->getCode() === $to->getCode()) {
            return $quantity;
        }

        if ($from->getDimension() === $to->getDimension() && $from->getDimension()->value !== 'custom') {
            return $this->divide(BigDecimal::of($quantity)->multipliedBy($from->getToBaseFactor()), $to->getToBaseFactor());
        }

        if ($ingredient) {
            foreach ($ingredient->getConversions() as $conversion) {
                if ($conversion->getFromUnit() === $from && $conversion->getToUnit() === $to) {
                    return (string) BigDecimal::of($quantity)->multipliedBy($conversion->getFactor())->strippedOfTrailingZeros();
                }
                if ($conversion->getFromUnit() === $to && $conversion->getToUnit() === $from) {
                    return $this->divide(BigDecimal::of($quantity), $conversion->getFactor());
                }
            }
        }

        throw new \DomainException(sprintf('No existe una conversión válida entre %s y %s%s.', $from->getName(), $to->getName(), $ingredient ? ' para '.$ingredient->getName() : ''));
    }

    public function ingredientCost(Ingredient $ingredient, string $quantity, Unit $unit): string
    {
        $purchaseUnit = $ingredient->getPurchaseUnit() ?? throw new \DomainException('El insumo no tiene unidad de compra.');
        $inPurchaseUnit = $this->convert($quantity, $unit, $purchaseUnit, $ingredient);
        return (string) BigDecimal::of($inPurchaseUnit)
            ->dividedBy($ingredient->getPurchaseQuantity(), 10, RoundingMode::HALF_UP)
            ->multipliedBy($ingredient->getPurchasePrice())
            ->toScale(2, RoundingMode::HALF_UP);
    }

    private function divide(BigDecimal $value, string $divisor): string
    {
        return (string) $value->dividedBy($divisor, 8, RoundingMode::HALF_UP)->strippedOfTrailingZeros();
    }
}
