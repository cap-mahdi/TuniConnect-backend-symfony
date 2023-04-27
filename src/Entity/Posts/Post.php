<?php

namespace App\Entity\Posts;

use App\Entity\Accounts\Member;
use App\Repository\Posts\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column]
    private ?int $totalLikes = null;

    #[ORM\Column]
    private ?int $totalShares = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $poster = null;



    #[ORM\ManyToMany(targetEntity: Member::class)]
    #[ORM\JoinTable(name: 'post_likes')]
    private Collection $memberLikes;



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;



    public function __construct()
    {
        $this->memberLikes = new ArrayCollection();

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

    public function getTotalLikes(): ?int
    {
        return $this->totalLikes;
    }

    public function setTotalLikes(int $totalLikes): self
    {
        $this->totalLikes = $totalLikes;

        return $this;
    }

    public function getTotalShares(): ?int
    {
        return $this->totalShares;
    }

    public function setTotalShares(int $totalShares): self
    {
        $this->totalShares = $totalShares;

        return $this;
    }

    public function getPoster(): ?Member
    {
        return $this->poster;
    }

    public function setPoster(?Member $poster): self
    {
        $this->poster = $poster;

        return $this;
    }



    /**
     * @return Collection<int, Member>
     */
    public function getMemberLikes(): Collection
    {
        return $this->memberLikes;
    }

    public function addMemberLike(Member $memberLike): self
    {
        if (!$this->memberLikes->contains($memberLike)) {
            $this->memberLikes->add($memberLike);
        }

        return $this;
    }

    public function removeMemberLike(Member $memberLike): self
    {
        $this->memberLikes->removeElement($memberLike);

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
