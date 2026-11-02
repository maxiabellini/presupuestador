<?php
namespace App\Controller\Admin;

use App\Entity\AdditionalCost;
use App\Entity\Ingredient;
use App\Entity\IngredientCategory;
use App\Entity\Quote;
use App\Entity\Recipe;
use App\Entity\Unit;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'home')]
    public function home(): Response { return $this->redirectToRoute('admin'); }
    public function index(): Response { return $this->render('admin/dashboard.html.twig'); }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span class="brand-lockup"><img src="/images/logo-dulce-costo.png" alt=""><span>Jazmín Pastelería</span></span>')
            ->setFaviconPath('/images/logo-dulce-costo.png')
            ->disableDarkMode()
            ->renderContentMaximized();
    }
    public function configureAssets(): Assets { return Assets::new()->addCssFile('styles/app.css'); }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Inicio', 'fa fa-home');
        yield MenuItem::section('Presupuestar');
        yield MenuItem::linkTo(QuoteCrudController::class, 'Presupuestos', 'fa fa-file-invoice-dollar');
        yield MenuItem::linkTo(RecipeCrudController::class, 'Recetas', 'fa fa-cake-candles');
        yield MenuItem::section('Costos');
        yield MenuItem::linkTo(IngredientCrudController::class, 'Insumos', 'fa fa-basket-shopping');
        yield MenuItem::linkTo(AdditionalCostCrudController::class, 'Gastos adicionales', 'fa fa-bolt');
        yield MenuItem::section('Configuración');
        yield MenuItem::linkTo(IngredientCategoryCrudController::class, 'Categorías', 'fa fa-tags');
        yield MenuItem::linkTo(UnitCrudController::class, 'Unidades', 'fa fa-scale-balanced');
        yield MenuItem::linkToLogout('Cerrar sesión', 'fa fa-sign-out-alt');
    }
}
