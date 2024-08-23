<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\RentalRecipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RentalRecipeRepository::class)]
#[ORM\Table(name: 'rental_recipe')]
#[ORM\HasLifecycleCallbacks]
class RentalRecipe
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(nullable: false)]
    #[Assert\Positive()]
    private int $basicRent;

    #[ORM\Column(nullable: false)]
    #[Assert\Range(min: 1, max: 28)]
    private int $maturity;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $validityFrom;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getBasicRent(): ?int
    {
        return $this->basicRent;
    }

    public function setBasicRent(int $basicRent): static
    {
        $this->basicRent = $basicRent;

        return $this;
    }

    public function getMaturity(): ?int
    {
        return $this->maturity;
    }

    public function setMaturity(int $maturity): static
    {
        $this->maturity = $maturity;

        return $this;
    }

    public function getValidityFrom(): ?\DateTimeImmutable
    {
        return $this->validityFrom;
    }

    public function setValidityFrom(\DateTimeImmutable $validityFrom): static
    {
        $this->validityFrom = $validityFrom;

        return $this;
    }
}
