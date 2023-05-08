<?php

namespace App\Entity\Posts;

use App\Entity\Accounts\Member;
use App\Repository\Posts\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: PostRepository::class)]
#[HasLifecycleCallbacks]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["SharedPost"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["SharedPost"])]
    private ?string $text = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups(["SharedPost"])]
    private array $photos = [];

    #[ORM\ManyToOne(inversedBy: 'myPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["SharedPost"])]
    private ?Member $owner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["SharedPost"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: SharedPost::class, orphanRemoval: true)]
    private Collection $sharedPosts;

    #[ORM\Column(nullable: false,options: ["default" => false])]
    #[Groups(["SharedPost"])]
    private ?bool $edited = null;



    public function __construct()
    {
        $this->sharedPosts = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function setPhotos(?array $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    public function getOwner(): ?Member
    {
        return $this->owner;
    }

    public function setOwner(?Member $owner): self
    {
        $this->owner = $owner;

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
    public function prePersist(){
        $this->date = new \DateTime();
    }

    /**
     * @return Collection<int, SharedPost>
     */
    public function getSharedPosts(): Collection
    {
        return $this->sharedPosts;
    }

    public function addSharedPost(SharedPost $sharedPost): self
    {
        if (!$this->sharedPosts->contains($sharedPost)) {
            $this->sharedPosts->add($sharedPost);
            $sharedPost->setPost($this);
        }

        return $this;
    }

    public function removeSharedPost(SharedPost $sharedPost): self
    {
        if ($this->sharedPosts->removeElement($sharedPost)) {
            // set the owning side to null (unless already changed)
            if ($sharedPost->getPost() === $this) {
                $sharedPost->setPost(null);
            }
        }

        return $this;
    }

    public function isEdited(): ?bool
    {
        return $this->edited;
    }

    public function setEdited(?bool $edited): self
    {
        $this->edited = $edited;

        return $this;
    }


}
