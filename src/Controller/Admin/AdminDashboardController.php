<?php

namespace App\Controller\Admin;

use App\Entity\RentalRecipe;
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
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/main_dashboard.html.twig');
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
            ->setFaviconPath('favicon.svg')
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
                    MenuItem::linkToCrud('Rent recipe', 'fa-solid fa-ticket', RentalRecipe::class),
                ]
            );
    }
}
