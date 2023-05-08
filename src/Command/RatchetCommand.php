<?php
namespace App\Command;

use App\EventListener\NewMessageListener;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RatchetCommand extends Command
{
    protected static $defaultName = 'app:ratchet';
    private ManagerRegistry  $doctrine ;
    private $httpKernel ;

    public function __construct( ManagerRegistry $doctrine , HttpKernelInterface $httpKernel)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
        $this->httpKernel = $httpKernel;
    }

    protected function execute(InputInterface $input, OutputInterface $output  )
    {
        $chat = new Chat($this->httpKernel);

        $wsServer = new WsServer($chat);

        $httpServer = new HttpServer($wsServer);

        $server = IoServer::factory(
            $httpServer,
            8080
        );

        $server->loop->addPeriodicTimer(1, function() use ($output) {
/*            $output->writeln('Ratchet server running...');*/
        });

        $server->loop->addPeriodicTimer(60, function() use ($chat) {
            $connections = array();
            foreach ($chat->clients as $client) {
                $connections[] = $client;
            }
        });

        $entityManager = $this->doctrine->getManager();
      /*  $newMessageListener = new NewMessageListener($chat);

        $entityManager->getEventManager()->addEventListener(
            ['postPersist'],
            $newMessageListener
        );*/

        $server->run();

        return Command::SUCCESS;
    }
}
