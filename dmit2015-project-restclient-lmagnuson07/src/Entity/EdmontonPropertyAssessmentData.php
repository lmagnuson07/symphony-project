<?php

namespace App\Entity;

use App\Repository\EdmontonPropertyAssessmentDataRepository;
use Doctrine\ORM\Mapping as ORM;

class EdmontonPropertyAssessmentData
{

    private ?string $accountNumber = null;

    private ?string $suite = null;

    private ?string $houseNumber = null;

    private ?string $streetName = null;

    private ?bool $garage = null;

    private ?int $neighbourhoodId = null;

    private ?string $neighbourhood = null;

    private ?string $ward = null;

    private ?int $assessedValue = null;

    private ?float $latitude = null;

    private ?float $longitude = null;

    private ?string $assessmentClass1 = null;

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
