<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Accounts\AddressRepository;
use App\Repository\Accounts\UserRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class authenticationController extends AbstractController
{


    
        public function __construct(RequestStack $requestStack, JWTTokenManagerInterface $jwtManager, private UserPasswordHasherInterface $hasher)
        {
            $this->requestStack = $requestStack;
            $this->jwtManager = $jwtManager;
        }



    private $requestStack;
    private $jwtManager;
    
    #[Route('/get-current-user', name: 'test')]
    public function verifiesToken(UserRepository $userRepository,SerializerInterface $serializer )
    {

        try{

            $request = $this->requestStack->getCurrentRequest();
            
            // Get the token from the request
            $token = $request->headers->get('Authorization');
            
        // Remove the Bearer prefix and trim any whitespace
        $token = str_replace('Bearer ', '', $token);

        // Verify the token
        $payload = $this->jwtManager->parse($token);
        
        
        // If the token is invalid, an exception will be thrown.
        // If the token is valid, $payload will contain the decoded token data.
        
        $user=$userRepository->findOneBy(["email"=>$payload["email"]]);
        $person=$user->getPerson();
        $data = $serializer->serialize($person ,
        JsonEncoder::FORMAT,
        [AbstractNormalizer::GROUPS => 'member']) ;
        return $this->json($data,200, ["Content-Type" => "application/json"]);
    }
    catch(\Exception $exception){
        return $this->json($exception->getMessage());
    }
}




public function uploadImage(Request $request,string $type,Member $person):void
{
    $uploadedFile = null;
    if($request->files->has($type)){

        $uploadedFile = $request->files->get($type);

    }

    if (!$uploadedFile) {
        return;
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





#[Route('/api/register', name: 'member.register' , methods: ['POST'])]
    public function addMember(Request $request ,UserRepository $userRepository,MemberRepository $memberRepository,AddressRepository $addressRepository,JWTTokenManagerInterface $jwtManager): Response
    {
        $data=$request->request->all();
        try{
            $person=new Member();
            $address=new Address();
            $user=new User();
            $user->setEmail($data["email"]);
            $user->setPassword($this->hasher->hashPassword($user,$data["password"]));
            $user->setRoles(["ROLE_USER"]);
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
        dump("here");
        $token=$jwtManager->create($user);
        dump($token);
        return new JsonResponse(['token' => $token],200 );
        }


        
        catch(\Exception $e){ 
            return new JsonResponse($e->getMessage(),400);
        }
    }
    #[Route('/api/login', name: 'login' , methods: ['POST'])]
    public function login(Request $request,UserRepository $userRepository,JWTTokenManagerInterface $jwtManager): Response
    {
        $data=json_decode($request->getContent());
        $data=get_object_vars($data);
        $user=$userRepository->findOneBy(["email"=>$data["email"]]);
        if($user){
            if($this->hasher->isPasswordValid($user,$data["password"])){
                $token=$jwtManager->create($user);
                return new JsonResponse(['token' => $token],200 );
            }
            else{
                return new JsonResponse("Wrong credentials",400);
            }
        }
        else{
            return new JsonResponse("Wrong credentials",400);
        }
    }


}