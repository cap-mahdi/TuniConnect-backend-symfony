<?php

namespace App\Entity\Accounts;

use App\Repository\Accounts\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['friendRequest:get'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'friendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['friendRequest:get'])]

    private ?Member $sender = null;

    #[ORM\ManyToOne(inversedBy: 'friendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['friendRequest:get'])]

    private ?Member $receiver = null;

    #[ORM\Column(length: 20 , options: ['default'=>'pending'])]
    #[Groups(['friendRequest:get'])]

    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Member
    {
        return $this->sender;
    }

    public function setSender(?Member $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?Member
    {
        return $this->receiver;
    }

    public function setReceiver(?Member $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
