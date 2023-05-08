<?php

namespace App\Controller\Posts;

use App\Repository\Posts\CommentRepository;
use App\Repository\Posts\SharedPostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Posts\Comment;
use App\Entity\Posts\SharedPost;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/comment')]
class CommentController extends AbstractController
{
    //delete comment
    #[Route('/delete/{id}', name: 'comment.delete', methods: ['DELETE'])]
    public function deleteComment(Comment $comment = null,SerializerInterface $serializer,ManagerRegistry $doctrine): Response
    {
        try{
            $entityManager = $doctrine->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
            return new Response("Comment deleted", 200, ["Content-Type" => "application/json"]);
        }catch(\Exception $exception){
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }
    //update comment
    #[Route('/update/{id}', name: 'comment.update', methods: ['PUT'])]
    public function updateComment(Comment $comment = null,SerializerInterface $serializer,ManagerRegistry $doctrine,Request $resuest): Response
    {
        try{
            $entityManager = $doctrine->getManager();
            $data = json_decode($resuest->getContent(), true);
            $comment->setText($data['text']);
            $entityManager->persist($comment);
            $entityManager->flush();
            return new Response("Comment updated", 200, ["Content-Type" => "application/json"]);
        }catch(\Exception $exception){
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }

    //get comments by post
    #[Route('/get/all/{id}', name: 'comment.get_all', methods: ['GET'])]
    public function getAllComments(SharedPost $sharedPost,SerializerInterface $serializer,ManagerRegistry $doctrine): Response
    {
        try{
            $comments = $sharedPost->getComments()->toArray();
            $jsonData = $serializer->serialize($comments, 'json', ['groups' => 'Comment:GetAll']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);
        }catch(\Exception $exception){
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }

    //get comments by post paginated
    #[Route('/get/all/paginated/{id}', name: 'comment.get_all_paginated', methods: ['GET'])]
    public function getAllCommentsPaginated($id,SerializerInterface $serializer,ManagerRegistry $doctrine,Request $request,CommentRepository $commentRepository): Response
    {
        try{
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
            $comments = $commentRepository->findBy(["post"=>$id],["createdAt"=>"DESC"],$limit,$offset);
            $jsonData = $serializer->serialize($comments, 'json', ['groups' => 'Comment:GetAll']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);
        }catch(\Exception $exception){
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }



}
