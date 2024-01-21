<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ArticleController extends AbstractController
{

    #[Route('/article', name: 'app_article')]
    public function index(EntityManagerInterface $em): Response
    {
        $article = new Articles();
        $article->setTitle("Title of the article");

        /*
        // tell Doctrine you want to (eventually) save the Entity (no queries yet)
        $em->persist($article);
        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
        */

        $getArticle = $em->getRepository(Articles::class)->findOneBy([
            'id' => 1
        ]);

        $em->remove($getArticle);
        $em->flush();

//        return new Response("Article was created!");

        return $this->render('article/index.html.twig', [
            'article' => $getArticle,
        ]);
    }
}
