<?php
namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Form\IngredientConversionType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Ingredient::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Insumo')->setEntityLabelInPlural('Insumos'); }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex(); yield TextField::new('name','Nombre'); yield AssociationField::new('category','Categoría');
        yield NumberField::new('purchaseQuantity','Cantidad comprada')->setNumDecimals(3); yield AssociationField::new('purchaseUnit','Unidad de compra');
        yield MoneyField::new('purchasePrice','Precio de compra')->setCurrency('ARS')->setStoredAsCents(false);
        yield NumberField::new('normalizedUnitCost','Costo por unidad base')->setNumDecimals(6)->hideOnForm();
        yield DateTimeField::new('priceUpdatedAt','Precio actualizado')->hideOnForm(); yield TextareaField::new('notes','Información adicional')->hideOnIndex();
        yield CollectionField::new('conversions','Equivalencias específicas')->setEntryType(IngredientConversionType::class)->allowAdd()->allowDelete()->setFormTypeOption('by_reference', false)->hideOnIndex();
    }
}
