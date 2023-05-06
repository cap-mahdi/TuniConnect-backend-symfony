<?php

namespace App\Controller\Covoiturage;

use App\Entity\Covoiturage\Covoiturage;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Covoiturage\CovoiturageRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/', name: 'covoi.create', methods: ['POST'])]
    public function createCovoiturage(Request $request, CovoiturageRepository $covoiturageRepository, MemberRepository $memberRepository, SerializerInterface $serializer): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $cov = new Covoiturage();
            $driver = $memberRepository->find($data['driver_id']);
            $cov->setDriver($driver);
            $cov->setDestination($data['destination']);
            $cov->setDeparture($data['departure']);
            $cov->setDepartureTime(new \DateTime($data['departure_time']));
            $cov->setNumberOfPlaces($data['number_of_places']);
            $cov->setPrice($data['price']);
            $cov->setDescription($data['description']);
            $cov->setCreatedAt(new \DateTimeImmutable());

            $covoiturageRepository->save($cov, true);
            $data = $serializer->serialize($cov ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Cov:POST']]);
            return new JsonResponse($data,Response::HTTP_CREATED,[],true);

        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(),  $e->getCode(), [], true);
        }

    }

    #[Route('/', name: 'covoi.getAll', methods: ['GET'])]
    public function getAll(CovoiturageRepository $repository, SerializerInterface $serializer): JsonResponse {
        try {
            $covs = $repository->findAll();
            $data = $serializer->serialize($covs ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Cov:GET']]);
            return new JsonResponse($data,Response::HTTP_OK,[],true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(),  $e->getCode(), [], true);
        }
    }

    #[Route('/find', name: 'covoi.findID', methods: ['GET'])]
    public function findByID(CovoiturageRepository $repository, SerializerInterface $serializer, Request $request): JsonResponse {
        try {
            $cov = $repository->find($request->query->get('id'));
            $data = $serializer->serialize($cov ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Cov:GET']]);
            return new JsonResponse($data,Response::HTTP_OK,[],true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(),  $e->getCode(), [], true);
        }
    }

    #[Route('/driver', name: 'covoi.findDriver', methods: ['GET'])]
    public function findByDriver(CovoiturageRepository $covRepository,MemberRepository $memberRepository, SerializerInterface $serializer, Request $request): JsonResponse {
        try {
            $driver = $memberRepository->find($request->query->get('id'));
            $cov = $covRepository->findBy(['driver'=> $driver]);
            $data = $serializer->serialize($cov ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Cov:GET']]);
            return new JsonResponse($data,Response::HTTP_OK,[],true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(),  $e->getCode(), [], true);
        }
    }

    #[Route('/update', name: 'covoi.update', methods: ['PUT'])]
    public function update(CovoiturageRepository $covRepository, SerializerInterface $serializer, Request $request): JsonResponse {
        try {

            $cov = $covRepository->find($request->query->get('id'));
            $data = json_decode($request->getContent(), true);

            $cov->setDestination($data['destination']?? $cov->getDestination());
            $cov->setDeparture($data['departure']?? $cov->getDeparture());
            $cov->setDepartureTime(new \DateTime($data['departure_time']?? $cov->getDepartureTime()));
            $cov->setNumberOfPlaces($data['number_of_places']?? $cov->getNumberOfPlaces());
            $cov->setNumberOfPlacesTaken($data['number_of_places_taken']?? $cov->getNumberOfPlacesTaken());
            $cov->setPrice($data['price']?? $cov->getPrice());
            $cov->setDescription($data['description']?? $cov->getDescription());

            $covRepository->save($cov,true);
            $data = $serializer->serialize($cov ,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => ['Cov:GET']]);
            return new JsonResponse($data,Response::HTTP_OK,[],true);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode(), [], true);
        }
    }

    #[Route('/delete', name: 'covoi.delete', methods: ['DELETE'])]
    public function delete(Request $request, CovoiturageRepository $repository): JsonResponse {
        try {
            $repository->remove($repository->find($request->query->get('id')), true);
            return $this->json("covoiturage deleted successfully",200);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode(), [], true);
        }
    }

}
