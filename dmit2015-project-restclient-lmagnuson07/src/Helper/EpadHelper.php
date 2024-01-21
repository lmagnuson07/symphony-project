<?php

namespace App\Helper;

use App\Entity\EdmontonPropertyAssessmentData;

class EpadHelper
{
    public static function mapToDto(array $data): EdmontonPropertyAssessmentData
    {
        $epad = new EdmontonPropertyAssessmentData();

        $epad
            ->setAccountNumber($data['accountNumber'])
            ->setHouseNumber($data['houseNumber'] ?? null)
            ->setStreetName($data['streetName'] ?? null)
            ->setSuite($data['suite'] ?? null)
            ->setAssessedValue($data['assessedValue'] ?? null)
            ->setLongitude($data['longitude'] ?? null)
            ->setLatitude($data['latitude'] ?? null)
            ->setNeighbourhood($data['neighbourhood'] ?? null)
            ->setNeighbourhoodId($data['neighbourhoodId'] ?? null)
            ->setGarage($data['garage'] ?? null)
            ->setWard($data['ward'] ?? null)
            ->setAssessmentClass1($data['assessmentClass1']?? null) ;

        return $epad;
    }
}