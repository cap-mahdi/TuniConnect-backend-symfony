<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Person;
use App\Repository\Posts\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Accounts\AddressRepository;
use App\Repository\Accounts\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/person')]
class PersonController extends AbstractController
{
public function __construct(private UserPasswordHasherInterface $hasher){
    
}


  //upload profile picture
    #[Route('/upload/{id}', name: 'upload', methods: ['POST'])]
    public function upload(Request $request,PersonRepository $personRepository):Response
    {   
        $isCover=false;
        if($request->files->has('cover')){
            $isCover=true;
            $uploadedFile = $request->files->get('cover');

        }
        else{
            if($request->files->has('profile')){
                $isCover=false;
                $uploadedFile = $request->files->get('cover');
    
            }
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
            $idPerson=$request->attributes->get("_route_params")["id"];
            $person=$personRepository->find($idPerson);
            if($isCover){
                $person->setCoverPicture($fileName);
            }
            else{
               $person->setProfilePicture($fileName); 
            }
            $personRepository->save($person,true);
            
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

        return new JsonResponse('/images/' . $fileName,200,[],true);
    
    }


    #[Route('/signup', name: 'signup',methods:['POST'])]
    public function signup(Request $request,AddressRepository $addressRepository,UserRepository $userRepository,MemberRepository $memberRepository):Response
    {
        $data=json_decode($request->getContent(),true);
        try{
            $person=new Member();
            $address=new Address();
            $user=new User();


            
        $person->setFirstName($data['firstName']);
        $person->setLastName($data['lastName']);
        $person->setBirthday(new \DateTime($data['birthday']));
        $person->setGender($data['gender']);
        $person->setPhone($data['phone']);
        $person->setDateOfMembership(new \DateTime());


        $address->setCity($data['city']);
        $address->setStreet($data['street']);
        $address->setZipCode($data['zipCode']);
        $address->setCountry($data['country']);
        $address->setState($data['state']);
        $addressRepository->save($address,true);
        $person->setAddress($address);



        $user->setEmail($data["email"]);
        $user->setPassword($this->hasher->hashPassword($user,$data["password"]));
        $userRepository->save($user,true);
        $person->setUser($user);
        $memberRepository->save($person,true);

        $user->setPerson($person);
        $userRepository->save($user,true);
        $person->setUser($user);
        $memberRepository->save($person,true);

      

        $id = $user->getId() ;
        $result = ["id"=>$id] ;

        return new JsonResponse($result,200);
        }


        
        catch(\Exception $e){
            $memberRepository->remove($person,true);
            $userRepository->remove($user,true);
            $addressRepository->remove($address,true); 
            return new JsonResponse($e->getMessage(),400);
        }
    }
}



