<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\Member;
use App\Entity\Accounts\User;
use App\Entity\Covoiturage\RequestCovoiturage;
use App\Entity\Posts\SharedPost;
use App\Repository\Covoiturage\CovoiturageRepository;
use App\Repository\Covoiturage\RequestCovoiturageRepository;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Accounts\AddressRepository;
use App\Repository\Accounts\UserRepository;
use App\Repository\Posts\CommentRepository;
use App\Repository\Posts\PostRepository;
use App\Repository\SharedPostRepository;
use \Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/member')]
class MemberController extends AbstractController
{
    private HttpKernelInterface $httpKernel;

    public function __construct(HttpKernelInterface                 $httpKernel,
                                private UserPasswordHasherInterface $hasher)
    {
        $this->httpKernel = $httpKernel;

    }

    #[Route('/post', name: 'member.post')]
    public function post(Request $request): Response
    {
        $data = $request->getContent();
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id<\d+>}', name: 'member.id')]
    public function getById(ManagerRegistry $doctrine, SerializerInterface $serializer, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Member::class);
        $allMembers = $repository->find($id);

        $data = $serializer->serialize($allMembers,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'member']);

        return new JsonResponse($data, 200, [], true);
    }


    #[Route('/', name: 'member.list')]
    public function index(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Member::class);
        $allMembers = $repository->findAll();


        $data = $serializer->serialize($allMembers,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'Member:Get']);

        return new JsonResponse($data, 200, [], true);

    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function uploadImage(Request $request, string $type, Member $person): void
    {
        if ($request->files->has($type)) {

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

            if ($type == "cover") {
                $person->setCoverPicture($fileName);
            } else if ($type == "profile") {
                $person->setProfilePicture($fileName);
            }


        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

    }


    #[Route('/register', name: 'member.register', methods: ['POST'])]
    public function addMember(Request $request, UserRepository $userRepository, MemberRepository $memberRepository, AddressRepository $addressRepository): Response
    {
        $data = $request->request->all();
        try {
            $person = new Member();
            $address = new Address();
            $user = new User();

            $user->setEmail($data["email"]);
            $user->setPassword($this->hasher->hashPassword($user, $data["password"]));
            $userRepository->save($user, true);


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
            $addressRepository->save($address, true);
            $person->setAddress($address);


            $user->setPerson($person);
            $userRepository->save($user, true);

            $this->uploadImage($request, "cover", $person);
            $this->uploadImage($request, "profile", $person);

            $memberRepository->save($person, true);


            $id = $user->getId();
            return new JsonResponse($id, 200);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }
    }


    #[Route('/remove', name: 'member.remove')]
    public function removeMember(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $member = new Member();


        return $this->render('member/remove.html.twig', [
            'member' => $member,
        ]);

    }

    #[Route('/get/friends/{id}', name: 'member.get_friends')]
    public function getFriends(Member $member = null, SerializerInterface $serializer): Response
    {
        try {
            $friends = $member->getFriends();
            $jsonData = $serializer->serialize($friends, 'json', ['groups' => 'member:friend']);
            return new Response($jsonData, 200, ["Content-Type" => "application/json"]);
        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }

    }


    //get the member's notifications
    #[Route('/get/notification/{id}', name: 'member_get_notifications')]
    public function getNotifications(Member $member = null, SerializerInterface $serializer): Response
    {
        try {
            $notifications = $member->getNotifications();
            return $this->json($notifications);
        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }
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
    public function acceptCovRequest(Request $request, RequestCovoiturageRepository $requestCovoiturageRepository, MemberRepository $memberRepository, CovoiturageRepository $covoiturageRepository, SerializerInterface $serializer): JsonResponse
    {
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
            $covoiturage->setNumberOfPlacesTaken($covoiturage->getNumberOfPlacesTaken() + 1);
            $sender->addCovoituragesTaken($covoiturage);
            $requestCovoiturageRepository->save($Request, true);
            $covoiturageRepository->save($covoiturage, true);

            $data = $serializer->serialize($Request, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        } catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode(), [], true);
        }
    }

    #[Route('/rejectCov', name: 'member.rejectCov', methods: ['PUT'])]
    public function rejectCovRequest(Request $request, RequestCovoiturageRepository $requestCovoiturageRepository, MemberRepository $memberRepository, CovoiturageRepository $covoiturageRepository, SerializerInterface $serializer): JsonResponse
    {
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
        } catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode(), [], true);
        }
    }


//  get friend request by member id
    #[Route('/get/friend/request/{id}', name: 'get_friend_request')]
    public function getFriendRequestsByMember($id, ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        try {
            $memberRepository = $doctrine->getRepository(Member::class);
            $member = $memberRepository->find($id);
            $friendRequests = $member->getFriendRequests();
            $jsonData = $serializer->serialize($friendRequests,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => 'friendRequest:get']);
            return new JsonResponse($jsonData, 200, [], true);

        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }


    }



//  get friend request by member id
//get member number of comments
    #[Route('/get/number/comments/{id}', name: 'get_member_number_comments')]
    public function getMemberNumberOfComments($id, CommentRepository $commentRepository): Response
    {
        try {
            $count = count($commentRepository->findBy(['commenter' => $id]));
            return new JsonResponse($count, 200, [], true);

        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }

    }
    //get number of likes of member
    #[Route('/get/number/likes/{id}', name: 'get_member_number_likes')]
    public function getMemberNumberOfLikes(Member $member =null): Response
    {
        try {

            return new JsonResponse(count($member->getLikedPosts()), 200, [], true);

        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }

    }

    //get number of shares of member
    #[Route('/get/number/shares/{id}', name: 'get_member_number_shares')]
    public function getMemberNumberOfShares(Member $member =null): Response
    {
        try {

            return new JsonResponse(count($member->getSharedPosts()), 200, [], true);

        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }

    }
    //get number of photos of member
    #[Route('/get/number/photos/{id}', name: 'get_member_number_photos')]
    public function getMemberNumberOfPhotos(Member $member = null,PostRepository $postRepository): Response
    {
        try {
            $posts = $postRepository->findBy(['owner' => $member]);
            $counts = 0;
            foreach ($posts as $post) {
                if ($post->getPhotos() != null) {
                    $counts++;
                }
            }
            $counts += $member->getProfilePicture() != null ? 1 : 0;
            $counts += $member->getCoverPicture() != null ? 1 : 0;
            return new JsonResponse($counts, 200, [], true);

        } catch (Exception $exception) {
            return $this->json($exception->getMessage(), 500, ["Content-Type" => "application/json"]);

        }

    }

    #[Route('/search/{keyword}', name: 'member.search')]
    public function searchMembers(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer ,  $keyword): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Member::class);

        $members = $repository->createQueryBuilder('m')
            ->where('m.firstName LIKE :keyword OR m.lastName LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->getQuery()
            ->getResult();

        $data = $serializer->serialize($members,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'member']);

        return new JsonResponse($data, 200, [], true);
    }
}



