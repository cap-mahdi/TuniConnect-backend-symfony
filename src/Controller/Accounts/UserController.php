<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\User;
use App\Repository\Accounts\UserRepository;
use App\Repository\Posts\PersonRepository;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
    }

   

#[Route("/user/delete/{id}", name: "app_user_delete",methods: ["DELETE"])]
public function delete(Request $request,UserRepository $userRepository):Response
{
    try {
        $idUser=$request->attributes->get("_route_params")["id"];
        $user=$userRepository->find(intval($idUser));
        $userRepository->remove($user,true);
        return $this->json("deleted",200, ["Content-Type" => "application/json"]);
    }
    catch (\Exception $exception){
        return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
    }

    }

#[Route("/user/find", name: "app_user_find",methods: ["GET"])]
public function getUsers(Request $request,UserRepository $userRepository):Response
{
    try {
      if($request->query->has("id")){
          $id=$request->query->get("id");
          $user=$userRepository->find(intval($id));
          return $this->json($user,200, ["Content-Type" => "application/json"]);
      }
      else{
          $users=$userRepository->findAll();
          return $this->json($users,200, ["Content-Type" => "application/json"]);
      }
    }
    catch (\Exception $exception){
        return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
    }

    }

}



