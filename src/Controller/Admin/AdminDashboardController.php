<?php

namespace App\Controller\Admin;

use App\Entity\AdditionalFee;
use App\Entity\RentalRecipe;
use App\Repository\RentalRecipeRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractDashboardController
{
    public function __construct(private RentalRecipeRepository $rentalRecipeRepository)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $rents = $this->rentalRecipeRepository->findAll();

        return $this->render('admin/main_dashboard.html.twig', [
            'rentalRecipes' => $rents,
        ]);
    }

    public function configureActions(): Actions
    {
        $actions = parent::configureActions();
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EstateRent')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('estate-rent-admin')
            ->setTextDirection('ltr')
            ->renderContentMaximized()
            ->generateRelativeUrls();
    }

    public function configureCrud(): Crud
    {
        $crud = parent::configureCrud();
        $crud->setDateFormat(DateTimeField::FORMAT_MEDIUM)
            ->setDateTimeFormat(DateTimeField::FORMAT_MEDIUM)
            ->setThousandsSeparator(' ')
            ->setDecimalSeparator(',');

        return $crud;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('Rental', 'fa-solid fa-sign-hanging')
            ->setSubItems(
                [
                    MenuItem::linkToCrud('Rental recipe', 'fa-solid fa-ticket', RentalRecipe::class),
                    MenuItem::linkToCrud('Additional fee', 'fa-solid fa-comment-dollar', AdditionalFee::class),
                ]
            );
    }
}
