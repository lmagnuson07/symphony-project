<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(DishRepository $dr): Response
    {
        $dishes = $dr->findAll();

        $random = array_rand($dishes, 2);

        return $this->render('home/index.html.twig', [
            'dish1' => $dishes[$random[0]],
            'dish2' => $dishes[$random[1]]
        ]);
    }
}
