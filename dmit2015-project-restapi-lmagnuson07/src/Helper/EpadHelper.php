<?php

namespace App\Helper;

use App\Entity\Epad;

class EpadHelper
{
    public static function mapToDto(array $data): Epad
    {
        $epad = new Epad();

        $epad
            ->setAccountNumber($data['accountNumber'])
            ->setHouseNumber($data['houseNumber'] ?? null)
            ->setStreetName($data['streetName'] ?? null)
            ->setSuite($data['suite'] ?? null)
            ->setAssessedValue((int)$data['assessedValue'] ?? null)
            ->setLongitude((float)$data['longitude'] ?? null)
            ->setLatitude((float)$data['latitude'] ?? null)
            ->setNeighbourhood($data['neighbourhood'] ?? null)
            ->setNeighbourhoodId((int)$data['neighbourhoodId'] ?? null)
            ->setGarage((bool)$data['garage'] ?? null)
            ->setWard($data['ward'] ?? null)
            ->setAssessmentClass1($data['assessmentClass1']?? null) ;

        return $epad;
    }
}