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
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Address::class);

        $addresses = $repository->findAll();

        $data = [];

        foreach ($addresses as $address) {
            $data[] = [
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'zipcode' => $address->getZipcode(),
                'country' => $address->getCountry()
            ];
        }

        return $this->json($data);
    }

    #[Route('/{id}',
        name: 'address.details',
        requirements: [
            'id'=>'\d+'
        ],
        methods: ['GET']
    )]
    public function details(ManagerRegistry $doctrine, Request $request , $id): JsonResponse
    {
        $repository = $doctrine->getRepository(Address::class);
        $address = $repository->find($id);
        if($address)
        {
            $data[] = [
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'zipcode' => $address->getZipcode(),
                'country' => $address->getCountry()
            ];
            return $this->json($data);
        }
        else
        {
            return $this->json("the address does not exist");
        }
    }


    #[Route("/add",
        name:"address.add",
        methods:["POST"]
    )]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $address =  new Address();
        $address->setCountry($request->request->get('country'));
        $address->setState($request->request->get('state'));
        $address->setCity($request->request->get('city'));
        $address->setStreet($request->request->get('street'));
        $address->setZipcode($request->request->get('zipcode'));
        $entityManager->persist($address);
        $entityManager->flush();

        return $this->json('Created new project successfully with id ' . $address->getId());
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
        $address->setCountry($request->request->get('country'));
        $address->setState($request->request->get('state'));
        $address->setCity($request->request->get('city'));
        $address->setStreet($request->request->get('street'));
        $address->setZipcode($request->request->get('zipcode'));
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
    public function delete(Address $address = null, ManagerRegistry $doctrine): JsonResponse
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
