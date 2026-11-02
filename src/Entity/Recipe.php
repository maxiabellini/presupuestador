<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Recipe
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 160, unique: true)]
    #[Assert\NotBlank]
    private string $name = '';
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    #[Assert\Positive]
    private string $yieldQuantity = '1';
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $yieldUnit = null;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $instructions = null;
    #[ORM\Column(type: 'decimal', precision: 18, scale: 4)]
    private string $calculatedCost = '0';
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $costCalculatedAt = null;
    /** @var Collection<int, RecipeIngredient> */
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: RecipeIngredient::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $ingredients;
    /** @var Collection<int, RecipeComponent> */
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: RecipeComponent::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $components;

    public function __construct() { $this->ingredients = new ArrayCollection(); $this->components = new ArrayCollection(); }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $v): self { $this->name = trim($v); return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): self { $this->description = $v; return $this; }
    public function getYieldQuantity(): string { return $this->yieldQuantity; }
    public function setYieldQuantity(string $v): self { $this->yieldQuantity = $v; return $this; }
    public function getYieldUnit(): ?Unit { return $this->yieldUnit; }
    public function setYieldUnit(?Unit $v): self { $this->yieldUnit = $v; return $this; }
    public function getInstructions(): ?string { return $this->instructions; }
    public function setInstructions(?string $v): self { $this->instructions = $v; return $this; }
    public function getCalculatedCost(): string { return $this->calculatedCost; }
    public function setCalculatedCost(string $v): self { $this->calculatedCost = $v; return $this; }
    public function getCostCalculatedAt(): ?\DateTimeImmutable { return $this->costCalculatedAt; }
    public function setCostCalculatedAt(?\DateTimeImmutable $v): self { $this->costCalculatedAt = $v; return $this; }
    public function getIngredients(): Collection { return $this->ingredients; }
    public function addIngredient(RecipeIngredient $v): self { if (!$this->ingredients->contains($v)) { $this->ingredients->add($v); $v->setRecipe($this); } return $this; }
    public function removeIngredient(RecipeIngredient $v): self { $this->ingredients->removeElement($v); return $this; }
    public function getComponents(): Collection { return $this->components; }
    public function addComponent(RecipeComponent $v): self { if (!$this->components->contains($v)) { $this->components->add($v); $v->setRecipe($this); } return $this; }
    public function removeComponent(RecipeComponent $v): self { $this->components->removeElement($v); return $this; }
    public function __toString(): string { return $this->name; }
}
