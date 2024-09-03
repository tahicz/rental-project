<?php

namespace App\Controller\Admin;

use App\Entity\PaymentRecord;
use App\Enum\SystemEnum;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class PaymentRecordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentRecord::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('amount', 'Amount')
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false);
        yield DateField::new('receivedOn', 'Received on');
        yield AssociationField::new('paymentRecipe', 'Payment recipe')
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->andWhere('entity.payableAmount > entity.paidAmount');
            })
            ->setSortProperty('maturityDate');
        yield AssociationField::new('income')
        ->setSortProperty('incomeDate')
        ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
            $queryBuilder->leftJoin(PaymentRecord::class, 'pr', Join::WITH, 'pr.income = entity.id')
            ->andWhere('pr.amount is null');
        });

        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInPlural('Payment records')
            ->setEntityLabelInSingular('Payment record')
            ->setPageTitle(Crud::PAGE_INDEX, '%entity_label_singular% list')
            ->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% detail');

        return $crud;
    }
}
