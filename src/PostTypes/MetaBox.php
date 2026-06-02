<?php
/**
 * "Details" meta box for the plugin's custom post types.
 *
 * Classic meta boxes still render below the block editor canvas, so this needs
 * no JavaScript or build step.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\PostTypes;

use WP_Post;

/**
 * Renders and persists the per-post-type meta fields defined in PostTypes.
 */
class MetaBox {

	private const NONCE_ACTION = 'pd_meta_save';

	private const NONCE_NAME = 'pd_meta_nonce';

	/**
	 * Hook the meta box registration and save handler.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Register a "Details" meta box for every post type that has meta fields.
	 *
	 * @return void
	 */
	public function add_meta_boxes(): void {
		foreach ( array_keys( PostTypes::meta_fields() ) as $post_type ) {
			add_meta_box(
				'pd_details',
				__( 'Details', 'ploetner-dev-blocks' ),
				array( $this, 'render' ),
				$post_type,
				'side'
			);
		}
	}

	/**
	 * Render the meta box fields for the given post.
	 *
	 * @param WP_Post $post Current post.
	 *
	 * @return void
	 */
	public function render( WP_Post $post ): void {
		$fields = PostTypes::meta_fields()[ $post->post_type ] ?? array();
		if ( ! $fields ) {
			return;
		}

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		foreach ( $fields as $key => $field ) {
			$value = (string) get_post_meta( $post->ID, $key, true );
			printf(
				'<p><label for="%1$s"><strong>%2$s</strong></label><br />',
				esc_attr( $key ),
				esc_html( $field['label'] )
			);
			printf(
				'<input type="%4$s" id="%1$s" name="%1$s" value="%2$s" class="widefat" placeholder="%3$s" />',
				esc_attr( $key ),
				esc_attr( $value ),
				esc_attr( $field['placeholder'] ?? '' ),
				esc_attr( $field['input'] ?? 'text' )
			);
			if ( ! empty( $field['description'] ) ) {
				printf( '<br /><span class="description">%s</span>', esc_html( $field['description'] ) );
			}
			echo '</p>';
		}
	}

	/**
	 * Persist meta box values for any of our post types.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 *
	 * @return void
	 */
	public function save( int $post_id, WP_Post $post ): void {
		$fields = PostTypes::meta_fields()[ $post->post_type ] ?? array();
		if ( ! $fields ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if (
			! isset( $_POST[ self::NONCE_NAME ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( $fields as $key => $field ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$sanitize = $field['sanitize'] ?? 'sanitize_text_field';
			if ( ! is_callable( $sanitize ) ) {
				continue;
			}
			// Value is sanitized by the configured callback above.
			update_post_meta( $post_id, $key, call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
