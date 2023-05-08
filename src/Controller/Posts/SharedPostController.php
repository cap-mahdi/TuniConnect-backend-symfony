<?php

namespace App\Controller\Posts;

use App\Entity\Accounts\Member;
use App\Entity\Posts\Comment;
use App\Entity\Posts\Post;
use App\Repository\Accounts\MemberRepository;
use App\Entity\Posts\SharedPost;
use App\Repository\Posts\PostRepository;
use App\Repository\Posts\SharedPostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/shared/post')]
class SharedPostController extends AbstractController
{
    #[Route('/get/all/{id}', name: 'shared_post.get_all', methods: ['GET'])]
    public function getAllPosts($id, SerializerInterface $serializer,SharedPostRepository $sharedPostRepository): Response
    {
        try {
            $posts = $sharedPostRepository->findTimelinePost($id);
            foreach ($posts as $sharedPost) {
                $sharedPost->setShares($sharedPost->getPost()->getSharedPosts()->count() - 1);

            }
            $jsonData = $serializer->serialize($posts, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }
    //count all timeline posts
    #[Route('/count/all/{id}', name: 'shared_post.count_all', methods: ['GET'])]
    public function countAllPosts(Member $member = null, SerializerInterface $serializer): Response
    {
        try {
            $memberPosts = $member->getSharedPosts()->toArray();
            $memberFriends = $member->getFriends();
            $friendPosts = [];
            foreach ($memberFriends as $friend) {
                $friendPosts = array_merge($friendPosts, (array)$friend->getSharedPosts()->toArray());
            }
            $posts = array_merge($memberPosts, $friendPosts);
            $count = count($posts);
            return new Response($count, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }
    //get post using offset and limit ordered by date
    #[Route('/get/all/paginated/{id}', name: 'shared_post.get_all_offset_limit', methods: ['GET'])]
    public function getAllPostsOffsetLimit($id, SerializerInterface $serializer,Request $request,SharedPostRepository $sharedPostRepository): Response
    {
        try {
            $limit = $request->query->get('limit');
            $offset = $request->query->get('offset');
            $posts = $sharedPostRepository->findTimelinePost($id,$limit,$offset);
            foreach ($posts as $sharedPost) {
                $sharedPost->setShares($sharedPost->getPost()->getSharedPosts()->count() - 1);

            }
            $jsonData = $serializer->serialize($posts, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    #[Route('/get/user/{id}', name: 'shared_post.get_all_by_user', methods: ['GET'])]
    public function getAllPostsByUser(Member $member = null, SerializerInterface $serializer): Response
    {
        try {
            $memberPosts = $member->getSharedPosts()->toArray();
            usort($memberPosts, function ($sharedPost1, $sharedPost2) {
                $date1 = $sharedPost1->getDate();
                $date2 = $sharedPost2->getDate();
                if ($date1 == $date2) {
                    return 0;
                }
                return ($date1 > $date2) ? -1 : 1;
            });
            $jsonData = $serializer->serialize($memberPosts, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    //share a post
    #[Route('/share/{id}', name: 'shared_post.share', methods: ['POST'])]
    public function sharePost(SharedPost $sharedPost = null, SerializerInterface $serializer, MemberRepository $memberRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $member = $memberRepository->find($data['member_id']);
            $newSharedPost = new SharedPost();
            $newSharedPost->setSharer($member);
            $newSharedPost->setPost($sharedPost->getPost());
            $newSharedPost->setIsShared(true);
            $manager = $doctrine->getManager();

            $manager->persist($newSharedPost);

            $manager->flush();
            $jsonData = $serializer->serialize($newSharedPost, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    //like/dislike a post
    #[Route('/like/{id}', name: 'shared_post.like', methods: ['POST'])]
    public function likePost(SharedPost $sharedPost = null, SerializerInterface $serializer, MemberRepository $memberRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $member = $memberRepository->find($data['member_id']);

            if ($sharedPost->getLikers()->contains($member)) {
                $sharedPost->removeLiker($member);
            } else {
                $sharedPost->addLiker($member);
            }
            $manager = $doctrine->getManager();

            $manager->persist($sharedPost);

            $manager->flush();
            $jsonData = $serializer->serialize($sharedPost, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    //comment on a post
    #[Route('/comment/{id}', name: 'shared_post.comment', methods: ['POST'])]
    public function commentPost(SharedPost $sharedPost = null, SerializerInterface $serializer, MemberRepository $memberRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $member = $memberRepository->find($data['member_id']);
            $newComment = new Comment();
            $newComment->setText($data['text']);
            $newComment->setCommenter($member);
            $sharedPost->addComment($newComment);
            $manager = $doctrine->getManager();

            $manager->persist($newComment);
            $manager->persist($sharedPost);

            $manager->flush();
            $jsonData = $serializer->serialize($sharedPost, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    //delete a post
    #[Route('/delete/{id}', name: 'shared_post.delete', methods: ['DELETE'])]
    public function deletePost(SharedPost $sharedPost = null, ManagerRegistry $doctrine, SharedPostRepository $sharedPostRepository): Response
    {
        try {
            $manager = $doctrine->getManager();
            $manager->remove($sharedPost);
            if (!$sharedPost->isIsShared()) {
                $sharedPosts = $sharedPostRepository->findBy(['post' => $sharedPost->getPost()]);
                foreach ($sharedPosts as $sharedPost) {
                    $manager->remove($sharedPost);
                }
                $manager->remove($sharedPost->getPost());

            }
            $manager->flush();
            return new Response("Post deleted", 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    //get post by id
    #[Route('/get/{id}', name: 'shared_post.get', methods: ['GET'])]
    public function getPost(SharedPost $sharedPost = null, SerializerInterface $serializer): Response
    {
        try {
            $jsonData = $serializer->serialize($sharedPost, 'json', ['groups' => 'SharedPost']);
            $data = json_decode($jsonData, true);
            $data['shares'] = $sharedPost->getPost()->getSharedPosts()->count() - 1;
            $jsonData = json_encode($data);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }

    //update a post
    #[Route('/update/{id}', name: 'shared_post.update', methods: ['PUT'])]
    public function updatePost(SharedPost $sharedPost = null, SerializerInterface $serializer, ManagerRegistry $doctrine, Request $request,SluggerInterface $slugger): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            dump($data);

            $post = $sharedPost->getPost();
            $post->setText($data['text']);
            $post->setEdited(true);

            $manager = $doctrine->getManager();
            $manager->persist($post);
            $manager->flush();


            $jsonData = $serializer->serialize($sharedPost, 'json', ['groups' => 'SharedPost']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }


}