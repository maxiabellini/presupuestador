<?php

namespace App\Entity;

use App\Enum\QuoteItemMode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class QuoteItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Quote $quote = null;
    #[ORM\Column(length: 160)]
    #[Assert\NotBlank]
    private string $name = '';
    #[ORM\Column(enumType: QuoteItemMode::class)]
    private QuoteItemMode $mode = QuoteItemMode::CUSTOM;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Recipe $sourceRecipe = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    #[Assert\Positive]
    private string $quantity = '1';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    #[Assert\Positive]
    private string $yieldQuantity = '1';
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $yieldUnit = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $ingredientCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $materialCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $totalCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2, nullable: true)]
    private ?string $salePrice = null;
    /** @var Collection<int, QuoteItemIngredient> */
    #[ORM\OneToMany(mappedBy: 'quoteItem', targetEntity: QuoteItemIngredient::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $ingredients;

    public function __construct() { $this->ingredients = new ArrayCollection(); }
    public function getId(): ?int { return $this->id; }
    public function getQuote(): ?Quote { return $this->quote; }
    public function setQuote(?Quote $v): self { $this->quote = $v; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): self { $this->name = trim($v); return $this; }
    public function getMode(): QuoteItemMode { return $this->mode; }
    public function setMode(QuoteItemMode $v): self { $this->mode = $v; return $this; }
    public function getSourceRecipe(): ?Recipe { return $this->sourceRecipe; }
    public function setSourceRecipe(?Recipe $v): self { $this->sourceRecipe = $v; return $this; }
    public function getQuantity(): string { return $this->quantity; }
    public function setQuantity(string $v): self { $this->quantity = $v; return $this; }
    public function getYieldQuantity(): string { return $this->yieldQuantity; }
    public function setYieldQuantity(string $v): self { $this->yieldQuantity = $v; return $this; }
    public function getYieldUnit(): ?Unit { return $this->yieldUnit; }
    public function setYieldUnit(?Unit $v): self { $this->yieldUnit = $v; return $this; }
    public function getIngredientCost(): string { return $this->ingredientCost; }
    public function setIngredientCost(string $v): self { $this->ingredientCost = $v; return $this; }
    public function getMaterialCost(): string { return $this->materialCost; }
    public function setMaterialCost(string $v): self { $this->materialCost = $v; return $this; }
    public function getTotalCost(): string { return $this->totalCost; }
    public function setTotalCost(string $v): self { $this->totalCost = $v; return $this; }
    public function getSalePrice(): ?string { return $this->salePrice; }
    public function setSalePrice(?string $v): self { $this->salePrice = $v; return $this; }
    public function getIngredients(): Collection { return $this->ingredients; }
    public function addIngredient(QuoteItemIngredient $v): self { if (!$this->ingredients->contains($v)) { $this->ingredients->add($v); $v->setQuoteItem($this); } return $this; }
    public function removeIngredient(QuoteItemIngredient $v): self { $this->ingredients->removeElement($v); return $this; }
    public function __toString(): string { return $this->name ?: 'Producto'; }
}
