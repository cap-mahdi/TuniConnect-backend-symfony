<?php

namespace App\Entity\Posts;

use App\Entity\Accounts\Member;
use App\Repository\Posts\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["SharedPost",'Comment:GetAll'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["SharedPost",'Comment:GetAll'])]
    private ?string $text = null;

    #[ORM\Column]
    #[Groups(["SharedPost",'Comment:GetAll'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SharedPost $post = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["SharedPost",'Comment:GetAll'])]
    private ?Member $commenter = null;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPost(): ?SharedPost
    {
        return $this->post;
    }

    public function setPost(?SharedPost $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getCommenter(): ?Member
    {
        return $this->commenter;
    }

    public function setCommenter(?Member $commenter): self
    {
        $this->commenter = $commenter;

        return $this;
    }
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
