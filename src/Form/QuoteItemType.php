<?php
namespace App\Form;

use App\Entity\QuoteItem;
use App\Entity\Recipe;
use App\Entity\Unit;
use App\Enum\QuoteItemMode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('name', TextType::class, ['label'=>'Producto'])->add('mode', EnumType::class, ['class'=>QuoteItemMode::class,'choice_label'=>fn($v)=>$v->label(),'label'=>'Modalidad'])
            ->add('sourceRecipe', EntityType::class, ['class'=>Recipe::class,'required'=>false,'placeholder'=>'Sin receta base','label'=>'Receta base'])
            ->add('quantity', NumberType::class, ['label'=>'Cantidad de productos','scale'=>2])->add('yieldQuantity', NumberType::class, ['label'=>'Rendimiento','scale'=>2])
            ->add('yieldUnit', EntityType::class, ['class'=>Unit::class,'label'=>'Unidad de rendimiento'])
            ->add('salePrice', NumberType::class, ['label'=>'Precio de venta independiente','required'=>false,'scale'=>2,'help'=>'Opcional: precio específico para este producto.'])
            ->add('ingredients', CollectionType::class, ['entry_type'=>QuoteItemIngredientType::class,'allow_add'=>true,'allow_delete'=>true,'by_reference'=>false,'label'=>'Insumos (copia histórica)']);
    }
    public function configureOptions(OptionsResolver $r): void { $r->setDefaults(['data_class'=>QuoteItem::class]); }
}
