<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use \Doctrine\Persistence\ManagerRegistry ;
use Exception;
use \Symfony\Component\HttpFoundation\Request;
use phpDocumentor\Reflection\TypeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/member')]
class MemberController extends AbstractController
{    private HttpKernelInterface $httpKernel;

    public function __construct(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;

    }

    #[Route('/post' , name : 'member.post')]
public function post(Request $request) : Response
{
    $data=$request->getContent() ;
    return new JsonResponse($data , 200,  [] , true) ;
}

    #[Route('/{id<\d+>}' , name : 'member.id')]
    public function getById(ManagerRegistry $doctrine , SerializerInterface $serializer , $id) : Response
    {
        $entityManager  = $doctrine->getManager() ;
        $repository =  $entityManager->getRepository(Member::class)  ;
        $allMembers =$repository->find($id) ;

        $data = $serializer->serialize($allMembers ,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'member']) ;

        return new JsonResponse($data , 200,  [] , true);
    }







    #[Route('/' , name : 'member.list')]
    public function index(ManagerRegistry $doctrine , SerializerInterface $serializer) : Response
    {
        $entityManager  = $doctrine->getManager() ;
        $repository =  $entityManager->getRepository(Member::class)  ;
        $allMembers =$repository->findAll() ;



        $data = $serializer->serialize($allMembers ,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'Member:Get']) ;

        return new JsonResponse($data , 200,  [] , true);

    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    #[Route('/register', name: 'member.register' , methods: ['POST'])]
    public function addMember(ManagerRegistry $doctrine ,Request $request , SerializerInterface $serializer): Response
    {
        try{
        $entityManager = $doctrine->getManager() ;
        $body = json_decode($request->getContent(), true);
        $member = new Member() ;

        $member->setName($body['name']) ;
        $member->setPhone($body['phone']) ;
        $currentTime = new \DateTime();
        $member->setDateOfMembership( $currentTime) ;

        ////MUST CHANGE INTO ADDING A NEW ADDRESS
        $address = $doctrine->getRepository(Address::class)->find(1) ;
        $member->setAddress($address) ;

        ////MUST CHANGE INTO ADDING A NEW MEMBER

            $bodyUser = $body["user"] ;
            $bodyUser = ["email"=>$bodyUser["email"] , "password"=>$bodyUser["password"]];
            $bodyUser = json_encode($bodyUser) ;

            $subRequestUser = Request::create('/user/add', 'POST', [], [], [], [], $bodyUser);

            // simulate an HTTP request to the desired route
            $responseUserJSON = $this->httpKernel->handle($subRequestUser, HttpKernelInterface::SUB_REQUEST);

            $responseUser =  json_decode($responseUserJSON->getContent(), true);

            $userId = $responseUser["id"] ;

            $user = $doctrine->getRepository(User::class)->find($userId) ;$member->setUser($user );
        //SAVE MEMBER
        $entityManager->persist($member);
        $entityManager->flush();

        //SERIALIZATION INTO JSON
            $data = $serializer->serialize($member ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'Member:Post']) ;


            return new JsonResponse($data , 201,  [] , true);

        }
        catch (Exception $exception){
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }


    #[Route('/remove', name: 'member.remove')]
    public function removeMember(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager() ;
        $member = new Member() ;




        return $this->render('member/remove.html.twig', [
            'member' => $member,
        ]);

    }
}



