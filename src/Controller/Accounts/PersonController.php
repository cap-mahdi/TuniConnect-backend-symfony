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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/person')]
class PersonController extends AbstractController
{
public function __construct(private UserPasswordHasherInterface $hasher){
    
}


//get person by id passed in path
    #[Route('/{id<\d+>}', name: 'person.id', methods: ['GET'])]
    public function getById(MemberRepository $memberRepository,SerializerInterface $serializer , $id): Response
    {
        $person = $memberRepository->find($id);
        $data = $serializer->serialize($person,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'member']);
        return new JsonResponse($data, 200, [], true);
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
                $uploadedFile = $request->files->get('profile');
    
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



    

}



