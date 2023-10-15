<?php
/**
 * Plugin Name: Chip
 * Version: 1.0.0-alpha
 * License: GPL v2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chip
 * Domain Path: /languages
 *
 * @package Chip
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

define( 'CHIP_VERSION', '1.0.0' );
define( 'CHIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CHIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action(
	'plugins_loaded',
	function () {
		new Chip\WebSocketController();
		( new Chip\Main() )->run();
	}
);
