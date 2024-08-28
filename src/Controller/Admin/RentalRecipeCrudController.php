<?php

namespace App\Controller\Admin;

use App\Entity\RentalRecipe;
use App\Enum\SystemEnum;
use App\Service\PaymentPlanner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class RentalRecipeCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PaymentPlanner $paymentPlanner,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return RentalRecipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('basicRent', 'Basic rent')
            ->setRequired(true)
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false);
        yield CollectionField::new('additionalFees', 'Additional fees')
            ->hideOnIndex()
            ->useEntryCrudForm(AdditionalFeeCrudController::class)
            ->allowAdd();
        yield IntegerField::new('maturity')
            ->setFormTypeOptions([
                'attr' => [
                    'min' => 1,
                    'max' => 28,
                ],
            ])
            ->setRequired(true);
        yield DateField::new('validityFrom');
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInPlural('Rental recipes')
            ->setEntityLabelInSingular('Rental recipe')
            ->setPageTitle(Crud::PAGE_INDEX, '%entity_label_singular% list')
            ->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% detail');

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $generateNextPaymentsAction = Action::new('new_payments_action', 'Generate new payments', 'fa-solid fa-file-invoice-dollar');
        $generateNextPaymentsAction->linkToCrudAction('generateNewPayments');
        $actions->add(Crud::PAGE_DETAIL, $generateNextPaymentsAction);

        return $actions;
    }

    public function generateNewPayments(AdminContext $context): Response
    {
        $rentalRecipe = $context->getEntity()->getInstance();
        try {
            $this->paymentPlanner->planFuturePayments($rentalRecipe, 12);
            $this->addFlash('success', 'Generated next 12 payments.');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Some future payments are not generated. Error:'.$exception->getMessage());
        } finally {
            $url = $this->adminUrlGenerator
                ->unsetAll()
                ->setController(RentalRecipeCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($rentalRecipe->getId());

            return $this->redirect($url->generateUrl());
        }
    }
}
