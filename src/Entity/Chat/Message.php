<?php

namespace App\Entity\Chat;

use App\Entity\Accounts\Member;
use App\Repository\Chat\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Message:POST")]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups("Message:POST")]
    private ?string $body = null;

    #[ORM\ManyToOne(inversedBy: 'messagesSent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("Message:POST")]
    private ?Member $sender = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'messagesReceived')]
    #[ORM\JoinTable(name: 'messages_receiver')]
    #[Groups("Message:POST")]
    private Collection $receivers;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("Message:POST")]
    private ?\DateTimeInterface $date = null;


    public function __construct()
    {
        $this->receivers = new ArrayCollection();
    }
    public function getId(): ?int{
        return $this->id;
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
    #[ORM\PrePersist]
    public function onPrePersist(){
        $this->date = new \DateTime();
    }



}
