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

    public function uploadImage(Request $request,string $type,Member $person):void
    {
        if($request->files->has($type)){
    
            $uploadedFile = $request->files->get($type);
    
        }
    
        if (!$uploadedFile) {
            throw new FileException('No file uploaded');
        }
    
    
        $fileName = uniqid() . '.' . $uploadedFile->guessExtension();
    
            try {
                $uploadedFile->move(
                    $this->getParameter('image_directory'),
                    $fileName
                );
                
                if($type=="cover"){
                    $person->setCoverPicture($fileName);
                }
                else if($type=="profile"){
                   $person->setProfilePicture($fileName); 
                }
                
                
            } catch (FileException $e) {
                throw new FileException($e->getMessage());
            }
    
    }




    #[Route('/register', name: 'member.register' , methods: ['POST'])]
    public function addMember(Request $request ,UserRepository $userRepository,MemberRepository $memberRepository,AddressRepository $addressRepository): Response
    {
        $data=$request->request->all();
        try{
            $person=new Member();
            $address=new Address();
            $user=new User();

            $user->setEmail($data["email"]);
            $user->setPassword($this->hasher->hashPassword($user,$data["password"]));
            $userRepository->save($user,true);

            
        $person->setFirstName($data['firstName']);
        $person->setLastName($data['lastName']);
        $person->setBirthday(new \DateTime($data['birthday']));
        $person->setGender($data['gender']);
        $person->setPhone($data['phone']);
        $person->setDateOfMembership(new \DateTime());
        $person->setUser($user);


        $address->setCity($data['city']);
        $address->setStreet($data['street']);
        $address->setZipCode($data['zipCode']);
        $address->setCountry($data['country']);
        $address->setState($data['state']);
        $addressRepository->save($address,true);
        $person->setAddress($address);



        
        
        $user->setPerson($person);
        $userRepository->save($user,true);
        
        $this->uploadImage($request,"cover",$person);
        $this->uploadImage($request,"profile",$person);
        
        $memberRepository->save($person,true);
      

        $id = $user->getId() ;
        return new JsonResponse($id,200 );
        }


        
        catch(\Exception $e){ 
            return new JsonResponse($e->getMessage(),400);
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



