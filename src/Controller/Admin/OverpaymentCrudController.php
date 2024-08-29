<?php

namespace App\Controller\Admin;

use App\Entity\Overpayment;
use App\Enum\SystemEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OverpaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Overpayment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('amount', 'Amount')
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false);
		yield AssociationField::new('paymentRecord', 'Payment');
    }

	public function configureCrud(Crud $crud): Crud
	{
		$crud->setEntityLabelInPlural('Overpayments')
			->setEntityLabelInSingular('Overpayment')
			->setPageTitle(Crud::PAGE_INDEX, '%entity_label_singular% list')
			->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% detail');

		return $crud;
	}
}
