<?php

// src/WebSocket/Chat.php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Chat implements MessageComponentInterface
{
public $connections  = [];
public $clients;
private $httpKernel ;
public function __construct(HttpKernelInterface $httpKernel)
{
$this->clients = new \SplObjectStorage();
    $this->httpKernel = $httpKernel;
}


public function onOpen(ConnectionInterface $conn )
{

$this->clients->attach($conn);

echo "New connection! ({$conn->resourceId}) \n";
}

public function onMessage(ConnectionInterface $from, $msg)
{
        $payload = json_decode($msg, true);
        if (isset($payload["userId"])) {
            $userId = $payload["userId"];
            if (!isset($this->connections[$userId])) {
                $this->connections[$userId] = $from;
            }

        }

    $content = json_decode($msg , true);
    $content = json_encode($content);

       if (isset($payload["senderId"])) {
           $senderId = $payload["senderId"];
           $body = $payload["body"];
           $roomId = $payload["roomId"];

// Ensure that all strings are encoded in UTF-8 format
       /*    $senderId = mb_convert_encoding($senderId, "UTF-8", mb_detect_encoding($senderId));
           $body = mb_convert_encoding($body, "UTF-8", mb_detect_encoding($body));
           $roomId = mb_convert_encoding($roomId, "UTF-8", mb_detect_encoding($roomId));*/

           $content = [
               "senderId" => $senderId,
               "body" => $body,
               "roomId" => $roomId
           ];

           $content = json_encode($content);

           $subRequestRoomMes = Request::create("/room/addMessage", 'POST', [], [], [], [], $content);
           $responseRoomMesJSON = $this->httpKernel->handle($subRequestRoomMes, HttpKernelInterface::SUB_REQUEST);

           echo"Received message : {$from->resourceId} \n";
            $path = "/room/getRoomMembers/". $roomId;
           echo"Received message : {$msg} {$path}\n";

           $subRequestRoomMes = Request::create($path, 'GET', [], [], [], []);
           $responseRoomMesJSON = $this->httpKernel->handle($subRequestRoomMes, HttpKernelInterface::SUB_REQUEST);

           $members = json_decode($responseRoomMesJSON->getContent(), true);

           dump($members)  ;

           foreach ($this->clients as $client) {


            for ( $i = 0  ; $i < count($members) ; $i++) {
                if(isset($this->connections[$members[$i]["id"]])){
                     if ($this->connections[$members[$i]["id"]] == $client) {
                          $client->send($msg);

                     }
                }



           }



        }






}}

public function onClose(ConnectionInterface $conn)
{
$this->clients->detach($conn);
echo "Connection {$conn->resourceId} has disconnected\n";
}

public function onError(ConnectionInterface $conn, \Exception $e)
{
echo "An error has occurred: {$e->getMessage()}\n";
$conn->close();
}
    public function sendToAll($msg)
    {
        foreach ($this->clients as $client) {

            $client->send($msg);
        }
    }
}
