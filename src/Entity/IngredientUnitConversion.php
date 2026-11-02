<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'ingredient_unit_unique', columns: ['ingredient_id', 'from_unit_id', 'to_unit_id'])]
class IngredientUnitConversion
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'conversions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Ingredient $ingredient = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $fromUnit = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $toUnit = null;
    /** Una unidad de origen equivale a este valor en la unidad destino. */
    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    #[Assert\Positive]
    private string $factor = '1';

    public function getId(): ?int { return $this->id; }
    public function getIngredient(): ?Ingredient { return $this->ingredient; }
    public function setIngredient(?Ingredient $v): self { $this->ingredient = $v; return $this; }
    public function getFromUnit(): ?Unit { return $this->fromUnit; }
    public function setFromUnit(?Unit $v): self { $this->fromUnit = $v; return $this; }
    public function getToUnit(): ?Unit { return $this->toUnit; }
    public function setToUnit(?Unit $v): self { $this->toUnit = $v; return $this; }
    public function getFactor(): string { return $this->factor; }
    public function setFactor(string $v): self { $this->factor = $v; return $this; }
    public function __toString(): string { return sprintf('1 %s = %s %s', $this->fromUnit?->getSymbol(), $this->factor, $this->toUnit?->getSymbol()); }
}
