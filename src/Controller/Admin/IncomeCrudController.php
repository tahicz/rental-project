<?php

namespace App\Controller\Admin;

use App\Entity\Income;
use App\Enum\SystemEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IncomeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Income::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('amount', 'Amount')
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false);
        yield DateField::new('incomeDate', 'Income date');
        yield IntegerField::new('variableSymbol', 'Variable symbol')
        ->onlyOnForms();
        yield TextField::new('formatedVariableSymbol', 'Variable symbol')
        ->hideOnForm();
        yield TextField::new('message');
        yield AssociationField::new('recipientAccount', 'Recipient account');
        yield AssociationField::new('senderAccount', 'Sender account');

        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }
}
