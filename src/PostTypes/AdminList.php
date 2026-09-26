<?php
/**
 * Admin list table tweaks for the plugin's custom post types.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\PostTypes;

use WP_Query;

/**
 * Orders the admin list tables like the front end (menu_order, then date) and
 * adds an "Order" column to every CPT plus "Year" and "Event" columns to talks.
 */
class AdminList {

	/**
	 * Extra columns per post type: post_type => [ column key => meta key ].
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function meta_columns(): array {
		return array(
			'pd_talk' => array(
				'pd_talk_year'  => '_pd_talk_year',
				'pd_talk_event' => '_pd_talk_event',
			),
		);
	}

	/**
	 * Hook the query ordering and the column callbacks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'pre_get_posts', array( $this, 'order' ) );

		foreach ( array_keys( PostTypes::definitions() ) as $post_type ) {
			add_filter(
				"manage_{$post_type}_posts_columns",
				fn ( array $columns ): array => $this->columns( $columns, $post_type )
			);
			add_filter( "manage_edit-{$post_type}_sortable_columns", array( $this, 'sortable_columns' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'render_column' ), 10, 2 );
		}
	}

	/**
	 * Default the admin list order to menu_order ASC, date ASC, like the blocks.
	 * An explicit ?orderby= (clicking a column header) still wins.
	 *
	 * @param WP_Query $query Current query.
	 *
	 * @return void
	 */
	public function order( WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		if ( ! is_string( $post_type ) || ! array_key_exists( $post_type, PostTypes::definitions() ) ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( '' === $orderby || null === $orderby ) {
			$query->set(
				'orderby',
				array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				)
			);
		}
	}

	/**
	 * Insert the "Order" column (and any meta columns) after the title.
	 *
	 * @param array<string, string> $columns   Existing columns.
	 * @param string                $post_type Post type of the list table.
	 *
	 * @return array<string, string>
	 */
	public function columns( array $columns, string $post_type ): array {
		$extra = array();
		foreach ( self::meta_columns()[ $post_type ] ?? array() as $column => $meta_key ) {
			$label            = PostTypes::meta_fields()[ $post_type ][ $meta_key ]['label'] ?? $column;
			$extra[ $column ] = $label;
		}
		$extra['pd_order'] = __( 'Order', 'ploetner-dev-blocks' );

		$result = array();
		foreach ( $columns as $key => $label ) {
			$result[ $key ] = $label;
			if ( 'title' === $key ) {
				$result += $extra;
			}
		}

		// No title column (unlikely): append at the end.
		return $result + $extra;
	}

	/**
	 * Make the "Order" column sortable.
	 *
	 * @param array<string, string|array<int, mixed>> $columns Sortable columns.
	 *
	 * @return array<string, string|array<int, mixed>>
	 */
	public function sortable_columns( array $columns ): array {
		$columns['pd_order'] = array( 'menu_order', false );

		return $columns;
	}

	/**
	 * Output a custom column cell.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 */
	public function render_column( string $column, int $post_id ): void {
		if ( 'pd_order' === $column ) {
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
			return;
		}

		foreach ( self::meta_columns() as $columns ) {
			if ( isset( $columns[ $column ] ) ) {
				echo esc_html( (string) get_post_meta( $post_id, $columns[ $column ], true ) );
				return;
			}
		}
	}
}
