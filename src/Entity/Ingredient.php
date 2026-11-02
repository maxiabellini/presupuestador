<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Ingredient
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 140, unique: true)]
    #[Assert\NotBlank]
    private string $name = '';
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?IngredientCategory $category = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 6)]
    #[Assert\Positive]
    private string $purchaseQuantity = '1';
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $purchaseUnit = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    #[Assert\PositiveOrZero]
    private string $purchasePrice = '0';
    /** Costo por unidad base (gramo, mililitro o unidad). */
    #[ORM\Column(type: 'decimal', precision: 20, scale: 8)]
    private string $normalizedUnitCost = '0';
    #[ORM\Column]
    private \DateTimeImmutable $priceUpdatedAt;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;
    /** @var Collection<int, IngredientUnitConversion> */
    #[ORM\OneToMany(mappedBy: 'ingredient', targetEntity: IngredientUnitConversion::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $conversions;

    public function __construct() { $this->priceUpdatedAt = new \DateTimeImmutable(); $this->conversions = new ArrayCollection(); }
    #[ORM\PrePersist, ORM\PreUpdate]
    public function normalizeCost(): void
    {
        if ($this->purchaseUnit && (float) $this->purchaseQuantity > 0) {
            $baseAmount = (float) $this->purchaseQuantity * (float) $this->purchaseUnit->getToBaseFactor();
            $this->normalizedUnitCost = number_format((float) $this->purchasePrice / $baseAmount, 8, '.', '');
        }
    }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getCategory(): ?IngredientCategory { return $this->category; }
    public function setCategory(?IngredientCategory $category): self { $this->category = $category; return $this; }
    public function getPurchaseQuantity(): string { return $this->purchaseQuantity; }
    public function setPurchaseQuantity(string $value): self { $this->purchaseQuantity = $value; return $this; }
    public function getPurchaseUnit(): ?Unit { return $this->purchaseUnit; }
    public function setPurchaseUnit(?Unit $unit): self { $this->purchaseUnit = $unit; return $this; }
    public function getPurchasePrice(): string { return $this->purchasePrice; }
    public function setPurchasePrice(string $value): self { if ($value !== $this->purchasePrice) $this->priceUpdatedAt = new \DateTimeImmutable(); $this->purchasePrice = $value; return $this; }
    public function getNormalizedUnitCost(): string { return $this->normalizedUnitCost; }
    public function setNormalizedUnitCost(string $cost): self { $this->normalizedUnitCost = $cost; return $this; }
    public function getPriceUpdatedAt(): \DateTimeImmutable { return $this->priceUpdatedAt; }
    public function setPriceUpdatedAt(\DateTimeImmutable $date): self { $this->priceUpdatedAt = $date; return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }
    public function getConversions(): Collection { return $this->conversions; }
    public function addConversion(IngredientUnitConversion $conversion): self { if (!$this->conversions->contains($conversion)) { $this->conversions->add($conversion); $conversion->setIngredient($this); } return $this; }
    public function removeConversion(IngredientUnitConversion $conversion): self { $this->conversions->removeElement($conversion); return $this; }
    public function __toString(): string { return $this->name; }
}
