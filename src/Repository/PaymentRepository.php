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

    public function getNextPaymentDue(\DateTimeInterface $now): ?Payment
    {
        $result = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.maturityDate > :now')
            ->setParameter('now', $now)
            ->orderBy('p.maturityDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof Payment) {
            return $result;
        } else {
            return null;
        }
    }

    public function save(Payment $payment): void
    {
        $this->persist($payment);
        $this->flush();
    }

    public function persist(Payment $payment): void
    {
        $this->getEntityManager()->persist($payment);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
