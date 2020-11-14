<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentService{

    public function addCommentToArticle(Article $article, Comment $comment, EntityManagerInterface $manager ){

        $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article)
                    ->setAuthor($article->getUser()->getUsername());
                    
            $manager->persist($comment);
            $manager->flush();

    }

    public function removeCommentFromArticle($id,EntityManagerInterface $manager,CommentRepository $repository){
        
        $comment = $repository->find($id);

        $manager->remove($comment);
        $manager->flush();

        return $comment;

    }



}