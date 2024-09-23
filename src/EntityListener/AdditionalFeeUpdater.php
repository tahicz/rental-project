<?php

namespace App\EntityListener;

use App\Entity\AdditionalFee;
use App\Exception\NoValiditySetException;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: AdditionalFee::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: AdditionalFee::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: AdditionalFee::class)]
class AdditionalFeeUpdater
{
    private ?AdditionalFee $fee = null;

    public function preUpdate(AdditionalFee $originFee, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('parent') || $event->hasChangedField('child')) {
            return;
        } else {
            $this->fee = new AdditionalFee();
            $this->fee
                ->setBillable($originFee->isBillable())
                ->setDescription($originFee->getDescription())
                ->setFeeAmount($originFee->getFeeAmount())
                ->setPaymentFrequency($originFee->getPaymentFrequency())
                ->setRentRecipe($originFee->getRentRecipe())
                ->setValidityFrom(new \DateTimeImmutable())
                ->setParent($originFee)
                ->setChild(null);

            $originFee->setValidityTo($this->fee->getValidityFrom());
            foreach ($event->getEntityChangeSet() as $field => $values) {
                $event->setNewValue($field, $event->getOldValue($field));
            }
        }
    }

    public function postUpdate(AdditionalFee $additionalFee, PostUpdateEventArgs $event): void
    {
        $this->saveFee($event->getObjectManager());
    }

    public function postPersist(AdditionalFee $additionalFee, PostPersistEventArgs $event): void
    {
        $recipe = $additionalFee->getRentRecipe();
        if (null === $recipe) {
            throw new \RuntimeException('Rental recipe missing');
        }

        $feeId = $additionalFee->getId();
        if (null === $feeId) {
            throw new \RuntimeException('Fee id missing');
        }

        $parent = $event->getObjectManager()
            ->getRepository(AdditionalFee::class)
            ->getLastFee($additionalFee->getDescription(), $recipe, $feeId);

        if ($parent instanceof AdditionalFee) {
            $additionalFee->setParent($parent);
            $parent->setChild($additionalFee);

            $feeValidity = $additionalFee->getValidityFrom();
            if (!$feeValidity instanceof \DateTimeImmutable) {
                throw new NoValiditySetException($additionalFee->getId(), $additionalFee::class);
            }
            $parent->setValidityTo($feeValidity->modify('-1 day'));
            $event->getObjectManager()->persist($additionalFee);
            $event->getObjectManager()->persist($parent);

            $event->getObjectManager()->flush();
        }
    }

    private function saveFee(EntityManagerInterface $entityManager): void
    {
        if (null !== $this->fee) {
            $entityManager->persist($this->fee);

            $this->fee->getParent()?->setChild($this->fee);

            $entityManager->flush();
        }
        $this->fee = null;
    }
}
