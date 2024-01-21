<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Helper\AppHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegisterController extends AbstractController
{
    private string $restApiUrl = "http://localhost:8080";
    #[Route('/register', name: 'app_register')]
    public function reg(Request $request, HttpClientInterface $client, SessionInterface $session): Response
    {
        $regForm = $this->createForm(RegisterType::class);
        $regForm->handleRequest($request);

        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        $data = $regForm->getData();

        if ($regForm->isSubmitted() && $regForm->isValid()) {

            try {
                // Register the user (creates a database record)
                $response = $client->request(
                    'POST',
                    $this->restApiUrl . '/restapi/register', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'body' => json_encode([
                            'email' => $data['email'],
                            'username' => $data['username'],
                            'password' => $data['password']
                        ])
                    ]
                );
                $statusCode = $response->getStatusCode();

                if ($statusCode === 200) {
                    $data = $response->getContent();
                    $locationHeader = $response->getHeaders()['location'][0] ?? null;

                    // Set a flash message
                }


                // Login the newly registered user and set their token

            } catch(
                RedirectionExceptionInterface | DecodingExceptionInterface
                | ClientExceptionInterface | TransportExceptionInterface
                | ServerExceptionInterface $ex
            ) {

            }

            return $this->redirect($this->generateUrl('app_home'));
        }

        return $this->render('register/index.html.twig', [
            'regForm' => $regForm->createView(),
        ]);
    }
}
