<?php

namespace App\Entity\Notifications;

use App\Entity\Posts\Post;
use App\Repository\Notifications\PostNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostNotificationRepository::class)]
class PostNotification extends Notification
{

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;



    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
