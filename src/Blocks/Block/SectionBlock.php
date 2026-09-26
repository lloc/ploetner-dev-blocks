<?php
/**
 * Abstract base for CPT-backed section blocks.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

use WP_Post;

/**
 * A section block whose items come from a custom post type.
 */
abstract class SectionBlock extends Block {

	/**
	 * Backing post type slug. Each section block overrides this.
	 */
	public const POST_TYPE = '';

	/**
	 * Heading anchor / id used by the section wrapper. Each section block overrides this.
	 */
	public const ANCHOR = '';

	/**
	 * Backing post type slug.
	 *
	 * @return string
	 */
	protected function post_type(): string {
		return static::POST_TYPE;
	}

	/**
	 * Heading anchor / id used by the section wrapper.
	 *
	 * @return string
	 */
	protected function anchor(): string {
		return static::ANCHOR;
	}

	/**
	 * Message shown when the section has no items.
	 *
	 * @return string
	 */
	abstract protected function empty_message(): string;

	/**
	 * Build the inner body markup for the supplied posts.
	 *
	 * @param array<int, WP_Post> $posts Section posts.
	 *
	 * @return string
	 */
	abstract protected function render_body( array $posts ): string;

	/**
	 * Render callback: query the CPT, then wrap in the shared section markup.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 *
	 * @return string
	 */
	public function render( array $attributes ): string {
		$posts = Section::items( $this->post_type(), (int) ( $attributes['maxItems'] ?? 0 ) );

		$body = $posts
			? $this->render_body( $posts )
			: Section::empty_notice( $this->empty_message() );

		return Section::wrap(
			(string) ( $attributes['sectionLabel'] ?? '' ),
			(string) ( $attributes['heading'] ?? '' ),
			$this->anchor(),
			(string) ( $attributes['intro'] ?? '' ),
			$body
		);
	}
}
