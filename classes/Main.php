<?php

namespace Chip;

/**
 * Main entry point.
 *
 * @packate Chip
 */
class Main {

	/**
	 * @return void
	 */
	public function run() {
		if ( is_user_logged_in() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'add_root' ) );
		}
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script(
			'chio-app',
			plugins_url( 'dist/bundle.js', CHIP_PLUGIN_DIR . 'chip.php' ),
			array(),
			CHIP_VERSION,
			true
		);
		$user = wp_get_current_user();
		wp_localize_script(
			'chip-app',
			'ChipData',
			array(
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'user'  => $user->user_login,
			)
		);
		wp_enqueue_script( 'chip-app' );
	}

	/**
	 * @return void
	 */
	public function add_root() {
		echo '<div id="root"></div>';
	}
}
