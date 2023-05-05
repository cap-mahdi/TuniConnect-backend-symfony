<?php

namespace App\Entity\Posts;

use App\Entity\Accounts\Member;
use App\Repository\Posts\SharedPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SharedPostRepository::class)]
class SharedPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sharedPosts')]
    private ?Member $sharer = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'likedPosts')]
    private Collection $likers;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

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
}
