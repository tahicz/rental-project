<?php

namespace App\EntityListener;

use App\Entity\AdditionalFee;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: AdditionalFee::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: AdditionalFee::class)]
class AdditionalFeeUpdater
{
    private AdditionalFee $newFee;

    public function preUpdate(AdditionalFee $originFee, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('child')) {
            return;
        }
        $this->newFee = new AdditionalFee();
        $this->newFee->setBillable($originFee->isBillable())
            ->setDescription($originFee->getDescription())
            ->setFeeAmount($originFee->getFeeAmount())
            ->setPaymentFrequency($originFee->getPaymentFrequency())
            ->setRentRecipe($originFee->getRentRecipe())
            ->setValidityFrom(new \DateTimeImmutable());

        foreach ($event->getEntityChangeSet() as $field => $values) {
            $event->setNewValue($field, $event->getOldValue($field));
        }
    }

    public function postUpdate(AdditionalFee $originFee, PostUpdateEventArgs $event): void
    {
        if (!empty($this->newFee)) {
            $event->getObjectManager()->persist($this->newFee);
            $originFee->setChild($this->newFee);
            $event->getObjectManager()->persist($originFee);
            $event->getObjectManager()->flush();
        }
        unset($this->newFee);
    }
}
