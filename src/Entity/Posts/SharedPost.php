<?php

namespace App\Entity\Posts;

use App\Entity\Accounts\Member;
use App\Repository\Posts\SharedPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: SharedPostRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SharedPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["SharedPost" , 'PostNotification:get'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sharedPosts')]
    #[Groups(["SharedPost" ])]
    private ?Member $sharer = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'likedPosts')]
    #[ORM\JoinTable(name: 'shared_post_likes')]
    #[Groups(["SharedPost"])]
    private Collection $likers;



    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups(["SharedPost"])]
    private Collection $comments;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["SharedPost"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Groups(["SharedPost"])]
    private ?bool $isShared = null;

    #[ORM\ManyToOne(inversedBy: 'sharedPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["SharedPost"])]
    private ?Post $post = null;
    private $shares;




    public function __construct()
    {
        $this->likers = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSharer(): ?Member
    {
        return $this->sharer;
    }

    public function setSharer(?Member $sharer): self
    {
        $this->sharer = $sharer;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getLikers(): Collection
    {
        return $this->likers;
    }

    public function addLiker(Member $liker): self
    {
        if (!$this->likers->contains($liker)) {
            $this->likers->add($liker);
        }

        return $this;
    }

    public function removeLiker(Member $liker): self
    {
        $this->likers->removeElement($liker);

        return $this;
    }


    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

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
    public function setDateOnPersist(): void
    {
        $this->date = new \DateTimeImmutable();
    }

    public function isIsShared(): ?bool
    {
        return $this->isShared;
    }

    public function setIsShared(bool $isShared): self
    {
        $this->isShared = $isShared;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }


    #[VirtualProperty]
    #[Groups(["SharedPost"])]
    public function getShares(): int
    {
        return $this->shares;
    }
    public function setShares(int $shares): self
    {
        $this->shares = $shares;

        return $this;
    }



}
