<?php

namespace App\Entity;

use App\Enum\Dimension;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity('code')]
class Unit
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    private string $name = '';
    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    private string $code = '';
    #[ORM\Column(length: 20)]
    private string $symbol = '';
    #[ORM\Column(enumType: Dimension::class)]
    private Dimension $dimension = Dimension::CUSTOM;
    /** Multiplicador para llegar a la unidad base de la dimensión. */
    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    #[Assert\Positive]
    private string $toBaseFactor = '1';
    #[ORM\Column]
    private bool $active = true;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getCode(): string { return $this->code; }
    public function setCode(string $code): self { $this->code = strtolower(trim($code)); return $this; }
    public function getSymbol(): string { return $this->symbol; }
    public function setSymbol(string $symbol): self { $this->symbol = trim($symbol); return $this; }
    public function getDimension(): Dimension { return $this->dimension; }
    public function setDimension(Dimension $dimension): self { $this->dimension = $dimension; return $this; }
    public function getToBaseFactor(): string { return $this->toBaseFactor; }
    public function setToBaseFactor(string $factor): self { $this->toBaseFactor = $factor; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }
    public function __toString(): string { return sprintf('%s (%s)', $this->name, $this->symbol); }
}
