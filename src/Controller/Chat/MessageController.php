<?php

namespace App\Controller\Chat;

use App\Entity\Accounts\Member;
use App\Entity\Chat\Message;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("message")]
class MessageController extends AbstractController
{
    /*#[Route('/send/{id<\d+>}', name: 'message.send',methods: "POST")]
    public function sendMessage(Request $request,ManagerRegistry $doctrine, Member $receiver = null ): Response
    {
        $info = $request->request;
        //if receiver do not exist
        if(!$receiver)
            return $this->json("Receiver do not exist",404);

        //search the sender
        $manager = $doctrine->getManager();
        $memberRepository = $doctrine->getRepository(Member::class);
        $sender = $memberRepository->find([
            "id"=>$info->get("id")
        ]);
        //if sender do not exist
        if(!$sender)
            return $this->json("Sender do not exist",404);

        //if sender == receiver
        if($sender == $receiver)
            return $this->json("You cannot send message to yourself",400);

        //creating the message
        $message = new Message();
        $message->setSender($sender);
        $message->setBody($info->get("body"));
        $message->addReceiver($receiver);

        $manager->persist($message);

        $manager->flush();

        return $this->json($message->display(),201);
    }*/
    #[Route('/delete/{id<\d+>}', name: 'message.delete', methods: "DELETE")]
    public function deleteMessage(ManagerRegistry $doctrine,Message $message = null): Response{
        if(!$message){
            return $this->json("The message do not exist",404);
        }else{
            $manager = $doctrine->getManager();
            $manager->remove($message);

            $manager->flush();
            return $this->json("The message has been deleted",200);
        }
    }
    /*#[Route('/get/{id1<\d+>}/{id2<\d+>}', name: 'messages.get', methods: "GET")]
    public function getMessages(Request $request,ManagerRegistry $doctrine,$id1,$id2): Response{
        $memberRepository = $doctrine->getRepository(Member::class);
        $requestedUser = $memberRepository->findBy([
            "id" => $id1
        ]);
        if(!$requestedUser)
            return $this->json("Receiver do not exist ",404);
        $currentUser = $memberRepository->findBy([
            "id" => $id2
        ]);
        if(!$currentUser)
            return $this->json("Current uesr do not exist ",404);
        if($currentUser == $requestedUser)
            return $this->json("User cannot send message to himself",400);

        $messageRepository = $doctrine->getRepository(Message::class);
        $messages = $messageRepository->getConversation($id1,$id2);
        return $this->json(array_map(function ($message){
            return $message->display();
        },$messages),200);
    }*/
    /*#[Route('/update/{id<\d+>}', name: 'message.update', methods: "PATCH")]
    public function updateMessage(Request $request,ManagerRegistry $doctrine,Message $message = null): Response{
        $manager = $doctrine->getManager();
        if(!$message){
            return $this->json('message not found',404);
        }
        $body = $request->request->get("body");
        if(!$message->isEdited())
            $message->setIsEdited(true);
        $message->setBody($body);

        $manager->persist($message);
        $manager->flush();

        return $this->json("message updated successfully",200);

    }*/
}

