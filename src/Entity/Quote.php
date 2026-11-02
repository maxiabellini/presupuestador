<?php

namespace App\Entity;

use App\Enum\ProfitMode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'quote_document')]
class Quote
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 30, unique: true)]
    private string $number = '';
    #[ORM\Column(length: 160)]
    #[Assert\NotBlank]
    private string $customerName = 'Consumidor final';
    #[ORM\Column(length: 30)]
    private string $status = 'draft';
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $validUntil = null;
    #[ORM\Column(enumType: ProfitMode::class)]
    private ProfitMode $profitMode = ProfitMode::MARKUP;
    #[ORM\Column(type: 'decimal', precision: 8, scale: 3)]
    #[Assert\Range(min: 0, max: 99.999)]
    private string $profitPercentage = '30';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2, nullable: true)]
    private ?string $manualFinalPrice = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $ingredientCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $materialCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $additionalCostTotal = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $laborCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $totalCost = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $suggestedPrice = '0';
    #[ORM\Column(type: 'decimal', precision: 8, scale: 3)]
    private string $resultingMargin = '0';
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $calculatedAt = null;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;
    /** @var Collection<int, QuoteItem> */
    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $items;
    /** @var Collection<int, QuoteAdditionalCost> */
    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteAdditionalCost::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $additionalCosts;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); $this->items = new ArrayCollection(); $this->additionalCosts = new ArrayCollection(); }
    public function getId(): ?int { return $this->id; }
    public function getNumber(): string { return $this->number; }
    public function setNumber(string $v): self { $this->number = $v; return $this; }
    public function getCustomerName(): string { return $this->customerName; }
    public function setCustomerName(string $v): self { $this->customerName = trim($v); return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $v): self { $this->status = $v; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $v): self { $this->createdAt = $v; return $this; }
    public function getValidUntil(): ?\DateTimeImmutable { return $this->validUntil; }
    public function setValidUntil(?\DateTimeImmutable $v): self { $this->validUntil = $v; return $this; }
    public function getProfitMode(): ProfitMode { return $this->profitMode; }
    public function setProfitMode(ProfitMode $v): self { $this->profitMode = $v; return $this; }
    public function getProfitPercentage(): string { return $this->profitPercentage; }
    public function setProfitPercentage(string $v): self { $this->profitPercentage = $v; return $this; }
    public function getManualFinalPrice(): ?string { return $this->manualFinalPrice; }
    public function setManualFinalPrice(?string $v): self { $this->manualFinalPrice = $v; return $this; }
    public function getIngredientCost(): string { return $this->ingredientCost; }
    public function setIngredientCost(string $v): self { $this->ingredientCost = $v; return $this; }
    public function getMaterialCost(): string { return $this->materialCost; }
    public function setMaterialCost(string $v): self { $this->materialCost = $v; return $this; }
    public function getAdditionalCostTotal(): string { return $this->additionalCostTotal; }
    public function setAdditionalCostTotal(string $v): self { $this->additionalCostTotal = $v; return $this; }
    public function getLaborCost(): string { return $this->laborCost; }
    public function setLaborCost(string $v): self { $this->laborCost = $v; return $this; }
    public function getTotalCost(): string { return $this->totalCost; }
    public function setTotalCost(string $v): self { $this->totalCost = $v; return $this; }
    public function getSuggestedPrice(): string { return $this->suggestedPrice; }
    public function setSuggestedPrice(string $v): self { $this->suggestedPrice = $v; return $this; }
    public function getResultingMargin(): string { return $this->resultingMargin; }
    public function setResultingMargin(string $v): self { $this->resultingMargin = $v; return $this; }
    public function getCalculatedAt(): ?\DateTimeImmutable { return $this->calculatedAt; }
    public function setCalculatedAt(?\DateTimeImmutable $v): self { $this->calculatedAt = $v; return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $v): self { $this->notes = $v; return $this; }
    public function getItems(): Collection { return $this->items; }
    public function addItem(QuoteItem $v): self { if (!$this->items->contains($v)) { $this->items->add($v); $v->setQuote($this); } return $this; }
    public function removeItem(QuoteItem $v): self { $this->items->removeElement($v); return $this; }
    public function getAdditionalCosts(): Collection { return $this->additionalCosts; }
    public function addAdditionalCost(QuoteAdditionalCost $v): self { if (!$this->additionalCosts->contains($v)) { $this->additionalCosts->add($v); $v->setQuote($this); } return $this; }
    public function removeAdditionalCost(QuoteAdditionalCost $v): self { $this->additionalCosts->removeElement($v); return $this; }
    public function getFinalPrice(): string { return $this->manualFinalPrice ?? $this->suggestedPrice; }
    public function __toString(): string { return sprintf('%s - %s', $this->number ?: 'Nuevo', $this->customerName); }
}
