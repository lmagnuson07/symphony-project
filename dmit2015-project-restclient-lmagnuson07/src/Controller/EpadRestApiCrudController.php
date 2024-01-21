<?php

namespace App\Controller;

use App\Entity\EdmontonPropertyAssessmentData;
use App\Form\EpadCreateType;
use App\Form\EpadEditType;
use App\Helper\AppHelper;
use App\Repository\EpadRestApiCrudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/admin/epad', name: 'app_admin_epad.')]
class EpadRestApiCrudController extends AbstractController
{
    private EpadRestApiCrudRepository $ec;

    public function __construct(EpadRestApiCrudRepository $ec)
    {
        $this->ec = $ec;
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(Request $request, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");
        $max = 10;

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        $pageIndex = $request->query->get('start', 0);

        $start = $pageIndex * $max;
        $epad = $this->ec->findAll($jwt, $start, $max);
        $count = $epad['count'];

        $last = floor($count / $max);
        if ($epad['statusCode'] === 401) {
            // Unauthorized
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        } elseif ($epad['statusCode'] !== 200) {
            $this->addFlash('error', 'There was an error with the database connection');
        }

        return $this->render('epad_restapi_crud/index.html.twig', [
            'epadList' => $epad['data'],
            'pageIndex' => $pageIndex,
            'max' => $max,
            'lastIndex' => $last
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, HttpClientInterface $client): Response
    {
        $epad = new EdmontonPropertyAssessmentData();
        $form = $this->createForm(EpadCreateType::class, $epad);
        $form->handleRequest($request);

        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        $errors = $form->getErrors();
        if ($form->isSubmitted() && $form->isValid()) {
            $epad = $this->ec->create($jwt, $epad);

            if ($epad['statusCode'] === 401) {
                // Unauthorized
                $request->getSession()->remove('jwtToken');
                return $this->redirectToRoute('app_logout');
            } elseif ($epad['statusCode'] === 200) {
                $this->addFlash('success', 'Edmonton Property Assessment data inserted successfully');
            } else {
                $this->addFlash('error', 'There was an error inserting the Edmonton Property Assessment data.');
            }
            return $this->redirectToRoute('app_admin_epad.list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('epad_restapi_crud/new.html.twig', [
            'epad' => [],
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, Request $request, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        $epad = $this->ec->show($jwt, $id);

        if ($epad['statusCode'] === 401) {
            // Unauthorized
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        } elseif ($epad['statusCode'] !== 200) {
            $this->addFlash('error', "The Edmonton Property Assessment data with an id of: $id couldn't be found");
            return $this->redirectToRoute('app_admin_epad.list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('epad_restapi_crud/show.html.twig', [
            'epad' => $epad['data'] ?? null,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(string $id, Request $request, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        $data = $this->ec->show($jwt, $id);
        $epad = $data['data'];

        if ($data['statusCode'] === 401) {
            // Unauthorized
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        } elseif ($data['statusCode'] === 200) {
            $form = $this->createForm(EpadEditType::class, $epad);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $epad = $this->ec->edit($jwt, $id, $epad);
                if ($epad['statusCode'] === 200) {
                    $this->addFlash('success', 'Edmonton Property Assessment data updated successfully');
                } else {
                    $this->addFlash('error', "Edit failed. Try again");
                }
                return $this->redirectToRoute('app_admin_epad.list', [], Response::HTTP_SEE_OTHER);
            }
        } else {
            $this->addFlash('error', "Edit failed. The Edmonton Property Assessment data with an id of: $id couldn't be found.");
            return $this->redirectToRoute('app_admin_epad.list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('epad_restapi_crud/edit.html.twig', [
            'epad' => $epad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(string $id, Request $request, HttpClientInterface $client): Response
    {
        $user = $this->getUser();
        $jwt = $request->getSession()->get("jwtToken");

        if (!AppHelper::validateJwt($jwt, $user, $client)) {
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        }

        $epadData = $this->ec->show($jwt, $id);

        if ($epadData['statusCode'] === 401) {
            // Unauthorized
            $request->getSession()->remove('jwtToken');
            return $this->redirectToRoute('app_logout');
        } elseif ($epadData['statusCode'] === 200) {
            $epad = $epadData['data'];

            if ($this->isCsrfTokenValid('delete'.$epad->getAccountNumber(), $request->request->get('_token'))) {
                $deleteData = $this->ec->delete($jwt, $id);
                $message = $deleteData['data']['msg'];
                $status = $deleteData['data']['success'];

                if ($deleteData['statusCode'] === 200) {
                    if ($status) {
                        $this->addFlash('success', 'Edmonton Property Assessment data deleted successfully');
                    } else {
                        $this->addFlash('error', "Delete failed. Try again");
                    }
                } else {
                    $this->addFlash('error', "Delete failed. Try again");
                }
            }
        } else {
            $this->addFlash('error', "Edit failed. The Edmonton Property Assessment data with an id of: $id couldn't be found.");
            return $this->redirectToRoute('app_admin_epad.list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_admin_epad.list', [], Response::HTTP_SEE_OTHER);
    }
}
