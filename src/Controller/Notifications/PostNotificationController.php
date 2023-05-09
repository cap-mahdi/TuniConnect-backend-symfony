<?php

namespace App\Controller\Notifications;

use App\Entity\Accounts\Member;
use App\Entity\Notifications\PostNotification;
use App\Entity\Posts\Comment;
use App\Entity\Posts\SharedPost;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/notifications/post_notification')]
class PostNotificationController extends AbstractController
{


    #[Route('/add', name: 'post_notification_add' , methods: ["POST"])]
    public function addPostNotif(ManagerRegistry $doctrine ,Request $request , SerializerInterface $serializer): Response
    {

        //request body: type , postId , relatedTo
            //idOwner of the post
            //idPost
            //idTrigger of notification
            //type de notification


        try {
            $entityManager=$doctrine->getManager();
            $content = $request->request->all();
            $postNotification = new PostNotification();
            $postNotification->setType($content["type"]);
            $postNotification->setCreatedAt(new \DateTime());


            //retrieve the post data
            $post=new SharedPost();
            $sharedPostRepository=$doctrine->getRepository(SharedPost::class);
            $post=$sharedPostRepository->find($content["postId"]);

            $postNotification->setPost($post);


            //retrieve the person who triggered the notif
            $memberRepository=$doctrine->getRepository(Member::class);
            $relatedTo=$memberRepository->find($content["relatedTo"]);
            $postNotification->setRelatedTo($relatedTo);

            $entityManager->persist($postNotification);
            $entityManager->flush();

            //retrieve the owner of the post
            $owner=$memberRepository->find($content["ownerId"]);
            $owner->addNotification($postNotification);
            $entityManager->persist($owner);
            $entityManager->flush();

            $jsonData = $serializer->serialize($postNotification,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'PostNotification:get']);
            return new JsonResponse($jsonData, 200, [], true);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }




}
