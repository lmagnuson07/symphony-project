<?php

namespace App\Repository;

use App\Entity\EdmontonPropertyAssessmentData;
use App\Helper\EpadHelper;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EpadRestApiCrudRepository
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private string $restApiUrl = "https://localhost:8080/restapi/admin/epad";

    public function findAll(string $token, $start, $max): array
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->restApiUrl . '?start=' . $start . '&max=' . $max, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization: Bearer ' . $token
                    ]
                ]
            );

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $data = $response->toArray();
                $count = $data['count'];
                $data = array_map(function ($item) {
                    return EpadHelper::mapToDto($item);
                }, $data['data']);
            }

            return [
                'data' => $data ?? null,
                'count' => $count ?? null,
                'statusCode' => $statusCode
            ];
        } catch (
            RedirectionExceptionInterface|ClientExceptionInterface
            |TransportExceptionInterface|ServerExceptionInterface
            |DecodingExceptionInterface $ex
        ) {

            return [
                'data' => null,
                'statusCode' => 500,
                'exceptionMessage' => $ex->getMessage()
            ];
        }
    }

    public function create(string $token, EdmontonPropertyAssessmentData $epad): array
    {
        try {
            $response = $this->client->request(
                'POST',
                $this->restApiUrl . '/new', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization: Bearer ' . $token
                    ],
                    'json' => [
                        "accountNumber" => $epad->getAccountNumber(),
                        "houseNumber" => $epad->getHouseNumber(),
                        "streetName" => $epad->getStreetName(),
                        "suite" => $epad->getSuite(),
                        "assessedValue" => $epad->getAssessedValue(),
                        "longitude" => $epad->getLongitude(),
                        "latitude" => $epad->getLatitude(),
                        "neighbourhood" => $epad->getNeighbourhood(),
                        "neighbourhoodId" => $epad->getNeighbourhoodId(),
                        "garage" => $epad->isGarage(),
                        "ward" => $epad->getWard(),
                        "assessmentClass1" => $epad->getAssessmentClass1()
                    ]
                ]
            );

            $data = $response->toArray();
            $data = EpadHelper::mapToDto($data);

            $statusCode = $response->getStatusCode();

            return [
                'data' => $data,
                'statusCode' => $statusCode
            ];
        } catch (
            RedirectionExceptionInterface|DecodingExceptionInterface
            |ClientExceptionInterface|TransportExceptionInterface
            |ServerExceptionInterface $ex
        ) {
            return [
                'data' => null,
                'statusCode' => 500,
                'exceptionMessage' => $ex->getMessage()
            ];
        }
    }

    public function show(string $token, string $id): array
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->restApiUrl . '/' . $id, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization: Bearer ' . $token
                    ]
                ]
            );

            $data = $response->toArray();
            $data = EpadHelper::mapToDto($data);

            $statusCode = $response->getStatusCode();

            return [
                'data' => $data,
                'statusCode' => $statusCode
            ];
        } catch (
            RedirectionExceptionInterface|DecodingExceptionInterface
            |ClientExceptionInterface|TransportExceptionInterface
            |ServerExceptionInterface $ex
        ) {
            return [
                'data' => null,
                'statusCode' => 500,
                'exceptionMessage' => $ex->getMessage()
            ];
        }
    }

    public function edit(string $token, string $id, EdmontonPropertyAssessmentData $epad): array
    {
        try {
            $response = $this->client->request(
                'PUT',
                $this->restApiUrl . '/' . $id . '/edit', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization: Bearer ' . $token
                    ],
                    'json' => [
                        "accountNumber" => $epad->getAccountNumber(),
                        "houseNumber" => $epad->getHouseNumber(),
                        "streetName" => $epad->getStreetName(),
                        "suite" => $epad->getSuite(),
                        "assessedValue" => $epad->getAssessedValue(),
                        "longitude" => $epad->getLongitude(),
                        "latitude" => $epad->getLatitude(),
                        "neighbourhood" => $epad->getNeighbourhood(),
                        "neighbourhoodId" => $epad->getNeighbourhoodId(),
                        "garage" => $epad->isGarage(),
                        "ward" => $epad->getWard(),
                        "assessmentClass1" => $epad->getAssessmentClass1()
                    ]
                ]
            );

            $data = $response->toArray();
            $data = EpadHelper::mapToDto($data);

            $statusCode = $response->getStatusCode();

            return [
                'data' => $data,
                'statusCode' => $statusCode
            ];
        } catch (
            RedirectionExceptionInterface|DecodingExceptionInterface
            |ClientExceptionInterface|TransportExceptionInterface
            |ServerExceptionInterface $ex
        ) {
            return [
                'data' => null,
                'statusCode' => 500,
                'exceptionMessage' => $ex->getMessage()
            ];
        }
    }

    public function delete(string $token, string $id): array
    {
        try {
            $response = $this->client->request(
                'DELETE',
                $this->restApiUrl . '/' . $id . '/delete', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization: Bearer ' . $token
                    ]
                ]
            );

            $data = $response->toArray();
            $statusCode = $response->getStatusCode();

            return [
                'data' => $data,
                'statusCode' => $statusCode
            ];
        } catch (
            RedirectionExceptionInterface|DecodingExceptionInterface
            |ClientExceptionInterface|TransportExceptionInterface
            |ServerExceptionInterface $ex
        ) {
            return [
                'data' => null,
                'statusCode' => 500,
                'exceptionMessage' => $ex->getMessage()
            ];
        }
    }

    public function import(string $token): array
    {
        try {
            $response = $this->client->request(
                'POST',
                $this->restApiUrl . '/new', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization: Bearer ' . $token
                    ],
                ]
            );

            $data = $response->toArray();
            $statusCode = $response->getStatusCode();



            return [
                'data' => $data,
                'statusCode' => $statusCode
            ];
        } catch (
            RedirectionExceptionInterface|DecodingExceptionInterface
            |ClientExceptionInterface|TransportExceptionInterface
            |ServerExceptionInterface $ex
        ) {
            return [
                'data' => null,
                'statusCode' => 500,
                'exceptionMessage' => $ex->getMessage()
            ];
        }
    }
}
