<?php
// src/EventListener/MemberListener.php

namespace App\EventListener;

use App\Entity\Accounts\Member;
use App\Service\WebsocketPusher;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MemberListener
{
    private $pusher;

    public function __construct(WebsocketPusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function onPostPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Memberber) {
            $this->pusher->push(json_encode([
                'type' => 'new_member',
                'data' => [
                    'id' => $entity->getId(),
                    'name' => $entity->getName(),
                    // add other properties as needed
                ],
            ]));
        }
    }
}
