<?php

namespace App\Controller\Posts;

use App\Repository\Accounts\MemberRepository;
use App\Repository\Posts\PostRepository;
use MongoDB\Driver\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Posts\PostShare;
use App\Repository\Posts\PostShareRepository;
use Symfony\Component\Validator\Constraints\DateTime;

class PostShareController extends AbstractController
{

    #[Route("/post/share/add",name: "app_post_share_add",methods:["POST"])]
    public function createSharedPos(Request $request,MemberRepository $memberRepository,PostRepository $postRepository,PostShareRepository $postShareRepository):Response
    {
        try {
            $data=json_decode($request->getContent(), true);
            $newPost=new PostShare();
            $newPost->setDate(new \DateTime());
            $member=$memberRepository->find($data["member"]);
            $post=$postRepository->find($data["post"]);
            $newPost->setPost($post);
            $newPost->setMember($member);
            $postShareRepository->save($newPost,true);
            return $this->json("added",200, ["Content-Type" => "application/json"]);
        }
        catch (Exception $exception){
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }
    #[Route('/post/share/find', name: 'app_post_share_find',methods: ['GET'])]
    public function getSharedPOst(Request $request,PostShareRepository $postShareRepository): Response
    {
        try {
        $caracters=[];
        if($request->query->has("id")){
            $post=$postShareRepository->find(intval($request->query->get("id")));
            return $this->json($post,200, ["Content-Type" => "application/json"]);
        }
        if($request->query->has("postId")){

            $caracters["post"]=$request->query->get("postId");
        }
        if($request->query->has("memberId")){
            $caracters["memeber"]=$request->query->get("memberId");
        }
        $postes=$postShareRepository->findBy($caracters);
        return $this->json($postes,200, ["Content-Type" => "application/json"]);
        }
        catch (Exception $exception){
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }

#[Route("/post/share/delete/{id}", name: "app_post_share_delete",methods: ["DELETE"])]
public function deletePost(Request $request,PostShareRepository $postShareRepository):Response
{
    try {
       $id=$request->attributes->get("_route_params")["id"];
       $post=$postShareRepository->find(intval($id));
       $postShareRepository->remove($post,true);
        return $this->json("deleted",200, ["Content-Type" => "application/json"]);
    }
    catch (Exception $exception){
        return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
    }

}


}


