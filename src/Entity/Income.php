<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\IncomeRepository;
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

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getIncomeDate(): ?\DateTimeImmutable
    {
        return $this->incomeDate;
    }

    public function setIncomeDate(\DateTimeImmutable $incomeDate): static
    {
        $this->incomeDate = $incomeDate;

        return $this;
    }

    public function getAmount(): ?float
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
}
