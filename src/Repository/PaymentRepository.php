<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function getPaymentsDueSum(): float
    {
        return (float) $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.maturityDate <= :now')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPaymentsDueCount(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.maturityDate <= :now')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPaymentsActuallyMadeSum(): float
    {
        return (float) $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.maturityDate <= :now')
            ->andWhere('p.paymentDate is not null')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPaymentsActuallyMadeCount(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.maturityDate <= :now')
            ->andWhere('p.paymentDate is not null')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
