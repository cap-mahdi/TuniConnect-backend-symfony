<?php

namespace App\Controller\Accounts;

use App\Entity\Accounts\Address;
use App\Entity\Accounts\User;
use App\Repository\Accounts\AddressRepository;
use App\Repository\Accounts\UserRepository;
use App\Repository\Posts\PersonRepository;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/address')]
class AddressController extends AbstractController
{

    #[Route('/',
        name: 'address.list',
        methods: ['GET']
    )]
    public function getAddresses(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try
        {
            $queries = $request->query->all();
            $repository= $doctrine->getRepository(Address::class);
            $persons = $repository->findBy($queries);
            return $this->json($persons , 200, ["Content-Type" => "application/json"]);
        }catch (Exception $exception)
        {
            return $this->json($exception->getMessage(),400, ["Content-Type" => "application/json"]);
        }
    }


    #[Route("/add",
        name:"address.add",
        methods:["POST"]
    )]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();


        $content = json_decode($request->getContent());
        $address =  new Address();
        $address->setCountry($content->country);
        $address->setState($content->state);
        $address->setCity($content->city);
        $address->setStreet($content->street);
        $address->setZipcode($content->zipcode);
        $entityManager->persist($address);
        $entityManager->flush();

        $id = $address->getId() ;
        $data = ["id"=>$id] ;
        return $this->json($data,201);
    }


    #[Route('/{id}',
        name: 'address.edit',
        requirements: [
            'id'=>'\d+'
        ],
        methods: ['PUT']
    )]
    public function edit(Request $request, ManagerRegistry $doctrine, Address $address = null): JsonResponse
    {
        if(!$address)
        {
            return $this->json("the address does not exist");
        }
        $content = json_decode($request->getContent());
        $address =  new Address();
        $address->setCountry($content->country);
        $address->setState($content->state);
        $address->setCity($content->city);
        $address->setStreet($content->street);
        $address->setZipcode($content->zipcode);
        $manager = $doctrine->getManager();
        $manager->persist($address);
        $manager->flush();
        return $this->json("the address is updated successfully");

    }


    #[Route('/delete/{id}',
        name: 'address.delete',
        requirements: [
            'id'=>'\d+'
        ],
        methods: ['DELETE']
    )]
    public function delete( ManagerRegistry $doctrine ,Address $address = null): JsonResponse
    {
        if(!$address)
        {
            return $this->json("the address does not exist");
        }
        $manager = $doctrine->getManager();
        $manager->remove($address);
        $manager->flush();
        return $this->json("the address is deleted successfully");

    }

}
