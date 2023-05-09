<?php

namespace App\Controller\Posts;

use App\Entity\Accounts\Member;
use App\Entity\Posts\Post;
use App\Entity\Posts\SharedPost;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Posts\PostRepository;

use Doctrine\Persistence\ManagerRegistry;
use SebastianBergmann\Timer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Exception\BadRequestException,
    JsonResponse,
    Response,
    Request,
    Session\SessionInterface};
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;



#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/add', name: 'post.add', methods: ['POST'])]
    public function addPost(Request $request, MemberRepository $memberRepository, SerializerInterface $serializer,ManagerRegistry $doctrine,SluggerInterface $slugger): Response
    {
        try {


            $file = $request->files->get("image");
            dump($file);
            $data = $request->request->all();
            $member = $memberRepository->find($data['member_id']);

            $newPost = new Post();
            $newPost->setText($data['text']);
            $newPost->setEdited(false);
            $newPost->setOwner($member);
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('posts_images'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $this->json($e->getMessage(),500, ["Content-Type" => "application/json"]);
                }

                $newPost->setPhotos([$newFilename]);
            }

            $newSharedPost = new SharedPost();
            $newSharedPost->setPost($newPost);
            $newSharedPost->setSharer($member);
            $newSharedPost->setIsShared(false);

            $manager = $doctrine->getManager();
            $manager->persist($newPost);
            $manager->persist($newSharedPost);
            $manager->flush();

            $jsonData = $serializer->serialize($newSharedPost,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'SharedPost']);
            return new JsonResponse($jsonData, 200, [], true);


        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }

    #[Route('/:id', name: 'post.timeline', methods:['GET'])]
    public function getPostsTimeline(PostRepository $postRepository, SerializerInterface $serializer,Member $member): Response
    {
        try {
            $memberPosts = arrat_merge($member->getMyPosts(),$member->getSharedPosts());

            $memberFriends = $member->getFriends();
            $posts = [];
            foreach ($memberFriends as $friend){
                foreach($friend->getMyPosts() as $friendPost){
                    $posts[] = $friendPost;
                }
            }
            $timelinePosts = array_merge($memberPosts,$posts);
            $data = $serializer->serialize($posts,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
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
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }

    }

    #[Route('/find', name: 'post.get_by_poster', methods:['GET'])]
    public function getByPoster(Request $request, PostRepository $postRepository,SerializerInterface $serializer): Response
    {
        try {
            $poster_id = $request->query->get('poster_id');
            $posts = $postRepository->findBy(['poster' => $poster_id]);

            if (!$posts) {
                return new JsonResponse(['error' => 'Post not found'], 404);
            }
            $data = $serializer->serialize($posts,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
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
            $post->setUpdatedAt(new \DateTime());
            $postRepository->save($post, true);
            $data = $serializer->serialize($post,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Post:Get']);
            return new JsonResponse($data, 200, [], true);
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
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
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
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
            return $this->json($exception->getMessage(),500, ["Content-Type" => "application/json"]);
        }
    }
}
