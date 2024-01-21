<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/dish', name: 'app_dish.')]
class DishController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function index(DishRepository $dishRepository): Response
    {
        $dishes = $dishRepository->findAll();

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $dish = new Dish();

        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request); // This will magically set the $dish variable. The data coming from the form is Dish type already.

        if ($form->isSubmitted() && $form->isValid()) {
            // Entity manager
            $image = $request->files->get('dish')['attachment'];

            if ($image) {
                $dateFileName = md5(uniqid()) . '.' . $image->guessClientExtension();

                $image->move(
                    $this->getParameter('images_folder'),
                    $dateFileName
                );

                $dish->setImage($dateFileName);
            }

            $em->persist($dish);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('app_dish.list')
            );
        }

        return $this->render('dish/create.html.twig', [
            'dishForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'details')]
    public function details(EntityManagerInterface $em, Dish $dish): Response
    {
        return $this->render('dish/details.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(EntityManagerInterface $em, Request $request, $id): Response
    {
        $dish = $em->getRepository(Dish::class)->find($id);

        if (!$dish) {
            throw $this->createNotFoundException('Dish not found');
        }

        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_dish.list');
        }

        return $this->render('dish/create.html.twig', [
            'dishForm' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, EntityManagerInterface $em): Response
    {
        $dish = $em->getRepository(Dish::class)->find($id);

        $em->remove($dish);
        $em->flush();

        $this->addFlash('success', 'Dish was removed successfully');

        return $this->redirect(
            $this->generateUrl('app_dish.list')
        );
    }
}
