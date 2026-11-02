<?php
namespace App\Controller\Admin;

use App\Entity\Quote;
use App\Enum\ProfitMode;
use App\Form\QuoteAdditionalCostType;
use App\Form\QuoteItemType;
use App\Service\QuoteCalculator;
use App\Service\QuotePdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class QuoteCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly QuoteCalculator $calculator,
        private readonly QuotePdfGenerator $pdfGenerator,
        private readonly RequestStack $requestStack,
    ) {}
    public static function getEntityFqcn(): string { return Quote::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Presupuesto')->setEntityLabelInPlural('Presupuestos')->setDefaultSort(['createdAt'=>'DESC']); }
    public function configureActions(Actions $actions): Actions
    {
        $recalc = Action::new('recalculateCurrent','Recalcular con precios actuales','fa fa-rotate')->linkToCrudAction('recalculateCurrent')->displayIf(fn(Quote $q)=>$q->getId() !== null);
        $pdf = Action::new('downloadPdf', 'Ver PDF', 'fa fa-file-pdf')
            ->linkToCrudAction('downloadPdf')
            ->setHtmlAttributes(['target' => '_blank', 'rel' => 'noopener'])
            ->displayIf(fn (Quote $quote) => null !== $quote->getId());

        return $actions
            ->add(Crud::PAGE_INDEX, $pdf)
            ->add(Crud::PAGE_EDIT, $pdf)
            ->add(Crud::PAGE_DETAIL, $pdf)
            ->add(Crud::PAGE_INDEX,$recalc)
            ->add(Crud::PAGE_EDIT,$recalc)
            ->add(Crud::PAGE_DETAIL,$recalc)
            ->add(Crud::PAGE_INDEX,Action::DETAIL);
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('number', 'Número'))
            ->add(TextFilter::new('customerName', 'Cliente / referencia'))
            ->add(ChoiceFilter::new('status', 'Estado')->setChoices([
                'Borrador' => 'draft',
                'Enviado' => 'sent',
                'Aceptado' => 'accepted',
                'Rechazado' => 'rejected',
            ]))
            ->add(DateTimeFilter::new('createdAt', 'Fecha de creación'))
            ->add(DateTimeFilter::new('validUntil', 'Válido hasta'))
            ->add(NumericFilter::new('totalCost', 'Costo total'))
            ->add(NumericFilter::new('suggestedPrice', 'Precio sugerido'));
    }
    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            yield TextField::new('number', 'Número');
            yield TextField::new('customerName', 'Cliente');
            yield ChoiceField::new('status', 'Estado')->setChoices(['Borrador'=>'draft','Enviado'=>'sent','Aceptado'=>'accepted','Rechazado'=>'rejected']);
            yield MoneyField::new('ingredientCost', 'Ingredientes')->setCurrency('ARS')->setStoredAsCents(false);
            yield MoneyField::new('materialCost', 'Materiales')->setCurrency('ARS')->setStoredAsCents(false);
            yield MoneyField::new('laborCost', 'Mano de obra')->setCurrency('ARS')->setStoredAsCents(false);
            yield MoneyField::new('additionalCostTotal', 'Otros gastos')->setCurrency('ARS')->setStoredAsCents(false);
            yield MoneyField::new('totalCost', 'Costo total')->setCurrency('ARS')->setStoredAsCents(false);
            yield MoneyField::new('finalPrice', 'Precio final')->setCurrency('ARS')->setStoredAsCents(false)->setSortable(false);

            return;
        }

        yield FormField::addColumn(8); yield TextField::new('number','Número')->hideWhenCreating()->setFormTypeOption('disabled',true);
        yield TextField::new('customerName','Cliente / referencia'); yield ChoiceField::new('status','Estado')->setChoices(['Borrador'=>'draft','Enviado'=>'sent','Aceptado'=>'accepted','Rechazado'=>'rejected']);
        yield DateTimeField::new('createdAt','Creado')->hideOnForm(); yield DateField::new('validUntil','Válido hasta'); yield TextareaField::new('notes','Notas')->hideOnIndex();
        yield ChoiceField::new('profitMode','Forma de ganancia')->setChoices(['Recargo sobre costo'=>ProfitMode::MARKUP,'Margen sobre venta'=>ProfitMode::MARGIN]);
        yield NumberField::new('profitPercentage','Porcentaje deseado')->setHelp('Recargo: costo × (1 + %). Margen: costo ÷ (1 - %).'); yield MoneyField::new('manualFinalPrice','Precio final manual')->setCurrency('ARS')->setStoredAsCents(false)->setHelp('Dejar vacío para usar el sugerido.');
        yield CollectionField::new('items','Productos')->setEntryType(QuoteItemType::class)->allowAdd()->allowDelete()->setFormTypeOption('by_reference',false)->hideOnIndex();
        yield CollectionField::new('additionalCosts','Gastos adicionales')->setEntryType(QuoteAdditionalCostType::class)->allowAdd()->allowDelete()->setFormTypeOption('by_reference',false)->hideOnIndex();
        yield FormField::addColumn(4); yield MoneyField::new('ingredientCost','Ingredientes')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm();
        yield MoneyField::new('materialCost','Materiales')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm(); yield MoneyField::new('laborCost','Mano de obra')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm();
        yield MoneyField::new('additionalCostTotal','Otros gastos')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm(); yield MoneyField::new('totalCost','Costo total')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm();
        yield MoneyField::new('suggestedPrice','Precio sugerido')->setCurrency('ARS')->setStoredAsCents(false)->hideOnForm(); yield MoneyField::new('finalPrice','Precio final')->setCurrency('ARS')->setStoredAsCents(false)->onlyOnDetail();
        yield NumberField::new('resultingMargin','Margen resultante (%)')->hideOnForm(); yield DateTimeField::new('calculatedAt','Último cálculo')->hideOnForm();
    }
    public function persistEntity(EntityManagerInterface $em, object $entity): void
    {
        if ($entity instanceof Quote) { $entity->setNumber('P-'.date('Ymd-His').'-'.random_int(10,99)); $this->prepare($entity); }
        parent::persistEntity($em,$entity);
    }
    public function updateEntity(EntityManagerInterface $em, object $entity): void { if ($entity instanceof Quote) $this->prepare($entity); parent::updateEntity($em,$entity); }
    private function prepare(Quote $quote): void
    {
        foreach ($quote->getItems() as $item) if ($item->getSourceRecipe() && $item->getIngredients()->isEmpty()) $this->calculator->loadRecipeSnapshot($item,$item->getSourceRecipe());
        $this->calculator->calculate($quote);
    }
    #[AdminRoute('/{id}/recalculate-current', name: 'recalculate_current', options: ['methods' => ['GET']])]
    public function recalculateCurrent(Quote $quote, EntityManagerInterface $em): RedirectResponse
    {
        $this->calculator->calculate($quote,true); $em->flush(); $this->addFlash('success','Presupuesto recalculado explícitamente con los precios actuales.');
        return $this->redirect($this->requestStack->getCurrentRequest()?->headers->get('referer') ?? $this->generateUrl('admin'));
    }

    #[AdminRoute('/{id}/pdf', name: 'download_pdf', options: ['methods' => ['GET']])]
    public function downloadPdf(Quote $quote): Response
    {
        $response = new Response($this->pdfGenerator->generate($quote));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            'inline',
            sprintf('presupuesto-%s.pdf', $quote->getNumber()),
        ));

        return $response;
    }
}
