<?php

namespace App\Controller\Admin;

use App\Entity\AdditionalFeePayment;
use App\Enum\SystemEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdditionalFeePaymentCrudController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('amount', 'Amount')
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false);
        yield TextField::new('note');
        yield DateField::new('validityFrom');
        yield DateField::new('validityTo');
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public static function getEntityFqcn(): string
    {
        return AdditionalFeePayment::class;
    }
}
