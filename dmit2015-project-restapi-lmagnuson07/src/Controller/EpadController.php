<?php

namespace App\Controller;

use App\Entity\Epad;
use App\Repository\EpadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restapi/epad', name: 'app_epad.', methods: ['GET'])]
class EpadController extends AbstractController
{
    #[Route('/fetch/{id}', name: 'showOne', methods: ['GET'])]
    public function show(string $id, EpadRepository $er): JsonResponse
    {
        return $this->json(
            $er->find($id),
        );
    }

    #[Route('/findByAddress', name: 'findByAddress', methods: ['GET'])]
    public function findByAddress(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $houseNumber = $request->query->get('houseNumber');
        $streetName = $request->query->get('streetName');
        $suite = $request->query->get('suite');

        $qb = $em->createQueryBuilder();

        try {
            $result = $qb->select(array('epad'))
                ->from(Epad::class, 'epad')
                ->where('epad.houseNumber = :houseNumber')
                ->andWhere('epad.streetName = :streetName')
                ->andWhere('epad.suite = :suite')
                ->setParameter('houseNumber', $houseNumber)
                ->setParameter('streetName', $streetName)
                ->setParameter('suite', $suite)
                ->getQuery()
                ->getOneOrNullResult();

        } catch (NonUniqueResultException $e) {
            $result = null;
        }

        return $this->json(
            $result
        );
    }

    #[Route('/findByNeighbourhoodValue', name: 'findByNeighbourHoodValue', methods: ['GET'])]
    public function findByNeighbourhoodValue(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $neighbourHood = $request->query->get('neighbourhood');
        $minValue = $request->query->get('minValue');
        $maxValue = $request->query->get('maxValue');

        $qb = $em->createQueryBuilder();

        try {
            $query = $qb->select(array('epad'))
                ->from(Epad::class, 'epad')
                ->where('epad.neighbourhood = :neighbourhood')
                ->andWhere('epad.assessedValue >= :minValue')
                ->andWhere('epad.assessedValue <= :maxValue')
                ->setParameter('neighbourhood', $neighbourHood)
                ->setParameter('minValue', $minValue)
                ->setParameter('maxValue', $maxValue)
                ->setMaxResults(100)
                ->getQuery()
            ;

            $results = $query->getResult();

        } catch (Exception $e) {
            $results = null;
        }

        return $this->json(
            $results
        );
    }

    #[Route('/testJwt', name: 'testJwt', methods: ['GET'])]
    public function testJwt(): JsonResponse
    {
        return $this->json([
            'message' => 'Token is valid',
            'code' => 200
        ]);
    }

}
