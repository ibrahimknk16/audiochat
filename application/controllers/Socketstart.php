<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;
use React\Socket\Server;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Socketstart extends CI_Controller
{
	public function index()
	{
		$loop = Factory::create();
		$webSock = new Server('0.0.0.0:8080', $loop);
		$webServer = new \Ratchet\Server\IoServer(
			new HttpServer(
				new WsServer(
					new MyChatServer()
				)
			),
			$webSock
		);

		echo "WebSocket sunucusu başlatıldı.\n";

		$loop->run();
	}
}

class MyChatServer implements MessageComponentInterface
{
	protected $clients = [];
	protected $rooms = [];
	protected $users = [];

	protected $CI;

	public function __construct()
	{
		$this->CI = &get_instance();
	}

	public function onOpen(ConnectionInterface $conn)
	{
		$this->clients[$conn->resourceId] = $conn;
		echo "Yeni bağlantı: {$conn->resourceId}\n";
	}

	public function jsonEncode($array)
	{
		return json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	public function onMessage(ConnectionInterface $clnt, $dta)
	{
		$data = json_decode($dta);
		echo $data->type . "\n";

		@$clnt->room = $data->room;
		@$clnt->userID = $data->userID;
		@$message = $data->message;

		if ($data->type === 'audioStream') {
			@$audioData = $data->audioData;
			echo @$audioData;
			echo "Kullanıcı {$clnt->userID} {$data->room} odasına ses verisi gönderdi.\n";
			// Ses verisini diğer kullanıcılara iletmek için işlemleri gerçekleştir
			foreach ($this->clients as $client) {
				if ($client !== $clnt && isset($clnt->room) && $clnt->room === $client->room) {
					// Ses verisini diğer kullanıcılara gönder
					$client->send($audioData);
				}
			}
		} else if ($data->type === 'joinRoom') {
			$this->rooms[$data->room]['users'][$clnt->resourceId]['userID'] = $clnt->userID;
			$this->rooms[$data->room]['users'][$clnt->resourceId]['resourceId'] = $clnt->resourceId;
			echo "Kullanıcı {$clnt->userID} {$data->room} odasına katıldı.\n";

			foreach ($this->clients as $client) {
				if ($client !== $clnt && isset($clnt->room) && $clnt->room === $client->room) {
					$sendData = array(
						'message' => $clnt->userID . ' odaya katıldı.',
						'type' => 'joinRoom',
						'user' => 'Sistem'
					);
					$client->send($this->jsonEncode($sendData));
				}
			}
		} elseif ($data->type === 'chatMessage') {
			echo "Kullanıcı {$clnt->userID} {$data->room} odasına {$message} mesajını gönderdi.\n";
			foreach ($this->clients as $client) {
				if ($client !== $clnt && isset($clnt->room) && $clnt->room === $client->room) {
					$sendData = array(
						'message' => $message,
						'type' => 'chatMessage',
						'user' => $clnt->userID
					);
					$client->send($this->jsonEncode($sendData));
				}
			}
		} elseif ($data->type === 'leaveRoom') {
			echo "Kullanıcı {$clnt->userID} {$data->room} odasından ayrıldı.\n";
			foreach ($this->clients as $client) {
				if ($client !== $clnt && isset($clnt->room) && $clnt->room === $client->room) {
					$sendData = array(
						'message' => $clnt->userID . ' odadan ayrıldı.',
						'type' => 'leaveRoom',
						'user' => 'Sistem'
					);
					$client->send($this->jsonEncode($sendData));
				}
			}
		}
		/*elseif ($data->type == 'typing') {
            echo "{$clnt->userID} {$data->room} odasında yazıyor.\n";
            foreach ($this->clients as $client) {
                if ($client !== $clnt && isset($clnt->room) && $clnt->room === $client->room) {
                    $data = array(
                        'message' => $clnt->userID . ' yazıyor.',
                        'user' => 'Sistem'
                    );
                    //socket broadcast
                    $client->send($this->jsonEncode($data));
                }
            }
        }*/
	}

	public function onClose(ConnectionInterface $conn)
	{
		unset($this->clients[$conn->resourceId]);
		echo "Bağlantı kapandı: {$conn->resourceId}\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		echo "Hata: {$e->getMessage()}\n";
		$conn->close();
	}
}
