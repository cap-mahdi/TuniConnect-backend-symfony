<?php

namespace App\Entity\Notifications;

use App\Entity\Posts\Post;
use App\Entity\Posts\SharedPost;
use App\Repository\Notifications\PostNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostNotificationRepository::class)]
class PostNotification extends Notification
{

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SharedPost $post = null;



    public function getPost(): ?SharedPost
    {
        return $this->post;
    }

    public function setPost(?SharedPost $post): self
    {
        $this->post = $post;

        return $this;
    }
}
