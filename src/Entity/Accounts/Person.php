<?php

namespace App\Entity\Accounts;

use App\Repository\Posts\PersonRepository;
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
    #[Groups(["member", "Post:Get", "Post:Post" ])]
    protected ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups("member")]

    protected ?string $name = null;


    #[ORM\Column]
    #[Groups("member")]

    protected ?int $phone = null;


    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("member")]

    protected ?Address $address = null;


    #[ORM\OneToOne(inversedBy: 'person', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("member")]

    protected ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
