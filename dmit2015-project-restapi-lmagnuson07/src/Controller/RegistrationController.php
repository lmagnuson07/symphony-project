<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restapi', name: 'app.')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function index(EntityManagerInterface $em, Request $req, UserPasswordHasherInterface $ph): JsonResponse
    {
        $decoded = json_decode($req->getContent());

        if ($decoded !== null) {
            $email = $decoded->email;
            $userName = $decoded->username;
            $plainTextPass = $decoded->password;

            $user = new User();
            $hasedPass = $ph->hashPassword(
                $user,
                $plainTextPass
            );
            $user->setPassword($hasedPass);
            $user->setEmail($email);
            $user->setUsername($userName);
            $user->getRoles();

            $em->persist($user);
            $em->flush();
        }

        return $this->json([
            'message' => 'Registered Successfully'
        ]);

    }
}
