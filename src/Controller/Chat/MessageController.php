<?php

namespace App\Controller\Chat;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Chat\Message;
use App\Entity\Accounts\Person;
use App\Repository\Accounts\MemberRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("message")]
class MessageController extends AbstractController
{
    #[Route('/add/{id<\d+>}', name: 'message.send',methods: "POST")]
    public function sendMessage(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer, Member $receiver = null ): Response
    {
        try {
            $info =  json_decode($request->getContent(), true);

            if(!$receiver)
                return new JsonResponse("Receiver do not exist",404);

            $manager = $doctrine->getManager();
            $memberRepository = $doctrine->getRepository(Member::class);
            $sender = $memberRepository->find($info["sender"]["id"]);

            if(!$sender)
                return new JsonResponse("Sender do not exist",404);

            if($sender == $receiver)
                return new JsonResponse("You cannot send message to yourself",400);

            $message = new Message();
            $message->setSender($sender);
            $message->setBody($info["body"]);
            $message->addReceiver($receiver);

            $manager->persist($message);

            $manager->flush();
           $data = $serializer->serialize($message ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Message:POST']]);
            return new JsonResponse($data,201,[],true);
        }catch(\Exception $exception){
            return new JsonResponse($exception->getMessage(),500);
        }

    }
    #[Route('/delete/{id<\d+>}', name: 'message.delete', methods: "DELETE")]
    public function deleteMessage(ManagerRegistry $doctrine,Message $message = null): Response{
        try{
            if(!$message){
                return $this->json("The message do not exist",404);
            }else{
                $manager = $doctrine->getManager();
                $manager->remove($message);

                $manager->flush();
                return $this->json("The message has been deleted",200);
            }
        }catch(\Exception $exception){
            return new JsonResponse($exception->getMessage(),500);
        }

    }
    #[Route('/get', name: 'message.get.conversation', methods: "GET")]
    public function getMessages(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer): Response{
        try{
            $info =  json_decode($request->getContent(), true);
            $memberRepository = $doctrine->getRepository(Member::class);
            $receiverId = $info["receiver"];
            $senderId = $info["sender"]    ;
            $requestedUser = $memberRepository->find($receiverId);
            if(!$requestedUser)
                return $this->json("Receiver do not exist ",404);
            $currentUser = $memberRepository->find($senderId);
            if(!$currentUser)
                return $this->json("Current uesr do not exist ",404);
            if($currentUser == $requestedUser)
                return $this->json("User cannot send message to himself",400);

            $messageRepository = $doctrine->getRepository(Message::class);
            $messages = $messageRepository->findConversation($receiverId,$senderId);
            $data = $serializer->serialize($messages ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Message:POST']]);
            return new JsonResponse($data,200,[],true);
        }catch (\Exception $exception){
            return new JsonResponse($exception->getMessage(),500);
        }


    }


}

