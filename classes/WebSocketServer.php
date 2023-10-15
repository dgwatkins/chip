<?php

namespace Chip;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

/**
 * Web Socker Server
 *
 * @package Chip
 */
class WebSocketServer implements MessageComponentInterface {

	/**
	 * @var SplObjectStorage $clients
	 */
	protected $clients;

	public function __construct() {
		$this->clients = new \SplObjectStorage();
	}

	/**
	 * @return void
	 */
	public function run() {
		$server = \Ratchet\Server\IoServer::factory(
			new \Ratchet\Http\HttpServer(
				new \Ratchet\WebSocket\WsServer(
					$this
				)
			),
			8082
		);
		$server->run();
	}

	/**
	 * @param ConnectionInterface $conn
	 *
	 * @return void
	 */
	public function onOpen( ConnectionInterface $conn ) {
		$this->clients->attach( $conn );
	}

	/**
	 * @param ConnectionInterface $from
	 * @param string              $msg
	 *
	 * @return void
	 */
	public function onMessage( ConnectionInterface $from, $msg ) {
		foreach ( $this->clients as $client ) {
			if ( $from !== $client ) {
				$client->send( $msg );
			}
		}
	}

	/**
	 * @param ConnectionInterface $conn
	 *
	 * @return void
	 */
	public function onClose( ConnectionInterface $conn ) {
		$this->clients->detach( $conn );
	}

	/**
	 * @param ConnectionInterface $conn
	 * @param \Exception          $e
	 *
	 * @return void
	 */
	public function onError( ConnectionInterface $conn, \Exception $e ) {
		$conn->close();
	}
}
