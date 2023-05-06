<?php

namespace App\Entity\Covoiturage;

use App\Entity\Accounts\Member;
use App\Repository\Covoiturage\RequestCovoiturageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RequestCovoiturageRepository::class)]
class RequestCovoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ReqCov: POST'])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(['ReqCov: POST'])]
    private ?string $status = 'pending';

    #[ORM\ManyToOne(inversedBy: 'requestCovoiturageSent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ReqCov: POST'])]
    private ?Member $sender = null;

    #[ORM\ManyToOne(inversedBy: 'requestCovoiturages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ReqCov: POST'])]
    private ?Covoiturage $covoiturage = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSender(): ?Member
    {
        return $this->sender;
    }

    public function setSender(?Member $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getCovoiturage(): ?Covoiturage
    {
        return $this->covoiturage;
    }

    public function setCovoiturage(?Covoiturage $covoiturage): self
    {
        $this->covoiturage = $covoiturage;

        return $this;
    }
}
