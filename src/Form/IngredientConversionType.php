<?php
namespace App\Form;

use App\Entity\IngredientUnitConversion;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientConversionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void { $b->add('fromUnit', EntityType::class, ['class'=>Unit::class,'label'=>'Desde'])->add('factor', NumberType::class, ['label'=>'Equivale a','scale'=>6])->add('toUnit', EntityType::class, ['class'=>Unit::class,'label'=>'Unidad destino']); }
    public function configureOptions(OptionsResolver $r): void { $r->setDefaults(['data_class'=>IngredientUnitConversion::class]); }
}
