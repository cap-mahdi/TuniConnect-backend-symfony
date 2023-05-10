<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\FriendRequest;
use App\Entity\Accounts\Member;
use App\Entity\Notifications\PostNotification;
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


#[Route('/friend/request')]
class FriendRequestController extends AbstractController
{
    #[Route("/add" , name:'add_friend_request' , methods: ["POST"])]
    public function sendFriendRequest(ManagerRegistry $doctrine , Request $request ,  SerializerInterface $serializer): JsonResponse
    {
        //request body : receiver  , sender
        try {
            $entityManager=$doctrine->getManager();
            $memberRepository=$doctrine->getRepository(Member::class);


            $content = json_decode($request->getContent());

            $friendRequest= new FriendRequest();

            //retrieve the sender
            $senderId=$content->sender;
            $sender=new Member();
            $sender=$memberRepository->find($senderId);
            $friendRequest->setSender($sender);

            //retrieve the receiver
            $receiverId=$content->receiver;
            $receiver=new Member();
            $receiver=$memberRepository->find($receiverId);
            $friendRequest->setReceiver($receiver);


            $friendRequest->setStatus("pending");

            $entityManager->persist($friendRequest);
            $entityManager->flush();

            $jsonData = $serializer->serialize($friendRequest,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'friendRequest:get']);
            return new JsonResponse($jsonData, 200, [], true);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }


    #[Route("/edit" , name:'reply_friend_request' , methods: ["POST"])]
    public function replyToFriendRequest(ManagerRegistry $doctrine , Request $request ,  SerializerInterface $serializer): JsonResponse
    {
        //request body: status, friendRequestId
        try {
            $entityManager=$doctrine->getManager();
            $memberRepository=$doctrine->getRepository(Member::class);
            $friendRequestRepository = $doctrine->getRepository(FriendRequest::class);

//            $content = $request->request->all();

            $content = json_decode($request->getContent());

            $friendRequest= new FriendRequest();
            $friendRequestId=$content->friendRequestId;
            $friendRequest=$friendRequestRepository->find($friendRequestId);

            //retrieve the sender
            $sender=new Member();
            $sender=$friendRequest->getSender();


            //retrieve the receiver
            $receiver=new Member();
            $receiver=$friendRequest->getReceiver();




            $newStatus=$content->status;
            if($newStatus=="Accepted"){
                $sender->addFriend($receiver);
                $receiver->addFriend($sender);
            }
            $receiver->removeFriendRequest($friendRequest);

            $friendRequest->setStatus($newStatus);

            $entityManager->persist($friendRequest);
            $entityManager->persist($sender);
            $entityManager->persist($receiver);
            $entityManager->flush();

            $jsonData = $serializer->serialize($friendRequest,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'friendRequest:get']);
            return new JsonResponse($jsonData, 200, [], true);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);
        }
    }


//verify if a member sent a friend request to a member
    #[Route("/{idSender}/sent_to/{idReceiver}" , name:"verify_sending_friend_request")]
    public function verifyFriendRequestSending($idSender , $idReceiver , ManagerRegistry $doctrine ,  SerializerInterface $serializer)
    {
        try {
            $friendRequestRepository = $doctrine->getRepository(FriendRequest::class);
            $friendRequest = new FriendRequest();
            $friendRequest = $friendRequestRepository->findBy([
                "sender" => $idSender,
                "receiver" => $idReceiver
            ]);
            $data = [
                'sentRequest' => $friendRequest!=null
            ];

            return new JsonResponse($data, 200, ['Content-Type' => 'application/json']);

        } catch (\Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }
    }

}
