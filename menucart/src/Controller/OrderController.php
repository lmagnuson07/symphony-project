<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findBy(
            ['table_number' => 'table1']
        );

        return $this->render('order/index.html.twig', [
            'orders' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_details')]
    public function order(Dish $dish, EntityManagerInterface $em): Response
    {
        $order = new Order();
        $order->setTableNumber('table1');
        $order->setName($dish->getName());
        $order->setOrderNumber($dish->getId());
        $order->setPrice($dish->getPrice());
        $order->setStatus("open");

        // Entity manager
        $em->persist($order);
        $em->flush();

        $this->addFlash('name', $order->getName() . ' was added to the order');

        return $this->redirect($this->generateUrl('app_menu'));
    }

    #[Route('/status/{id},{status}', name: 'app_order_status')]
    public function status($id, $status, EntityManagerInterface $em): Response
    {
        $order = $em->getRepository(Order::class)->find($id);

        $order->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('app_order'));
    }

    #[Route('/delete/{id}', name: 'app_order_delete')]
    public function delete($id, OrderRepository $or, EntityManagerInterface $em): Response
    {
        $order = $or->find($id);

        $em->remove($order);
        $em->flush();

        return $this->redirect($this->generateUrl('app_order'));
    }

}
