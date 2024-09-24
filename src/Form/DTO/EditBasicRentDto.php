<?php

namespace App\Form\DTO;

use App\Entity\RentalRecipe;
use Symfony\Component\Uid\Ulid;

class EditBasicRentDto
{
    private ?float $percentage = null;
    private float $amount;
    private \DateTime $validityFrom;
    private string $note;

    private Ulid $parentId;

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function setPercentage(float $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function setValidityFrom(\DateTime $validityFrom): static
    {
        $this->validityFrom = $validityFrom;

        return $this;
    }

    public function setParentId(Ulid|string $parentId): static
    {
        if (is_string($parentId)) {
            $parentId = new Ulid($parentId);
        }
        $this->parentId = $parentId;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function getValidityFrom(): \DateTime
    {
        return $this->validityFrom;
    }

    public function getParentId(): Ulid
    {
        return $this->parentId;
    }

    public static function defaultData(RentalRecipe $rentalRecipe): self
    {
        $rentalRecipeId = $rentalRecipe->getId();

        if (null === $rentalRecipeId) {
            throw new \RuntimeException();
        }

        $dto = new self();
        $dto->setPercentage(0.0)
            ->setAmount($rentalRecipe->getBasicRent())
            ->setNote('')
            ->setValidityFrom(new \DateTime())
            ->setParentId($rentalRecipeId)
        ;

        return $dto;
    }
}
