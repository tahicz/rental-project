<?php

namespace App\Controller\Admin;

use App\Entity\RentalRecipe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
        yield MoneyField::new('basicRent')
            ->setRequired(true)
            ->setCurrency('CZK')
            ->setStoredAsCents(false);
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
}
