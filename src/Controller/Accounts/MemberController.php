<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use \Doctrine\Persistence\ManagerRegistry ;
use \Symfony\Component\HttpFoundation\Request;
use phpDocumentor\Reflection\TypeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/member')]
class MemberController extends AbstractController
{

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
            [AbstractNormalizer::GROUPS => 'member']) ;

        return new JsonResponse($data , 200,  [] , true);

    }
    #[Route('/add', name: 'member.add')]
    public function addMember(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager() ;
        $member = new Member() ;

        $member->setName("Aziz") ;
        $member->setPhone(558885544122) ;
        $member->setEmail("aziz@gmail.com") ;
        $member->setIsAdmin(0) ;
        $currentTime = new \DateTime();
        $member->setDateOfMembership( $currentTime) ;

        $address = $doctrine->getRepository(Address::class)->find(1) ;
        $member->setAddress($address) ;

        $user = $doctrine->getRepository(User::class)->find(3) ;
        $member->setUser($user );


        //Ajouter Member
       $entityManager->persist($member);
        $entityManager->flush();


       return $this->render('member/index.html.twig', [
            'member' => $member,
        ]);

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



