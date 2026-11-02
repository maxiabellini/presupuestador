<?php
namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeComponentType;
use App\Form\RecipeIngredientType;
use App\Service\RecipeCostCalculator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
use Symfony\Component\HttpFoundation\RedirectResponse;

class RecipeCrudController extends AbstractCrudController
{
    public function __construct(private readonly RecipeCostCalculator $calculator) {}
    public static function getEntityFqcn(): string { return Recipe::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Receta')->setEntityLabelInPlural('Recetas'); }
    public function configureActions(Actions $actions): Actions
    {
        $refresh = Action::new('recalculateAll', 'Actualizar todos los costos', 'fa fa-rotate')->createAsGlobalAction()->linkToCrudAction('recalculateAll');
        return $actions->add(Crud::PAGE_INDEX, $refresh);
    }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex(); yield TextField::new('name','Nombre'); yield TextareaField::new('description','Descripción')->hideOnIndex();
        yield NumberField::new('yieldQuantity','Rendimiento')->setNumDecimals(2); yield AssociationField::new('yieldUnit','Unidad del rendimiento');
        yield MoneyField::new('calculatedCost','Costo actual')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm(); yield DateTimeField::new('costCalculatedAt','Calculado')->hideOnForm();
        yield CollectionField::new('ingredients','Ingredientes')->setEntryType(RecipeIngredientType::class)->allowAdd()->allowDelete()->setFormTypeOption('by_reference', false)->hideOnIndex();
        yield CollectionField::new('components','Preparaciones reutilizables')->setEntryType(RecipeComponentType::class)->allowAdd()->allowDelete()->setFormTypeOption('by_reference', false)->hideOnIndex();
        yield TextareaField::new('instructions','Instrucciones')->hideOnIndex();
    }
    public function persistEntity(EntityManagerInterface $em, object $entity): void { if ($entity instanceof Recipe) $this->calculator->refresh($entity); parent::persistEntity($em,$entity); }
    public function updateEntity(EntityManagerInterface $em, object $entity): void { if ($entity instanceof Recipe) $this->calculator->refresh($entity); parent::updateEntity($em,$entity); }

    #[AdminRoute('/recalculate-all', name: 'recalculate_all', options: ['methods' => ['GET']])]
    public function recalculateAll(EntityManagerInterface $em): RedirectResponse
    {
        foreach ($em->getRepository(Recipe::class)->findAll() as $recipe) $this->calculator->refresh($recipe);
        $em->flush();
        $this->addFlash('success', 'Se actualizaron los costos de todas las recetas con los precios vigentes.');
        return $this->redirectToRoute('admin_recipe_index');
    }
}
