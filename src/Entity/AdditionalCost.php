<?php

namespace App\Entity;

use App\Enum\CostCalculationMethod;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class AdditionalCost
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 120, unique: true)]
    #[Assert\NotBlank]
    private string $name = '';
    #[ORM\Column(enumType: CostCalculationMethod::class)]
    private CostCalculationMethod $method = CostCalculationMethod::FIXED;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    #[Assert\PositiveOrZero]
    private string $rate = '0';
    #[ORM\Column(length: 60, nullable: true)]
    private ?string $rateUnit = null;
    #[ORM\Column]
    private bool $labor = false;
    #[ORM\Column]
    private bool $shared = true;
    #[ORM\Column]
    private bool $active = true;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4, nullable: true)]
    private ?string $acquisitionValue = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $usefulLifeUses = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): self { $this->name = trim($v); return $this; }
    public function getMethod(): CostCalculationMethod { return $this->method; }
    public function setMethod(CostCalculationMethod $v): self { $this->method = $v; return $this; }
    public function getRate(): string { return $this->rate; }
    public function setRate(string $v): self { $this->rate = $v; return $this; }
    public function getRateUnit(): ?string { return $this->rateUnit; }
    public function setRateUnit(?string $v): self { $this->rateUnit = $v; return $this; }
    public function isLabor(): bool { return $this->labor; }
    public function setLabor(bool $v): self { $this->labor = $v; return $this; }
    public function isShared(): bool { return $this->shared; }
    public function setShared(bool $v): self { $this->shared = $v; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $v): self { $this->active = $v; return $this; }
    public function getAcquisitionValue(): ?string { return $this->acquisitionValue; }
    public function setAcquisitionValue(?string $v): self { $this->acquisitionValue = $v; return $this; }
    public function getUsefulLifeUses(): ?int { return $this->usefulLifeUses; }
    public function setUsefulLifeUses(?int $v): self { $this->usefulLifeUses = $v; return $this; }
    public function __toString(): string { return $this->name; }
}
