<?php
/**
 * Front-end and editor styles for the plugin's blocks.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * Enqueues the plugin's own block stylesheet, so its blocks are styled on any
 * theme that provides the design-system tokens (colors, spacing, fonts).
 */
class Assets {

	/**
	 * Stylesheet handle.
	 */
	public const HANDLE = 'ploetner-dev-blocks';

	/**
	 * Hook the stylesheet into the front end and the editor.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'enqueue_block_assets', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the block stylesheet.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		$main = dirname( __DIR__ ) . '/ploetner-dev-blocks.php';
		$file = dirname( __DIR__ ) . '/assets/blocks.css';
		$ver  = is_readable( $file ) ? (string) filemtime( $file ) : false;

		wp_enqueue_style(
			self::HANDLE,
			plugins_url( 'assets/blocks.css', $main ),
			array(),
			$ver
		);
	}
}
