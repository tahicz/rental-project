<?php

namespace App\Repository;

use App\Entity\PaymentRecipe;
use App\Entity\RentalRecipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentRecipe>
 */
class PaymentRecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentRecipe::class);
    }

    public function getPaymentsDueSum(\DateTimeInterface $now): float
    {
        return (float) $this->createQueryBuilder('p')
            ->select('SUM(p.payableAmount)')
            ->where('p.maturityDate <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPaymentsDueCount(\DateTimeInterface $now): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.maturityDate <= :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPaymentsActuallyMadeSum(\DateTimeInterface $now): float
    {
        return (float) $this->createQueryBuilder('p')
            ->select('SUM(p.paidAmount)')
            ->where('p.maturityDate <= :now')
            ->andWhere('p.paymentDate is not null')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPaymentsActuallyMadeCount(\DateTimeInterface $now): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.maturityDate <= :now')
            ->andWhere('p.paymentDate is not null')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNextPaymentDue(\DateTimeInterface $now): ?PaymentRecipe
    {
        $result = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.maturityDate > :now')
            ->setParameter('now', $now)
            ->orderBy('p.maturityDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof PaymentRecipe) {
            return $result;
        } else {
            return null;
        }
    }

    public function save(PaymentRecipe $payment): void
    {
        $this->persist($payment);
        $this->flush();
    }

    public function persist(PaymentRecipe $payment): void
    {
        $this->getEntityManager()->persist($payment);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getPaymentsFrom(RentalRecipe $rentalRecipe, \DateTime $from): mixed
    {
        return $this->createQueryBuilder('p')
                    ->select('p')
                    ->andWhere('p.maturityDate > :now')
                    ->andWhere('p.paidAmount is null')
                    ->andWhere('p.rentalRecipe = :rentalRecipe')
                    ->setParameters(
                        new ArrayCollection([
                            'now' => $from->format('Y-m-d H:i:s'),
                            'rentalRecipe' => $rentalRecipe,
                        ])
                    )
                    ->orderBy('p.id', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}
