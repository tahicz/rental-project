<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\SystemEnum;
use App\Repository\IncomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: IncomeRepository::class)]
#[ORM\Table(name: 'income')]
#[ORM\HasLifecycleCallbacks]
class Income
{
    use Timestampable;
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $incomeDate;

    #[ORM\Column(nullable: false)]
    private float $amount;

    #[ORM\Column(nullable: false)]
    private int $variableSymbol;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private BankAccount $recipientAccount;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private BankAccount $senderAccount;

    /**
     * @var Collection<int, PaymentRecord>
     */
    #[ORM\OneToMany(targetEntity: PaymentRecord::class, mappedBy: 'income')]
    private Collection $paymentRecords;

    public function __construct()
    {
        $this->paymentRecords = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getIncomeDate(): \DateTimeImmutable
    {
        return $this->incomeDate;
    }

    public function setIncomeDate(\DateTimeImmutable $incomeDate): static
    {
        $this->incomeDate = $incomeDate;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVariableSymbol(): ?int
    {
        return $this->variableSymbol;
    }

    public function getFormatedVariableSymbol(): string
    {
        return sprintf('%010d', $this->variableSymbol);
    }

    public function setVariableSymbol(int $variableSymbol): static
    {
        $this->variableSymbol = $variableSymbol;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRecipientAccount(): BankAccount
    {
        return $this->recipientAccount;
    }

    public function setRecipientAccount(BankAccount $recipientAccount): static
    {
        $this->recipientAccount = $recipientAccount;

        return $this;
    }

    public function getSenderAccount(): BankAccount
    {
        return $this->senderAccount;
    }

    public function setSenderAccount(BankAccount $senderAccount): static
    {
        $this->senderAccount = $senderAccount;

        return $this;
    }

    /**
     * @return Collection<int, PaymentRecord>
     */
    public function getPaymentRecords(): Collection
    {
        return $this->paymentRecords;
    }

    public function addPaymentRecord(PaymentRecord $paymentRecord): static
    {
        if (!$this->paymentRecords->contains($paymentRecord)) {
            $this->paymentRecords->add($paymentRecord);
            $paymentRecord->setIncome($this);
        }

        return $this;
    }

    public function removePaymentRecord(PaymentRecord $paymentRecord): static
    {
        if ($this->paymentRecords->removeElement($paymentRecord)) {
            // set the owning side to null (unless already changed)
            if ($paymentRecord->getIncome() === $this) {
                $paymentRecord->setIncome(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        $nf = new \NumberFormatter('cs_CZ', \NumberFormatter::CURRENCY);

        return $nf->formatCurrency($this->getAmount(), SystemEnum::CURRENCY->value).' ('.$this->getIncomeDate()->format('d. m. Y').')';
    }
}
