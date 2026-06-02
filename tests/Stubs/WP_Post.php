<?php
/**
 * Minimal global WP_Post double for unit tests.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

if ( ! class_exists( 'WP_Post' ) ) {
	/**
	 * Minimal WP_Post double exposing the properties the plugin reads.
	 */
	class WP_Post {

		public int $ID = 0;

		public string $post_title = '';

		public string $post_content = '';

		public string $post_type = '';

		/**
		 * Construct from an associative array of properties.
		 *
		 * @param array<string, mixed> $props Property values.
		 */
		public function __construct( array $props = array() ) {
			foreach ( $props as $key => $value ) {
				if ( property_exists( $this, $key ) ) {
					$this->$key = $value;
				}
			}
		}
	}
}
