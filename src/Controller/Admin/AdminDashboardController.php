<?php

namespace App\Controller\Admin;

use App\Entity\AdditionalFee;
use App\Entity\BankAccount;
use App\Entity\Income;
use App\Entity\PaymentRecipe;
use App\Entity\PaymentRecord;
use App\Entity\RentalRecipe;
use App\Enum\SystemEnum;
use App\Repository\IncomeRepository;
use App\Repository\PaymentRecipeRepository;
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
        private readonly PaymentRecipeRepository $paymentRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly IncomeRepository $incomeRepository
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $today = new \DateTimeImmutable('today');

        $rents = $this->rentalRecipeRepository->findAll();

        $paymentsDueSum = $this->paymentRepository->getPaymentsDueSum($today);
        $paymentsDueCount = $this->paymentRepository->getPaymentsDueCount($today);
        $paymentsActuallyMade = $this->incomeRepository->findAll();
        $nextDue = $this->paymentRepository->getNextPaymentDue($today);

        $incomeSum = 0.0;
        array_walk($paymentsActuallyMade, function (Income $income) use (&$incomeSum) {
            $incomeSum += $income->getAmount();
        });

        if (empty($rents)) {
            $generateNextPaymentsUrl = '';
        } else {
            $generateNextPaymentsUrl = $this->adminUrlGenerator
                ->unsetAll()
                ->setController(RentalRecipeCrudController::class)
                ->setAction('generateNewPayments')
                ->setEntityId($rents[0]->getId())
                ->generateUrl()
            ;
        }

        return $this->render('admin/main_dashboard.html.twig', [
            'rentalRecipes' => $rents,
            'payments' => [
                'dueSum' => $paymentsDueSum,
                'dueCount' => $paymentsDueCount,
                'actuallyMadeSum' => $incomeSum,
                'actuallyMadeCount' => count($paymentsActuallyMade),
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
            ->setTranslationDomain(SystemEnum::TRANSLATION_DOMAIN->value)
            ->setTextDirection('ltr')
            ->renderContentMaximized()
            ->generateRelativeUrls(false);
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
                    MenuItem::linkToCrud('Income', 'fa-solid fa-wallet', Income::class),
                    MenuItem::linkToCrud('Payment recipes', 'fa-solid fa-wallet', PaymentRecipe::class),
                    MenuItem::linkToCrud('Payment records', 'fa-solid fa-dollar', PaymentRecord::class),
                ]
            );
        yield MenuItem::subMenu('User settings', 'fas fa-cogs')
            ->setSubItems(
                [
                    MenuItem::linkToCrud('Bank', 'fa-solid fa-bank', BankAccount::class),
                ]
            );
    }
}
