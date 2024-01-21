<?php

namespace App\Entity;

use App\Repository\EpadRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: EpadRepository::class)]
class Epad implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $accountNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $suite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $houseNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $streetName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $garage = null;

    #[ORM\Column(nullable: true)]
    private ?int $neighbourhoodId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $neighbourhood = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ward = null;

    #[ORM\Column(nullable: true)]
    private ?int $assessedValue = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $assessmentClass1 = null;

    public function jsonSerialize():array
    {
        return array(
            'accountNumber' => $this->accountNumber,
            'houseNumber' => $this->houseNumber,
            'streetName' => $this->streetName,
            'suite' => $this->suite,
            'assessedValue' => $this->assessedValue,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'neighbourhood' => $this->neighbourhood,
            'neighbourhoodId' => $this->neighbourhoodId,
            'garage' => $this->garage,
            'ward' => $this->ward,
            'assessmentClass1' => $this->assessmentClass1,
        );
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getSuite(): ?string
    {
        return $this->suite;
    }

    public function setSuite(?string $suite): static
    {
        $this->suite = $suite;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): static
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(?string $streetName): static
    {
        $this->streetName = $streetName;

        return $this;
    }

    public function isGarage(): ?bool
    {
        return $this->garage;
    }

    public function setGarage(?bool $garage): static
    {
        $this->garage = $garage;

        return $this;
    }

    public function getNeighbourhoodId(): ?int
    {
        return $this->neighbourhoodId;
    }

    public function setNeighbourhoodId(?int $neighbourhoodId): static
    {
        $this->neighbourhoodId = $neighbourhoodId;

        return $this;
    }

    public function getNeighbourhood(): ?string
    {
        return $this->neighbourhood;
    }

    public function setNeighbourhood(?string $neighbourhood): static
    {
        $this->neighbourhood = $neighbourhood;

        return $this;
    }

    public function getWard(): ?string
    {
        return $this->ward;
    }

    public function setWard(?string $ward): static
    {
        $this->ward = $ward;

        return $this;
    }

    public function getAssessedValue(): ?int
    {
        return $this->assessedValue;
    }

    public function setAssessedValue(?int $assessedValue): static
    {
        $this->assessedValue = $assessedValue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAssessmentClass1(): ?string
    {
        return $this->assessmentClass1;
    }

    public function setAssessmentClass1(?string $assessmentClass1): static
    {
        $this->assessmentClass1 = $assessmentClass1;

        return $this;
    }
}
