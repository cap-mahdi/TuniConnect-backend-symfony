<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use App\Entity\Covoiturage\RequestCovoiturage;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Covoiturage\CovoiturageRepository;
use App\Repository\Covoiturage\RequestCovoiturageRepository;
use \Doctrine\Persistence\ManagerRegistry ;
use Exception;
use \Symfony\Component\HttpFoundation\Request;
use phpDocumentor\Reflection\TypeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        $member->setLastName($body['name']) ;
        $member->setPhone($body['phone']) ;
        $currentTime = new \DateTime();
        $member->setDateOfMembership( $currentTime) ;

        //// ADDING A NEW ADDRESS


            $bodyAddress = $body["address"] ;
            $bodyAddress = json_encode($bodyAddress) ;
        $subRequestAddress = Request::create('/address/add', 'POST', [], [], [], [], $bodyAddress);
            $responseAddressJSON = $this->httpKernel->handle($subRequestAddress, HttpKernelInterface::SUB_REQUEST);
            $responseAddress =  json_decode($responseAddressJSON->getContent(), true);
            $addressId = $responseAddress["id"] ;

            $address = $doctrine->getRepository(Address::class)->find($addressId) ;
            $member->setAddress($address) ;


        ////ADDING A NEW MEMBER

            $bodyUser = $body["user"] ;
                    //$bodyUser = ["email"=>$bodyUser["email"] , "password"=>$bodyUser["password"]];
            $bodyUser = json_encode($bodyUser) ;

            $subRequestUser = Request::create('/user/add', 'POST', [], [], [], [], $bodyUser);

            // simulate an HTTP request to the desired route
            $responseUserJSON = $this->httpKernel->handle($subRequestUser, HttpKernelInterface::SUB_REQUEST);

            $responseUser =  json_decode($responseUserJSON->getContent(), true);

            $userId = $responseUser["id"] ;

            $user = $doctrine->getRepository(User::class)->find($userId) ;
            $member->setUser($user );
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

    #[Route('/sendCovRequest', name: 'member.CovRequest', methods: ['POST'])]
    public function sendRequest(Request $request, CovoiturageRepository $covoiturageRepository, MemberRepository $memberRepository, RequestCovoiturageRepository $requestCovoiturageRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $sender = $memberRepository->find($request->query->get('id'));
            $covoiturage = $covoiturageRepository->find($request->query->get('covoiturage_id'));

            if ($covoiturage->getNumberOfPlacesTaken() >= $covoiturage->getNumberOfPlaces()) {
                return new JsonResponse("No more places available in the covoiturage", Response::HTTP_BAD_REQUEST, [], true);
            }

            $existingRequest = $requestCovoiturageRepository->findOneBy(['covoiturage' => $covoiturage, 'sender' => $sender]);
            if ($existingRequest && $existingRequest->getStatus() != 'rejected') {
                return new JsonResponse("You have already sent a request for this covoiturage", Response::HTTP_BAD_REQUEST, [], true);
            }

            $req = new RequestCovoiturage();
            $req->setCovoiturage($covoiturage);
            $req->setSender($sender);
            $requestCovoiturageRepository->save($req, true);

            $data = $serializer->serialize($req, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);

        } catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode(), [], true);
        }
    }

    #[Route('/acceptCov', name: 'member.acceptCov', methods: ['PUT'])]
    public function acceptCovRequest(Request $request, RequestCovoiturageRepository $requestCovoiturageRepository, MemberRepository $memberRepository, CovoiturageRepository $covoiturageRepository, SerializerInterface $serializer): JsonResponse {
        try {
            $sender = $memberRepository->find($request->query->get('sender_id'));
            $covoiturage = $covoiturageRepository->find($request->query->get('covoiturage_id'));
            $Request = $requestCovoiturageRepository->findOneBy(['covoiturage' => $covoiturage, 'sender' => $sender]);
            if (!$Request) {
                return new JsonResponse("Request not found", Response::HTTP_BAD_REQUEST, [], true);
            }
            if ($Request->getStatus() !== 'pending') {
                return new JsonResponse("Request already treated", Response::HTTP_BAD_REQUEST, [], true);
            }

            $Request->setStatus('accepted');
            $covoiturage->setNumberOfPlacesTaken($covoiturage->getNumberOfPlacesTaken()+1);
            $sender->addCovoituragesTaken($covoiturage);
            $requestCovoiturageRepository->save($Request, true);
            $covoiturageRepository->save($covoiturage, true);

            $data = $serializer->serialize($Request, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode(), [], true);
        }
    }

    #[Route('/rejectCov', name: 'member.rejectCov', methods: ['PUT'])]
    public function rejectCovRequest(Request $request, RequestCovoiturageRepository $requestCovoiturageRepository, MemberRepository $memberRepository, CovoiturageRepository $covoiturageRepository, SerializerInterface $serializer): JsonResponse {
        try {
            $sender = $memberRepository->find($request->query->get('sender_id'));
            $covoiturage = $covoiturageRepository->find($request->query->get('covoiturage_id'));
            $Request = $requestCovoiturageRepository->findOneBy(['covoiturage' => $covoiturage, 'sender' => $sender]);
            if (!$Request) {
                return new JsonResponse("Request not found", Response::HTTP_BAD_REQUEST, [], true);
            }
            if ($Request->getStatus() !== 'pending') {
                return new JsonResponse("Request already treated", Response::HTTP_BAD_REQUEST, [], true);
            }

            $Request->setStatus('rejected');
            $requestCovoiturageRepository->save($Request, true);

            $data = $serializer->serialize($Request, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode(), [], true);
        }
    }

}



