<?php

namespace App\Controller\Admin;

use App\Entity\AdditionalFee;
use App\Enum\AdditionalFeeEnum;
use App\Enum\PaymentFrequencyEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class AdditionalFeeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdditionalFee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield ChoiceField::new('description')
            ->setRequired(true)
            ->setChoices(AdditionalFeeEnum::translateAbleChoices());
        yield CollectionField::new('additionalFeePayments')
            ->setTemplatePath('admin/field/additional_fee/detail/additional_fee_payment.html.twig')
            ->allowAdd()
            ->allowDelete(false)
            ->setEntryIsComplex(true)
            ->useEntryCrudForm(AdditionalFeePaymentCrudController::class);
        yield ChoiceField::new('paymentFrequency', 'Payment frequency')
            ->setChoices(PaymentFrequencyEnum::translateAbleChoices())
            ->setRequired(true);
        yield BooleanField::new('billable')
            ->renderAsSwitch(in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true));
        yield AssociationField::new('rentRecipe');
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInPlural('Additional fees')
            ->setEntityLabelInSingular('Additional fee')
            ->setPageTitle(Crud::PAGE_INDEX, '%entity_label_singular% list')
            ->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% detail');

        return $crud;
    }
}
