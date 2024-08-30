<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\BankAccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: BankAccountRepository::class)]
#[ORM\Table(name: 'bank_account')]
#[ORM\HasLifecycleCallbacks]
class BankAccount
{
    use Timestampable;
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(nullable: false)]
    private int $prefix;

    #[ORM\Column(nullable: false)]
    private int $accountNumber;

    #[ORM\Column(nullable: false)]
    private int $bankCode;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getPrefix(): ?int
    {
        return $this->prefix;
    }

    public function setPrefix(?int $prefix): static
    {
        if (null === $prefix) {
            $prefix = 0;
        }
        $this->prefix = $prefix;

        return $this;
    }

    public function getAccountNumber(): int
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(int $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getBankCode(): int
    {
        return $this->bankCode;
    }

    public function getFormatedBankCode(): string
    {
        return sprintf('%04d', $this->getBankCode());
    }

    public function setBankCode(int $bankCode): static
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function __toString(): string
    {
        $account = '';
        if (!empty($this->getPrefix())) {
            $account .= $this->getPrefix().'-';
        }
        $account .= $this->getAccountNumber();
        $account .= '/'.$this->getFormatedBankCode();

        return $account;
    }
}
