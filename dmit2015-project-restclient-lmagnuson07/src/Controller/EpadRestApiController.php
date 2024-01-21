<?php

namespace App\Controller;

use App\Entity\EdmontonPropertyAssessmentData;
use App\Form\FindByAddressType;
use App\Form\FindByNeighbourHoodValueType;
use App\Helper\AppHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/epad', name: 'app_epad.')]
class EpadRestApiController extends AbstractController
{
    private string $restApiUrl = "https://127.0.0.1:8080";

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('epad_restapi/index.html.twig', [
            'controller_name' => 'EpadRestApiController',
        ]);
    }

    #[Route('/findByAddress', name: 'findByAddress')]
    public function findByAddress(HttpClientInterface $client, Request $request): Response
    {
        $epad = new EdmontonPropertyAssessmentData();

        $form = $this->createForm(FindByAddressType::class, $epad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $client->request(
                    'GET',
                    $this->restApiUrl . '/restapi/epad/findByAddress/?houseNumber='
                        . $epad->getHouseNumber() .'&streetName=' . $epad->getStreetName() .'&suite=' . $epad->getSuite(), [
                    'headers' => [
                        'Accept' => 'application/json',
                    ]
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode === 200) {
                    $this->addFlash('success', 'Record Found');
                }
                $content = $response->toArray();

                $epad = $this->mapToDto($content);

            } catch(
                RedirectionExceptionInterface | DecodingExceptionInterface
                | ClientExceptionInterface | TransportExceptionInterface
                | ServerExceptionInterface $ex
            ) {

            }

        }

        return $this->render('epad_restapi/findByAddress.html.twig', [
            'edmontonProperties' => $epad,
            'queryForm' => $form->createView()
        ]);
    }
    #[Route('/batch', name: 'batchJob',  methods: ['GET', 'POST'])]
    public function batchJob(HttpClientInterface $client, Request $request): Response|JsonResponse
    {
        $data['crsfToken'] = null;
        $complete = false;
        if ($request->isMethod('POST')) {
            $data = $request->toArray();
        }

        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        if ($this->isCsrfTokenValid('batchImportToken', $data['crsfToken'])) {
            $importNumber = $request->getSession()->get('importNumber', 0);
            $response = $client->request(
                'POST',
                $this->restApiUrl . '/restapi/admin/epad/import', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization: Bearer ' . $jwt
                    ],
                    'json' => [
                        "importNumber" => $importNumber,
                        "batchSize" => 5
                    ]
                ],
            );

            $data = $response->toArray();
            $statusCode = $response->getStatusCode();

            if ($statusCode === 401) {
                // Unauthorized
                $request->getSession()->remove('jwtToken');
                return $this->json([
                    "complete" => false,
                    "ajaxMsg" => "Unauthorized.",
                    "success" => false
                ]);
            } elseif ($statusCode === 200) {
                if ($data['data']['success']) {
                    if ($data['data']['complete']) {
                        $request->getSession()->remove('importNumber');
                        $complete = true;
                    } else {
                        $request->getSession()->set('importNumber',  $request->getSession()->get('importNumber', 0) + 1);
                    }
                } else {
                    return $this->json([
                        "complete" => false,
                        "ajaxMsg" => "Server error.",
                        "success" => true
                    ]);
                }

            } else {
                return $this->json([
                    "complete" => false,
                    "ajaxMsg" => "Error.",
                    "success" => false
                ]);
            }
            return $this->json([
                "complete" => $complete,
                "success" => $data['data']['success'],
                "ajaxMsg" => $data['msg']
            ]);
        }

        return $this->render('epad_restapi/batch.html.twig', [
        ]);
    }

    #[Route('/findByNeighbourhoodValue', name: 'findByNeighbourhoodValue')]
    public function findByNeighbourhoodValue(HttpClientInterface $client, Request $request): Response
    {
        $epads = [];
        $form = $this->createForm(FindByNeighbourHoodValueType::class);
        $form->handleRequest($request);
        $data = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $client->request(
                    'GET',
                    $this->restApiUrl . '/restapi/epad/findByNeighbourhoodValue?neighbourhood='
                        . $data['neighbourhood'] . '&minValue=' . $data['assessedMinValue'] . '&maxValue=' . $data['assessedMaxValue'], [
                    'headers' => [
                        'Accept' => 'application/json',
                    ]
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode === 200) {
                    $this->addFlash('success', 'Record Found');
                }
                $content = $response->toArray();

                $epads = array_map(function ($item) {
                    return $this->mapToDto($item);
                }, $content);

            } catch(
                RedirectionExceptionInterface | DecodingExceptionInterface
                | ClientExceptionInterface | TransportExceptionInterface
                | ServerExceptionInterface $ex
            ) {

            }
        }

        return $this->render('epad_restapi/findByNeighbourhoodValue.html.twig', [
            'edmontonProperties' => $epads,
            'queryForm' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, HttpClientInterface $client): Response
    {
        try {
            $response = $client->request(
                'GET',
                $this->restApiUrl . '/restapi/epad/fetch/' . $id, [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $this->addFlash('success', 'Record Found');
            }
            $content = $response->toArray();

            $epad = $this->mapToDto($content);

        } catch(
            RedirectionExceptionInterface | DecodingExceptionInterface
            | ClientExceptionInterface | TransportExceptionInterface
            | ServerExceptionInterface $ex
        ) {

        }

        return $this->render('epad_restapi/show.html.twig', [
            'epad' => $epad,
        ]);
    }

    private function mapToDto(array $data): EdmontonPropertyAssessmentData
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
