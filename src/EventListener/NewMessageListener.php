<?php

namespace App\EventListener;

use App\Entity\Chat\Message;

use App\WebSocket\Chat;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class NewMessageListener implements EventSubscriber
{
    private $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Message) {
            return;
        }
/*        echo"Received message: {$entity->getBody()} \n";*/
/*
        $connectionMessage = [
            'type' => 'new_message',
            'data' => [
                'content' => $entity->getContent(),

            ],
        ];*/
/*        $this->chat->sendToAll(json_encode($connectionMessage));*/
        $this->chat->sendToAll($entity->getBody());

    }
    public function getSubscribedEvents(): array
    {
        return [
            'postPersist',
        ];
    }
}
