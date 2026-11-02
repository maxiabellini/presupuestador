<?php
namespace App\Form;

use App\Entity\RecipeIngredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Ingredient;
use App\Entity\Unit;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void { $b->add('ingredient', EntityType::class, ['class'=>Ingredient::class, 'label'=>'Insumo'])->add('quantity', NumberType::class, ['label'=>'Cantidad','scale'=>4])->add('unit', EntityType::class, ['class'=>Unit::class,'label'=>'Unidad']); }
    public function configureOptions(OptionsResolver $r): void { $r->setDefaults(['data_class'=>RecipeIngredient::class]); }
}
