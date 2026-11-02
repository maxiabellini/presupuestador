<?php
namespace App\Form;

use App\Entity\Recipe;
use App\Entity\RecipeComponent;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeComponentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void { $b->add('componentRecipe', EntityType::class, ['class'=>Recipe::class,'label'=>'Preparación'])->add('quantity', NumberType::class, ['label'=>'Cantidad','scale'=>4])->add('unit', EntityType::class, ['class'=>Unit::class,'label'=>'Unidad']); }
    public function configureOptions(OptionsResolver $r): void { $r->setDefaults(['data_class'=>RecipeComponent::class]); }
}
