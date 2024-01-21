<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/reg', name: 'app_reg')]
    public function reg(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // Better to create a new form class, but an alternative:
        $regForm = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => 'username'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Password Confirm']
            ])
            ->add('register', SubmitType::class)
            ->getForm()
        ;

        $regForm->handleRequest($request);

        if ($regForm->isSubmitted() && $regForm->isValid()) {
            // Need to do this since the fields arn't mapped to a class.
            $input = $regForm->getData();

            $user = new User();
            $user->setUsername($input['username']);

            $hasedPassword = $passwordHasher->hashPassword(
                $user,
                $input['password']
            );

            $user->setPassword($hasedPassword);

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_home'));
        }

        return $this->render('register/index.html.twig', [
            'regForm' => $regForm->createView(),
        ]);
    }
}
