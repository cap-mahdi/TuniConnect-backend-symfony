<?php

namespace App\Entity\Accounts;

use App\Entity\Chat\Message;
use App\Entity\Posts\Post;
use App\Entity\Posts\PostShare;
use App\Repository\Accounts\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: '`member`')]
class Member extends Person
{
    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $messagesSent;

    #[ORM\ManyToMany(targetEntity: Message::class, mappedBy: 'receivers')]
    private Collection $messagesReceived;

    #[ORM\OneToMany(mappedBy: 'poster',targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: PostShare::class)]
    private Collection $sharedPost;






    public function __construct()
    {
        $this->messagesSent = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->sharedPost = new ArrayCollection();
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSent(): Collection
    {
        return $this->messagesSent;
    }

    public function addMessagesSent(Message $messagesSent): self
    {
        if (!$this->messagesSent->contains($messagesSent)) {
            $this->messagesSent->add($messagesSent);
            $messagesSent->setSender($this);
        }

        return $this;
    }

    public function removeMessagesSent(Message $messagesSent): self
    {
        if ($this->messagesSent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getSender() === $this) {
                $messagesSent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    public function addMessagesReceived(Message $messagesReceived): self
    {
        if (!$this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived->add($messagesReceived);
            $messagesReceived->addReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceived(Message $messagesReceived): self
    {
        if ($this->messagesReceived->removeElement($messagesReceived)) {
            $messagesReceived->removeReceiver($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setPoster($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getPoster() === $this) {
                $post->setPoster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PostShare>
     */
    public function getSharedPost(): Collection
    {
        return $this->sharedPost;
    }

    public function addSharedPost(PostShare $sharedPost): self
    {
        if (!$this->sharedPost->contains($sharedPost)) {
            $this->sharedPost->add($sharedPost);
            $sharedPost->setMember($this);
        }

        return $this;
    }

    public function removeSharedPost(PostShare $sharedPost): self
    {
        if ($this->sharedPost->removeElement($sharedPost)) {
            // set the owning side to null (unless already changed)
            if ($sharedPost->getMember() === $this) {
                $sharedPost->setMember(null);
            }
        }

        return $this;
    }





}
