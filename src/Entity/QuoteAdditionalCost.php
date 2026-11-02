<?php

namespace App\Entity;

use App\Enum\CostCalculationMethod;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class QuoteAdditionalCost
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'additionalCosts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Quote $quote = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?AdditionalCost $sourceCost = null;
    #[ORM\Column(length: 120)]
    private string $name = '';
    #[ORM\Column(enumType: CostCalculationMethod::class)]
    private CostCalculationMethod $method = CostCalculationMethod::FIXED;
    #[ORM\Column]
    private bool $enabled = true;
    #[ORM\Column]
    private bool $labor = false;
    #[ORM\Column]
    private bool $shared = true;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 6)]
    #[Assert\PositiveOrZero]
    private string $quantity = '1';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    private string $rateSnapshot = '0';
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $amount = '0';

    public function getId(): ?int { return $this->id; }
    public function getQuote(): ?Quote { return $this->quote; }
    public function setQuote(?Quote $v): self { $this->quote = $v; return $this; }
    public function getSourceCost(): ?AdditionalCost { return $this->sourceCost; }
    public function setSourceCost(?AdditionalCost $v): self { $this->sourceCost = $v; if ($v) { $this->name = $v->getName(); $this->method = $v->getMethod(); $this->labor = $v->isLabor(); $this->shared = $v->isShared(); $this->rateSnapshot = $v->getMethod() === CostCalculationMethod::DEPRECIATION && $v->getUsefulLifeUses() ? (string) ((float) $v->getAcquisitionValue() / $v->getUsefulLifeUses()) : $v->getRate(); } return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): self { $this->name = $v; return $this; }
    public function getMethod(): CostCalculationMethod { return $this->method; }
    public function setMethod(CostCalculationMethod $v): self { $this->method = $v; return $this; }
    public function isEnabled(): bool { return $this->enabled; }
    public function setEnabled(bool $v): self { $this->enabled = $v; return $this; }
    public function isLabor(): bool { return $this->labor; }
    public function setLabor(bool $v): self { $this->labor = $v; return $this; }
    public function isShared(): bool { return $this->shared; }
    public function setShared(bool $v): self { $this->shared = $v; return $this; }
    public function getQuantity(): string { return $this->quantity; }
    public function setQuantity(string $v): self { $this->quantity = $v; return $this; }
    public function getRateSnapshot(): string { return $this->rateSnapshot; }
    public function setRateSnapshot(string $v): self { $this->rateSnapshot = $v; return $this; }
    public function getAmount(): string { return $this->amount; }
    public function setAmount(string $v): self { $this->amount = $v; return $this; }
    public function __toString(): string { return $this->name; }
}
