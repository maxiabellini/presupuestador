<?php

namespace App\Entity;

use App\Enum\CategoryType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class IngredientCategory
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank]
    private string $name = '';
    #[ORM\Column(enumType: CategoryType::class)]
    private CategoryType $type = CategoryType::INGREDIENT;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getType(): CategoryType { return $this->type; }
    public function setType(CategoryType $type): self { $this->type = $type; return $this; }
    public function __toString(): string { return $this->name; }
}
