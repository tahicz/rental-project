<?php

namespace App\Controller\Admin;

use App\Entity\PaymentRecipe;
use App\Enum\SystemEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class PaymentRecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentRecipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('payableAmount', 'Payable amount')
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setRequired(true)
            ->setStoredAsCents(false)
            ->hideOnForm();
        yield DateField::new('maturityDate', 'Maturity date');
        yield MoneyField::new('paidAmount', 'Paid amount')
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false);
        yield DateField::new('paymentDate', 'Payment date')
            ->hideWhenCreating();
        yield AssociationField::new('rentalRecipe', 'Rental recipe')
            ->hideOnIndex();
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInPlural('Payments')
            ->setEntityLabelInSingular('Payment')
            ->setPageTitle(Crud::PAGE_INDEX, '%entity_label_singular% list')
            ->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% detail');

        return $crud;
    }

    /**
     * @param PaymentRecipe $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof PaymentRecipe) {
            throw new \RuntimeException('Expected instance of Payment, got '.get_class($entityInstance));
        }

        $this->setPaymentAmount($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    private function setPaymentAmount(PaymentRecipe $payment): void
    {
        if (null === $payment->getRentalRecipe()) {
            $amount = 0.0;
        } else {
            $amount = $payment->getRentalRecipe()->getFullMonthlyRate();
        }
        $payment->setPayableAmount($amount);
    }

    /**
     * @param PaymentRecipe $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof PaymentRecipe) {
            throw new \RuntimeException('Expected instance of Payment, got '.get_class($entityInstance));
        }

        $this->setPaymentAmount($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }
}
