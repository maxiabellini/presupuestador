<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class QuoteItemIngredient
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?QuoteItem $quoteItem = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Ingredient $ingredient = null;
    #[ORM\Column(length: 160)]
    private string $ingredientName = '';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 6)]
    #[Assert\Positive]
    private string $quantity = '1';
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Unit $unit = null;
    #[ORM\Column(length: 30)]
    private string $unitSymbol = '';
    #[ORM\Column(length: 20)]
    private string $categoryType = 'ingredient';
    #[ORM\Column(type: 'decimal', precision: 20, scale: 8)]
    private string $unitCostSnapshot = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $lineCost = '0';

    public function getId(): ?int { return $this->id; }
    public function getQuoteItem(): ?QuoteItem { return $this->quoteItem; }
    public function setQuoteItem(?QuoteItem $v): self { $this->quoteItem = $v; return $this; }
    public function getIngredient(): ?Ingredient { return $this->ingredient; }
    public function setIngredient(?Ingredient $v): self { $this->ingredient = $v; if ($v) { $this->ingredientName = $v->getName(); $this->categoryType = $v->getCategory()?->getType()->value ?? 'ingredient'; } return $this; }
    public function getIngredientName(): string { return $this->ingredientName; }
    public function setIngredientName(string $v): self { $this->ingredientName = $v; return $this; }
    public function getQuantity(): string { return $this->quantity; }
    public function setQuantity(string $v): self { $this->quantity = $v; return $this; }
    public function getUnit(): ?Unit { return $this->unit; }
    public function setUnit(?Unit $v): self { $this->unit = $v; if ($v) $this->unitSymbol = $v->getSymbol(); return $this; }
    public function getUnitSymbol(): string { return $this->unitSymbol; }
    public function setUnitSymbol(string $v): self { $this->unitSymbol = $v; return $this; }
    public function getCategoryType(): string { return $this->categoryType; }
    public function setCategoryType(string $v): self { $this->categoryType = $v; return $this; }
    public function getUnitCostSnapshot(): string { return $this->unitCostSnapshot; }
    public function setUnitCostSnapshot(string $v): self { $this->unitCostSnapshot = $v; return $this; }
    public function getLineCost(): string { return $this->lineCost; }
    public function setLineCost(string $v): self { $this->lineCost = $v; return $this; }
    public function __toString(): string { return sprintf('%s %s de %s', $this->quantity, $this->unitSymbol, $this->ingredientName); }
}
