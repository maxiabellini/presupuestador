<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class RecipeComponent
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(inversedBy: 'components')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Recipe $recipe = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $componentRecipe = null;
    /** Cantidad del rendimiento del componente que se utiliza. */
    #[ORM\Column(type: 'decimal', precision: 18, scale: 6)]
    #[Assert\Positive]
    private string $quantity = '1';
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $unit = null;

    public function getId(): ?int { return $this->id; }
    public function getRecipe(): ?Recipe { return $this->recipe; }
    public function setRecipe(?Recipe $v): self { $this->recipe = $v; return $this; }
    public function getComponentRecipe(): ?Recipe { return $this->componentRecipe; }
    public function setComponentRecipe(?Recipe $v): self { $this->componentRecipe = $v; return $this; }
    public function getQuantity(): string { return $this->quantity; }
    public function setQuantity(string $v): self { $this->quantity = $v; return $this; }
    public function getUnit(): ?Unit { return $this->unit; }
    public function setUnit(?Unit $v): self { $this->unit = $v; return $this; }
    public function __toString(): string { return sprintf('%s %s de %s', $this->quantity, $this->unit?->getSymbol(), $this->componentRecipe?->getName()); }
}
