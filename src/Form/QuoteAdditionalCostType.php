<?php
namespace App\Form;

use App\Entity\AdditionalCost;
use App\Entity\QuoteAdditionalCost;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteAdditionalCostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void { $b->add('sourceCost', EntityType::class, ['class'=>AdditionalCost::class,'label'=>'Concepto'])->add('enabled', CheckboxType::class, ['required'=>false,'label'=>'Aplicar'])->add('quantity', NumberType::class, ['label'=>'Cantidad / horas / usos','scale'=>3]); }
    public function configureOptions(OptionsResolver $r): void { $r->setDefaults(['data_class'=>QuoteAdditionalCost::class]); }
}
