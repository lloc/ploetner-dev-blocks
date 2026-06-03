<?php
/**
 * Expertise block — ploetner-dev/expertise.
 *
 * Skill cards from the `pd_expertise` post type, laid out in a seamless
 * bordered grid of rows-of-three (matching the original Expertise pattern).
 * Title = post title (prefixed with an accent ▸), description = post content.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

use WP_Post;

/**
 * The Expertise block.
 */
class Expertise extends SectionBlock {

	public const NAME      = 'ploetner-dev/expertise';
	public const ICON      = 'awards';
	public const POST_TYPE = 'pd_expertise';
	public const ANCHOR    = 'expertise';

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'Expertise', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return Section::attributes(
			__( '01 / Expertise', 'ploetner-dev-blocks' ),
			__( 'What I do best', 'ploetner-dev-blocks' ),
			__( 'Deep specialization in WordPress at enterprise scale, from plugin architecture to multisite infrastructure.', 'ploetner-dev-blocks' ),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function empty_message(): string {
		return __( 'Add expertise items in the dashboard.', 'ploetner-dev-blocks' );
	}

	/**
	 * Build a single expertise grid cell (a column).
	 *
	 * @param string $title       Cell title.
	 * @param string $description Cell description (block markup).
	 *
	 * @return string
	 */
	public function cell( string $title, string $description ): string {
		$title = esc_html( $title );

		return <<<HTML
<!-- wp:column {"backgroundColor":"base-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<div class="wp-block-column has-base-card-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><mark style="background-color:transparent" class="has-inline-color has-accent-color">▸</mark> {$title}</h3>
<!-- /wp:heading -->
<div class="ploetner-card-desc">{$description}</div>
</div>
<!-- /wp:column -->
HTML;
	}

	/**
	 * Wrap a row of cells in an expertise-grid columns block. Rows after the first
	 * drop their top border and tuck up by 1px so the grid lines stay seamless.
	 *
	 * @param string $cells Concatenated cell markup.
	 * @param bool   $first Whether this is the first row.
	 *
	 * @return string
	 */
	public function row( string $cells, bool $first ): string {
		if ( $first ) {
			$attrs = '{"className":"ploetner-expertise-grid","style":{"spacing":{"blockGap":{"left":"1px","top":"1px"}},"border":{"width":"1px"}},"borderColor":"border"}';
			$style = 'border-width:1px';
		} else {
			$attrs = '{"className":"ploetner-expertise-grid","style":{"spacing":{"blockGap":{"left":"1px","top":"1px"},"margin":{"top":"1px"}},"border":{"width":"0px 1px 1px 1px"}},"borderColor":"border"}';
			$style = 'border-width:0px 1px 1px 1px;margin-top:1px';
		}

		return <<<HTML
<!-- wp:columns {$attrs} -->
<div class="wp-block-columns ploetner-expertise-grid has-border-color has-border-border-color" style="{$style}">
{$cells}
</div>
<!-- /wp:columns -->
HTML;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function render_body( array $posts ): string {
		$body  = '';
		$first = true;
		foreach ( array_chunk( $posts, 3 ) as $chunk ) {
			$cells = '';
			foreach ( $chunk as $post ) {
				$cells .= $this->cell( get_the_title( $post ), (string) $post->post_content );
			}
			$body .= $this->row( $cells, $first );
			$first = false;
		}

		return $body;
	}
}
