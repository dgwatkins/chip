<?php

namespace Chip;

/**
 * Web Socket Controller
 *
 * @package Chip
 */
class WebSocketController {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * @return void
	 */
	public function register_rest_routes() {
		register_rest_route(
			'chip/v1',
			'/server',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'start_websocket_server' ),
				'permission_callback' => is_user_logged_in(),
			)
		);
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function start_websocket_server() {
		$host = 'shop.loc';
		$port = 8082;

		if ( $this->is_port_in_use( $host, $port ) ) {
			return new \WP_REST_Response( 'WebSocket server is already running', 200 );
		}

		$pid = pcntl_fork();
		if ( -1 === $pid ) {
			return new \WP_REST_Response( 'Could not fork process', 500 );
		} elseif ( $pid ) {
			return new \WP_REST_Response( 'WebSocket server started', 200 );
		} else {
			$websocket_server = new WebSocketServer();
			$websocket_server->run();
			exit();
		}
	}

	/**
	 * @param string $host
	 * @param int    $port
	 *
	 * @return bool
	 */
	private function is_port_in_use( $host, $port ) {
		/* phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged */
		$socket = @socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
		if ( ! $socket ) {
			return false;
		}

		$result = socket_connect( $socket, $host, $port );
		socket_close( $socket );

		return $result;
	}
}
