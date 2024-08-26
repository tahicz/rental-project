<?php

namespace App\Controller\Admin;

use App\Entity\RentalRecipe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class RentalRecipeCrudController extends AbstractCrudController
{
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
            ->setCurrency('CZK')
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
}
