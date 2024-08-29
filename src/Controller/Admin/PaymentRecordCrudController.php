<?php

namespace App\Controller\Admin;

use App\Entity\Overpayment;
use App\Entity\PaymentRecord;
use App\Enum\SystemEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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
        yield DateField::new('paymentDate');
        yield AssociationField::new('paymentRecipe')
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->andWhere('entity.paidAmount <> entity.payableAmount');
            });

        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->updateRecipe($entityManager, $entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->updateRecipe($entityManager, $entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * @todo Tady by bylo lepší tuto fci volat až po uložení platby do DTB a následně projít všechny platby k předpisu platby a sčítnout zaplacené sumy.
     */
    private function updateRecipe(EntityManagerInterface $entityManager, PaymentRecord $record): void
    {
        $paidAmount = $record->getPaymentRecipe()->getPaidAmount();
        if (null === $paidAmount) {
            $paidAmount = 0.0;
        }

        $paidAmount += $record->getAmount();

        if ($paidAmount > $record->getPaymentRecipe()->getPayableAmount()) {
            $overpayment = $paidAmount - $record->getPaymentRecipe()->getPayableAmount();
            $this->saveOverpayment($overpayment, $record, $entityManager);
        }
        $record->getPaymentRecipe()->setPaidAmount($paidAmount);

        if ($paidAmount === $record->getPaymentRecipe()->getPayableAmount()) {
            $record->getPaymentRecipe()->setPaymentDate(\DateTimeImmutable::createFromMutable($record->getCreatedAt()));
        }
    }

    private function saveOverpayment(float $overpaymentAmount, PaymentRecord $record, EntityManagerInterface $entityManager): void
    {
        $overpayment = new Overpayment();
        $overpayment->setAmount($overpaymentAmount)
        ->setPaymentRecord($record);

        $entityManager->persist($overpayment);
    }
}
