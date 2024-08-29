<?php

namespace App\Controller\Admin;

use App\Entity\AdditionalFee;
use App\Entity\Overpayment;
use App\Entity\PaymentRecipe;
use App\Entity\PaymentRecord;
use App\Entity\RentalRecipe;
use App\Repository\PaymentRepository;
use App\Repository\RentalRecipeRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly RentalRecipeRepository $rentalRecipeRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $today = new \DateTimeImmutable('today');

        $rents = $this->rentalRecipeRepository->findAll();

        $paymentsDueSum = $this->paymentRepository->getPaymentsDueSum($today);
        $paymentsDueCount = $this->paymentRepository->getPaymentsDueCount($today);
        $paymentsActuallyMadeSum = $this->paymentRepository->getPaymentsActuallyMadeSum($today);
        $paymentsActuallyMadeCount = $this->paymentRepository->getPaymentsActuallyMadeCount($today);
        $nextDue = $this->paymentRepository->getNextPaymentDue($today);

        $generateNextPaymentsUrl = $this->adminUrlGenerator
            ->unsetAll()
            ->setController(RentalRecipeCrudController::class)
            ->setAction('generateNewPayments')
            ->setEntityId($rents[0]->getId())
            ->generateUrl()
        ;

        return $this->render('admin/main_dashboard.html.twig', [
            'rentalRecipes' => $rents,
            'payments' => [
                'dueSum' => $paymentsDueSum,
                'dueCount' => $paymentsDueCount,
                'actuallyMadeSum' => $paymentsActuallyMadeSum,
                'actuallyMadeCount' => $paymentsActuallyMadeCount,
                'nextDue' => $nextDue,
                'generateNextUrl' => $generateNextPaymentsUrl,
            ],
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
        yield MenuItem::subMenu('Finance', 'fa-solid fa-magnifying-glass-dollar')
            ->setSubItems(
                [
                    MenuItem::linkToCrud('Payment recipes', 'fa-solid fa-wallet', PaymentRecipe::class),
                    MenuItem::linkToCrud('Payment records', 'fa-solid fa-dollar', PaymentRecord::class),
                    MenuItem::linkToCrud('Overpayments', 'fa-solid fa-circle-dollar-to-slot', Overpayment::class),
                ]
            );
    }
}
