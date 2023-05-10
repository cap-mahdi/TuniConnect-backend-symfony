<?php

namespace App\Controller\Chat;

use App\Entity\Accounts\Member;
use App\Entity\Chat\Message;
use App\Entity\Chat\Room;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/room')]

class RoomController extends AbstractController
{
    private HttpKernelInterface $httpKernel;

    public function __construct(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;

    }



    #[Route('/create', name: 'room.create',methods: "POST" )]
    public function create(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer, Member $receiver = null  ): Response
    {
        try {
            $manager = $doctrine->getManager();
            $roomRepository = $doctrine->getRepository(Room::class);
            $content = $request->getContent();
            $data = json_decode($content, true);
            $room  = new Room() ;
            $creator = $doctrine->getRepository(Member::class)->find($data["creatorId"]) ;
            $room->setCreator( $creator) ;
            $room->addMember($creator) ;
            $manager->persist($room);
            $manager->flush();
            $data = $serializer->serialize($room ,
                JsonEncoder::FORMAT ,  [AbstractNormalizer::GROUPS => ['Room:CREATE']]);
            return new JsonResponse($data,201,[],true);

        }
        catch(\Exception $exception){
            return new JsonResponse($exception->getMessage(),500);
        }


    }

    #[Route('/addMember', name: 'room.addMember',methods: "POST" )]
    public function addMember(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer  ): Response
    {
        try {
            $manager = $doctrine->getManager();
            $roomRepository = $doctrine->getRepository(Room::class);
            $content = $request->getContent();
            $data = json_decode($content, true);
            $room  =  $doctrine->getRepository(Room::class)->find($data["roomId"]) ;
            $toAdd = $doctrine->getRepository(Member::class)->find($data["memberId"]) ;

            $room->addMember($toAdd) ;
            $manager->persist($room);
            $manager->flush();
            $data = $serializer->serialize($room ,
                JsonEncoder::FORMAT ,  [AbstractNormalizer::GROUPS => ['Room:CREATE']]);
            return new JsonResponse($data,201,[],true);

        }
        catch(\Exception $exception){
            return new JsonResponse($exception->getMessage(),500);
        }


    }




    #[Route('/addMessage', name: 'room.addMessage',methods: "POST" )]
    public function addMessage(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer ): Response
    {
        try {
            $manager = $doctrine->getManager();
            $roomRepository = $doctrine->getRepository(Room::class);
            $content = $request->getContent();

            $data = json_decode($content, true);
            $room  =  $doctrine->getRepository(Room::class)->find($data["roomId"]) ;

            $subRequestMessage= Request::create('/message/add', 'POST', [], [], [], [],$content);
            $responseMessageJSON = $this->httpKernel->handle($subRequestMessage, HttpKernelInterface::SUB_REQUEST);
            $responseMessage =  json_decode($responseMessageJSON->getContent(), true);
            $messageId = $responseMessage["id"] ;

            $message = $doctrine->getRepository(Message::class)->find($messageId) ;
            $room->addMessage($message) ;
            $manager->persist($room);
            $manager->flush();
            $data = $serializer->serialize($room ,
                JsonEncoder::FORMAT ,  [AbstractNormalizer::GROUPS => ['Room:CREATE']]);

            return new JsonResponse($data,200,[],true);

        }
        catch(\Exception $exception){
            return new JsonResponse($exception->getTrace(),500);
        }


    }

    #[Route('/getRoomMessages/{id<\d+>}', name: 'room.getRoomMessages',methods: "GET" )]
    public function getRoomMessages(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer , $id ): Response
    {
        try {
            $manager = $doctrine->getManager();
            $roomRepository = $doctrine->getRepository(Room::class);

            $room = $roomRepository->find($id) ;

            $messages = $room->getMessages() ;
             $data =  "[" ;

             //copilot are you here?
            foreach ($messages as $message) {
                $data .=$serializer->serialize($message,
                    JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ["Message:GET"]]) . "," ;
            }
            $data = substr($data, 0, -1) ;
            $data .= "]" ;
            $data = json_decode($data) ;
            $data = json_encode($data) ;


/*
            $data = $serializer->serialize($room,
                JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['Room:CREATE']]);*/

            return new JsonResponse($data, 200, [], true);

        } catch (\Exception $exception) {
            return new JsonResponse($exception->getTrace(), 500);
        }


    }


    #[Route('/getRooms/{id<\d+>}', name: 'room.getRooms',methods: "GET" )]
    public function getRooms(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer , $id ): Response
    {
        try {
            $manager = $doctrine->getManager();
            $roomRepository = $doctrine->getRepository(Room::class);

            $rooms = $roomRepository->findRoomsByMemberId($id);
            $data =  "[" ;

            //copilot are you here?
            foreach ($rooms as $room) {
                $data .=$serializer->serialize($room,
                        JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ["Room:CREATE"]]) . "," ;
            }
            $data = substr($data, 0, -1) ;
            $data .= "]" ;
            $data = json_decode($data) ;
            $data = json_encode($data) ;


            /*
                        $data = $serializer->serialize($room,
                            JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['Room:CREATE']]);*/

            return new JsonResponse($data, 200, [], true);

        } catch (\Exception $exception) {
            return new JsonResponse($exception->getTrace(), 500);
        }


    }


    #[Route('/getRoomMembers/{id<\d+>}', name: 'room.getRoomMembers',methods: "GET" )]
    public function getRoomMembers(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer , $id ): Response
    {
        try {
            $manager = $doctrine->getManager();
            $roomRepository = $doctrine->getRepository(Room::class);

            $room = $roomRepository->find($id);
            $members = $room->getMembers() ;
            $data =  "[" ;

            //copilot are you here?
            foreach ($members as $member) {
                $data .=$serializer->serialize($member,
                        JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ["RoomMember:GET"]]) . "," ;
            }
            $data = substr($data, 0, -1) ;
            $data .= "]" ;
            $data = json_decode($data) ;
            $data = json_encode($data) ;


            /*
                        $data = $serializer->serialize($room,
                            JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['Room:CREATE']]);*/

            return new JsonResponse($data, 200, [], true);

        } catch (\Exception $exception) {
            return new JsonResponse($exception->getTrace(), 500);
        }


    }


    }
