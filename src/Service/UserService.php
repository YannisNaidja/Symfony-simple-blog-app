<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService{

    public function getArticlesOfUser($id,UserRepository $repository){

        $user = $repository->find($id);

        $articles = $user->getArticles();

        return $articles;
    }

    public function registerUser(User $user,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder){
        
        $hash = $encoder->encodePassword($user, $user->getPassword());

        $user->setPassword($hash);

        $manager->persist($user);
        $manager->flush();
    }

}