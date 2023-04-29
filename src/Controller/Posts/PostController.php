<?php

namespace App\Controller\Posts;

use App\Entity\Posts\Post;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Posts\PostRepository;

use SebastianBergmann\Timer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Exception\BadRequestException, JsonResponse, Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/add', name: 'post.add', methods: ['POST'])]
    public function addPost(Request $request, MemberRepository $memberRepository, PostRepository $postRepository, SerializerInterface $serializer): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $poster = $memberRepository->find($data["poster_id"]);
            $post = new Post();
            $post->setText($data["text"]);
            $poster->addPost($post);
            $postRepository->save($post, true);
            $data = $serializer->serialize($post,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Post']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }

    #[Route('/', name: 'post.list', methods:['GET'])]
    public function getAll(PostRepository $postRepository, SerializerInterface $serializer): Response
    {
        try {
            $posts = $postRepository->findAll();
            $data = $serializer->serialize($posts,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }

    #[Route('/get', name: 'post.get', methods:['GET'])]
    public function getById(Request $request, PostRepository $postRepository,SerializerInterface $serializer): Response
    {
        try {
            $id = $request->query->get('id');
            $post = $postRepository->find($id);

            if (!$post) {
                return new JsonResponse(['error' => 'Post not found'], 404);
            }
            $data = $serializer->serialize($post,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }

    }

    #[Route('/update', name: 'post.update', methods:['PUT'])]
    public function update(Request $request, PostRepository $postRepository, SerializerInterface $serializer): Response
    {
        try {
            $id = $request->query->get('id');
            $post = $postRepository->find($id);
            $data = json_decode($request->getContent(), true);
            $post->setText($data['text']);
            $post->setDate(new \DateTime());
            $postRepository->save($post, true);
            $data = $serializer->serialize($post,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }

    }

    #[Route('/delete', name:'post.delete', methods:['DELETE'])]
    public function delete(Request $request, PostRepository $postRepository): Response
    {
        try {
            $id = $request->query->get('id');
            $post = $postRepository->find($id);
            $postRepository->remove($post, true);
            return new JsonResponse("Post deleted successfully");
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }

    #[Route('/update/likes', name: 'post.update_likes', methods: ['PUT'])]
    public function updateLikes(Request $request, PostRepository $postRepository, MemberRepository $memberRepository, SerializerInterface $serializer): Response
    {
        try {
            $id = $request->query->get('id');
            $post = $postRepository->find($id);
            $poster_id = $request->query->get('poster_id');
            $member = $memberRepository->find($poster_id);
            if ($post->getMemberLikes()->contains($member)) {
                $post->removeMemberLike($member);
                $post->setTotalLikes($post->getTotalLikes()-1);
            } else {
                $post->addMemberLike($member);
                $post->setTotalLikes($post->getTotalLikes()+1);
            }
            $postRepository->save($post, true);
            $data = $serializer->serialize($post,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }
}
