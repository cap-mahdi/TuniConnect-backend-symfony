<?php

namespace App\Entity\Covoiturage;

use App\Entity\Accounts\Member;
use App\Repository\Covoiturage\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST' ])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?string $destination = null;

    #[ORM\Column(length: 50)]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?string $departure = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?\DateTimeInterface $departureTime = null;

    #[ORM\Column]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?int $numberOfPlaces = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['Cov:GET', 'ReqCov: POST'])]
    private ?int $numberOfPlacesTaken = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?int $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Cov:POST', 'Cov:GET', 'ReqCov: POST'])]
    private ?Member $driver = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'covoituragesTaken')]
    #[Groups(['Cov:GET', 'ReqCov: POST'])]
    private Collection $passengers;

    #[ORM\OneToMany(mappedBy: 'covoiturage', targetEntity: RequestCovoiturage::class)]
    private Collection $requestCovoiturages;

    public function __construct()
    {
        $this->passengers = new ArrayCollection();
        $this->requestCovoiturages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDeparture(): ?string
    {
        return $this->departure;
    }

    public function setDeparture(string $departure): self
    {
        $this->departure = $departure;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTimeInterface $departureTime): self
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getNumberOfPlaces(): ?int
    {
        return $this->numberOfPlaces;
    }

    public function setNumberOfPlaces(int $numberOfPlaces): self
    {
        $this->numberOfPlaces = $numberOfPlaces;

        return $this;
    }

    public function getNumberOfPlacesTaken(): ?int
    {
        return $this->numberOfPlacesTaken;
    }

    public function setNumberOfPlacesTaken(int $numberOfPlacesTaken): self
    {
        $this->numberOfPlacesTaken = $numberOfPlacesTaken;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDriver(): ?Member
    {
        return $this->driver;
    }

    public function setDriver(?Member $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getPassengers(): Collection
    {
        return $this->passengers;
    }

    public function addPassenger(Member $passenger): self
    {
        if (!$this->passengers->contains($passenger)) {
            $this->passengers->add($passenger);
        }

        return $this;
    }

    public function removePassenger(Member $passenger): self
    {
        $this->passengers->removeElement($passenger);

        return $this;
    }

    /**
     * @return Collection<int, RequestCovoiturage>
     */
    public function getRequestCovoiturages(): Collection
    {
        return $this->requestCovoiturages;
    }

    public function addRequestCovoiturage(RequestCovoiturage $requestCovoiturage): self
    {
        if (!$this->requestCovoiturages->contains($requestCovoiturage)) {
            $this->requestCovoiturages->add($requestCovoiturage);
            $requestCovoiturage->setCovoiturage($this);
        }

        return $this;
    }

    public function removeRequestCovoiturage(RequestCovoiturage $requestCovoiturage): self
    {
        if ($this->requestCovoiturages->removeElement($requestCovoiturage)) {
            // set the owning side to null (unless already changed)
            if ($requestCovoiturage->getCovoiturage() === $this) {
                $requestCovoiturage->setCovoiturage(null);
            }
        }

        return $this;
    }
}
