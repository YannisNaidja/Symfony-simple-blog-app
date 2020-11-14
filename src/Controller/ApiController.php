<?php

namespace App\Controller;

use App\Service\ArticleService;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/articles", name="apiArticles", methods={"GET"} )
     */
    public function index(ArticleRepository $repo,ArticleService $articleService,EntityManagerInterface $em): JsonResponse
    {

        $articles = $articleService->getAllArticles($repo);
        $serializedArticles = [];

        
        foreach($articles as $article){
            $commentsArray = [];
            foreach($article->getComments() as $comment){
                $commentsArray[] = [
                    'id' => $comment->getId(),
                    'author' => $comment->getAuthor(),
                    'content' => $comment->getContent()
                ];
            }

            $serializedArticles[] = [
                'id' => $article->getId(),
                'title'=> $article->getTitle(),
                'category' => $article->getCategory()->getTitle(),
                'content'=> $article->getContent(),
                'commentaires' => $commentsArray
                   
            ];
        }
        
        return new JsonResponse(['data'=> $serializedArticles, 'count' => count($serializedArticles)]);
    }
}
