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
	 * URL and cache-busting version of a file shipped with the plugin.
	 *
	 * @param string $path Path relative to the plugin root (e.g. "assets/blocks.css").
	 *
	 * @return array{0: string, 1: string|false} URL and filemtime version (false when unreadable).
	 */
	public static function asset( string $path ): array {
		$root = dirname( __DIR__ );
		$file = $root . '/' . $path;

		return array(
			plugins_url( $path, $root . '/ploetner-dev-blocks.php' ),
			is_readable( $file ) ? (string) filemtime( $file ) : false,
		);
	}

	/**
	 * Enqueue the block stylesheet.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		list( $url, $ver ) = self::asset( 'assets/blocks.css' );

		wp_enqueue_style( self::HANDLE, $url, array(), $ver );
	}
}
