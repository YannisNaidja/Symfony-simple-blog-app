<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;

use App\Service\UserService;
use App\Service\ArticleService;
use App\Service\CommentService;

use App\Repository\UserRepository;

use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
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
    public function index(ArticleRepository $repo,ArticleService $articleService): Response
    {
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articleService->getAllArticles($repo);
        foreach($articles as $article){
            $article->setContent(substr($article->getContent(),0,100)."....");
        }
        
        //$articles = $repo->findAll();

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
            'title' => 'Bienvenue'   
        ]);
    }


    /**
     * @Route("/blog/new/{id}",name="blog_create")
     * 
     */
    public function form($id,Article $article = null , Request $request, EntityManagerInterface $manager,
                            ArticleService $articleService, UserRepository $repo){

        if(!$article){
            $article = new Article();
        }
        
            $form = $this->createForm(ArticleType::class,$article);             
            
            $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()){

                $articleService->addArticleToUser($id,$repo,$article,$manager);
            
                return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);

            }
           
        
        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId()!==null
        ]);
    }
    /**
     * 
     * @Route("/blog/edit/{id}", name="blog_edit")
     */

     public function edit($id,Article $article = null,Request $request, EntityManagerInterface $manager,
                            ArticleService $articleService, ArticleRepository $repo){

        $form = $this->createForm(ArticleType::class,$article);             
            
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $articleService->updateArticle($id,$repo,$article,$manager);
        
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);

        }

        return $this->render('blog/update.html.twig',[
            'formArticle' => $form->createView()   
        ]);
     }
    

     /**
     * @Route("/blog/{id}",name="blog_show")
     */
    //param converter se charge de trouver le bon article
    public function show(CommentService $commentService, Article $article,Request $request, EntityManagerInterface $manager){

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            
            $commentService->addCommentToArticle($article,$comment,$manager);
            
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
    public function deleteComment($id,CommentService $commentService,CommentRepository $repository,EntityManagerInterface $manager){

        $comment = $commentService->removeCommentFromArticle($id,$manager,$repository);

        return $this->redirectToRoute('blog_show', ['id' => $comment->getArticle()->getId()]);
    }


    /**
     * @Route("/blog/articles/{id}",name="user_articles")
     */
    public function articleOfUser($id,UserService $userService, UserRepository $repository){

      
        $articles = $userService->getArticlesOfUser($id,$repository);

        return $this->render('blog/userArticles.html.twig', [
            'articles' => $articles,
            'userId' => $id
        ]);

    }

    /**
     * 
     * @Route("blog/article/delete/{id}", name="blog_deleteArticle")
     * 
     */
    public function deleteArticle($id,EntityManagerInterface $manager,ArticleService $articleService,ArticleRepository $repository
    ,UserService $userService,UserRepository $userRepository){

        $user_id = $articleService->deleteArticle($id,$repository,$manager);

        $articles = $userService->getArticlesOfUser($user_id,$userRepository);

        return $this->render('blog/userArticles.html.twig', [
            'articles' => $articles
        
        ]);


    }
    
}
