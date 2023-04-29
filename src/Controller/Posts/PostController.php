<?php

namespace App\Controller\Posts;

use App\Entity\Posts\Post;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Posts\PostRepository;

use SebastianBergmann\Timer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;


#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/add', name: 'post.add', methods: ['POST'])]
    public function addPost(Request $request, MemberRepository $memberRepository, PostRepository $postRepository): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $poster = $memberRepository->find($data["poster_id"]);
            $post = new Post();
            $post->setText($data["text"]);
            $poster->addPost($post);
            $postRepository->save($post, true);
            return $this->json("Post added successfully");
        }
        catch (Exception $exception) {
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }

    #[Route('/', name: 'post.getAll', methods:['GET'])]
    public function getAll(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->json($posts);
    }

    #[Route('/{id<\d+>}', name: 'post.get', methods:['GET'])]
    public function getById(Post $post): Response
    {
        return $this->json($post);
    }

    #[Route('/update/{id<\d+>}', name: 'post.update', methods:['PATCH'])]
    public function update(Request $request, PostRepository $postRepository, Post $post): Response
    {
        $data = json_decode($request->getContent(), true);
        $post->setText($data['text']);
        $post->setDate(new \DateTime());
        $postRepository->save($post, true);
        return $this->json("Post updated successfully");

    }

    #[Route('/delete/{id<\d+>}', name:'post.delete', methods:['DELETE'])]
    public function delete(PostRepository $postRepository, Post $post): Response
    {
        $postRepository->remove($post, true);
        return $this->json("Post deleted successfully");
    }
}
