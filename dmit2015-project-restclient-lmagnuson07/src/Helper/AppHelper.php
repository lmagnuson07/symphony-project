<?php

namespace App\Helper;

use DateTime;
use DateTimeZone;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppHelper
{
    public static function validateJwt(string $jwt, UserInterface|null $user, HttpClientInterface $client): bool
    {
        $tokenParts = explode(".", $jwt);
        $jwtHeader = json_decode(base64_decode($tokenParts[0]));
        $jwtPayload = json_decode(base64_decode($tokenParts[1]));

        $userUsername = $user->getUsername() ?? null;
        $payloadUsername = $jwtPayload->username ?? null;

        try {
            $response = $client->request(
                'GET',
                'https://127.0.0.1:8080/restapi/epad/testJwt', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization: Bearer ' . $jwt
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray();

            if ($statusCode === 200 && $content['code'] === 200) {
                return true;
            } else {
                return false;
            }

        } catch(
            RedirectionExceptionInterface | DecodingExceptionInterface
            | ClientExceptionInterface | TransportExceptionInterface
            | ServerExceptionInterface $ex
        ) {

        }
        if (($userUsername !== null && $payloadUsername !== null) && $userUsername === $payloadUsername) {
            return true;
        } else {
            return false;
        }

    }
}