<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Article;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService{

    public function getAllArticles(ArticleRepository $repo){

        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        return $articles;

    }

    public function addArticleToUser($id,UserRepository $repository,Article $article,EntityManagerInterface $manager){


        //$repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find($id);

        if(!$article->getId()){
            $article->setCreatedAt(new \DateTime());
            
        }

        $article->setUser($user);

        $manager->persist($article);
        $manager->flush();

    }

    public function getArticleOfUser($id){

        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find($id);

        $articles = $user->getArticles();

        return $articles;

    }


}



