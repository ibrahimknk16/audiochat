<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\Server as ReactServer;

class Socketstart2 extends CI_Controller
{
    public function index()
    {
        $loop = EventLoopFactory::create();
        $socket = new ReactServer('0.0.0.0:3000', $loop);

        $server = new \Ratchet\Server\IoServer(
            new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer(
                    new SignalServer()
                )
            ),
            $socket,
            $loop  // Event loop'u ekleyin
        );

		echo 'WebSocket sunucusu başlatıldı.';
        $server->run();
    }
}

class SignalServer implements MessageComponentInterface
{
	protected $clients;

	public function __construct()
	{
		$this->clients = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $conn)
	{
		$this->clients->attach($conn);
		echo "Yeni bağlantı: {$conn->resourceId}\n";
	}

	public function onMessage(ConnectionInterface $from, $msg)
	{
		// Gelen mesajı diğer tüm bağlantılara iletiyoruz
		foreach ($this->clients as $client) {
			if ($client !== $from) {
				$client->send($msg);
			}
		}
	}

	public function onClose(ConnectionInterface $conn)
	{
		$this->clients->detach($conn);
		echo "Bağlantı kapandı: {$conn->resourceId}\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		echo "Hata: {$e->getMessage()}\n";
		$conn->close();
	}
}
