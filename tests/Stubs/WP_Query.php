<?php
/**
 * Minimal global WP_Query double for unit tests.
 *
 * Tests set the posts the next instance returns and inspect the captured
 * constructor arguments.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

if ( ! class_exists( 'WP_Query' ) ) {
	/**
	 * Minimal WP_Query double.
	 */
	class WP_Query {

		/**
		 * Posts handed to the next constructed instance.
		 *
		 * @var array<int, WP_Post>
		 */
		public static array $next_posts = array();

		/**
		 * Arguments captured from every constructed instance.
		 *
		 * @var array<int, array<string, mixed>>
		 */
		public static array $captured_args = array();

		/**
		 * Resolved posts for this instance.
		 *
		 * @var array<int, WP_Post>
		 */
		public array $posts = array();

		/**
		 * Capture the query arguments and resolve the queued posts.
		 *
		 * @param array<string, mixed> $args Query arguments.
		 */
		public function __construct( array $args = array() ) {
			self::$captured_args[] = $args;
			$this->posts           = self::$next_posts;
		}

		/**
		 * Reset the static fixtures between tests.
		 *
		 * @return void
		 */
		public static function reset(): void {
			self::$next_posts    = array();
			self::$captured_args = array();
		}
	}
}
