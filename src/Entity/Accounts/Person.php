<?php

namespace App\Entity\Accounts;

use App\Repository\Posts\PersonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Accounts\User;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name:"person_type",type: "string")]
#[ORM\DiscriminatorMap(["person"=>Person::class,"member"=>Member::class])]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["Member:Post" , 'friendRequest:get' ,'PostNotification:get', "Member:Get","Message:POST","Post:Get", "Post:Post","SharedPost","Comment:GetAll" ,"Message:GET",'Room:CREATE'  , "RoomMember:GET","member:friend" , "Cov:GET" , 'member', 'ReqCov: POST'])]

    protected ?int $id = null;



    #[ORM\Column(nullable: true)]
    #[Groups(["member", "Cov:GET" , 'PostNotification:get', 'ReqCov: POST'])]

    protected ?int $phone = null;


    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["Member:Post" , "Member:Get","member" , 'PostNotification:get' , 'friendRequest:get'])]

    protected ?Address $address = null;
    #[Groups(["member", 'PostNotification:get' , 'friendRequest:get'])]

    #[ORM\OneToOne(inversedBy: 'person', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]

    protected ?User $user = null;


    #[ORM\Column(length: 50)]
    #[Groups(["SharedPost","Comment:GetAll" , "member","member:friend",'PostNotification:get' , 'friendRequest:get','Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    protected ?string $firstName = null;

    #[ORM\Column(length: 50)]
    #[Groups(["SharedPost","Comment:GetAll" , "member","member:friend", 'PostNotification:get' , 'friendRequest:get', 'Cov:GET', 'ReqCov: POST'])]

    protected ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["member", 'PostNotification:get' , 'friendRequest:get'])]
    protected ?\DateTimeInterface $birthday = null;

    #[ORM\Column(length: 10)]
    #[Groups(["member" ,  'PostNotification:get' , 'friendRequest:get'])]
    protected ?string $gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["SharedPost","Comment:GetAll" , "member","member:friend",'friendRequest:get','Cov:GET', 'ReqCov: POST'])]

    protected ?string $profilePicture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("member" , 'friendRequest:get')]
    protected ?string $coverPicture = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getCoverPicture(): ?string
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(?string $coverPicture): self
    {
        $this->coverPicture = $coverPicture;

        return $this;
    }



    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }


    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
