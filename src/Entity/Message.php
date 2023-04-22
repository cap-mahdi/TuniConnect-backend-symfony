<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\ManyToOne(inversedBy: 'messagesSent')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $sender = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'messagesReceived')]
    #[ORM\JoinTable(name: 'messages_receiver')]
    private Collection $receivers;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->receivers = new ArrayCollection();
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
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

    /**
     * @return Collection<int, Member>
     */
    public function getReceivers(): Collection
    {
        return $this->receivers;
    }

    public function addReceiver(Member $receiver): self
    {
        if (!$this->receivers->contains($receiver)) {
            $this->receivers->add($receiver);
        }

        return $this;
    }

    public function removeReceiver(Member $receiver): self
    {
        $this->receivers->removeElement($receiver);

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }


}
