<?php

namespace App\Entity\Notifications;

use App\Entity\Covoiturage\Covoiturage;
use App\Repository\Notifications\CovoiturageNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageNotificationRepository::class)]
class CovoiturageNotification extends Notification
{

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Covoiturage $covoiturage = null;


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
