<?php

namespace App\EntityListener;

use App\Entity\PaymentRecipe;
use App\Entity\PaymentRecord;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;

#[AsEntityListener(event: Events::postUpdate, method: 'onPostUpdate', entity: PaymentRecord::class)]
#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: PaymentRecord::class)]
#[AsEntityListener(event: Events::postRemove, method: 'onPostRemove', entity: PaymentRecord::class)]
class PaymentRecordListener
{
    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function onPostPersist(PaymentRecord $paymentRecord, LifecycleEventArgs $event): void
    {
        $recipe = $paymentRecord->getPaymentRecipe();
        if (null === $recipe) {
            return;
        }
        $this->recountPaidAmount($recipe);
        if ($recipe->getPaidAmount() === $recipe->getPayableAmount()) {
            $recipe->setPaymentDate($paymentRecord->getReceivedOn());
        }

        $event->getObjectManager()->flush();
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function onPostUpdate(PaymentRecord $paymentRecord, LifecycleEventArgs $event): void
    {
        $recipe = $paymentRecord->getPaymentRecipe();
        if (null === $recipe) {
            return;
        }
        $this->recountPaidAmount($recipe);

        if ($recipe->getPaidAmount() !== $recipe->getPayableAmount()) {
            $recipe->setPaymentDate(null);
        }
        if ($recipe->getPaidAmount() === $recipe->getPayableAmount()) {
            $recipe->setPaymentDate($paymentRecord->getReceivedOn());
        }
        $event->getObjectManager()->flush();
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function onPostRemove(PaymentRecord $paymentRecord, LifecycleEventArgs $event): void
    {
        $recipe = $paymentRecord->getPaymentRecipe();
        if (null === $recipe) {
            return;
        }
        $this->recountPaidAmount($recipe);
        if ($recipe->getPaidAmount() !== $recipe->getPayableAmount()) {
            $recipe->setPaymentDate(null);
        }
        if ($recipe->getPaidAmount() === $recipe->getPayableAmount()) {
            $recipe->setPaymentDate($paymentRecord->getReceivedOn());
        }

        $event->getObjectManager()->flush();
    }

    private function recountPaidAmount(PaymentRecipe $recipe): void
    {
        $paidAmount = 0.0;
        foreach ($recipe->getPaymentRecords() as $record) {
            $paidAmount += $record->getAmount();
        }
        if ($paidAmount > $recipe->getPayableAmount()) {
            throw new \RuntimeException('Paid amount ('.$paidAmount.') cannot be greater than payable amount ('.$recipe->getPayableAmount().')');
        }
        $recipe->setPaidAmount($paidAmount);
    }
}
