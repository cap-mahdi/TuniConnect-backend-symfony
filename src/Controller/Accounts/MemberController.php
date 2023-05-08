<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Accounts\AddressRepository;
use App\Repository\Accounts\UserRepository;
use \Doctrine\Persistence\ManagerRegistry ;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/member')]
class MemberController extends AbstractController
{    private HttpKernelInterface $httpKernel;

    public function __construct(HttpKernelInterface $httpKernel,
    private UserPasswordHasherInterface $hasher)
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




   
    #[Route('/remove', name: 'member.remove')]
    public function removeMember(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager() ;
        $member = new Member() ;




        return $this->render('member/remove.html.twig', [
            'member' => $member,
        ]);

    }

    #[Route('/get/friends/{id}', name: 'member.get_friends')]
    public function getFriends(Member $member = null,SerializerInterface $serializer): Response
    {
        try{
            $friends =  $member->getFriends();
            $jsonData = $serializer->serialize($friends, 'json',['groups' => 'member:friend']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);
        }catch(Exception $exception){
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }






    }
}



