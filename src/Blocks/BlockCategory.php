<?php
/**
 * Block-editor category registration.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

/**
 * Registers the dedicated "Plötner.dev" block category.
 */
class BlockCategory {

	/**
	 * Hook into the block category filter.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'block_categories_all', array( $this, 'add_category' ) );
	}

	/**
	 * Prepend a dedicated block-editor category.
	 *
	 * This is separate from the pattern category of the same slug registered in
	 * functions.php — patterns and blocks use independent category systems.
	 *
	 * @param array<int, array<string, mixed>> $categories Existing categories.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function add_category( array $categories ): array {
		array_unshift(
			$categories,
			array(
				'slug'  => 'ploetner-dev',
				'title' => __( 'Plötner.dev', 'ploetner-dev-blocks' ),
				'icon'  => null,
			)
		);

		return $categories;
	}
}
