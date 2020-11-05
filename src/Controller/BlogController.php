<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CommentType;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/",name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue dans le blog',
            'age' => 12,
        ]);
    }


    /**
     * @Route("/blog/new/{id}",name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form($id,Article $article = null , Request $request, EntityManagerInterface $manager){

        if(!$article){
            $article = new Article();
        }
        
        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find($id);

          /*  $form = $this->createFormBuilder($article)
                        ->add('title')
                        ->add('content')
                        ->add('image')
                        ->getForm();   */



            $form = $this->createForm(ArticleType::class,$article);             
            
            $form->handleRequest($request);

            //dump($article); 

            if($form->isSubmitted() && $form->isValid()){
                if(!$article->getId()){
                    $article->setCreatedAt(new \DateTime());
                    
                }

                $article->setUser($user);

                $manager->persist($article);
                $manager->flush();
                return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);

            }
           
        
        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId()!==null
        ]);
    }

     /**
     * @Route("/blog/{id}",name="blog_show")
     */
    //param converter se charge de trouver le bon article
    public function show(Article $article,Request $request, EntityManagerInterface $manager){

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
                    
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);

        }
        
        return $this->render('blog/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/delete/{id}",name="blog_delete")
     */
    public function deleteComment($id,EntityManagerInterface $manager){

        $repository = $this->getDoctrine()->getRepository(Comment::class);

        $comment = $repository->find($id);

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('blog_show', ['id' => $comment->getArticle()->getId()]);
    }


    /**
     * @Route("/blog/articles/{id}",name="user_articles")
     */
    public function articleOfUser($id){

        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find($id);

        $articles = $user->getArticles();

        return $this->render('blog/userArticles.html.twig', [
            'articles' => $articles
        ]);

    }

    
}
