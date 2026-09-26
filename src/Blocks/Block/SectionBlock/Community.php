<?php
/**
 * Community block — ploetner-dev/community.
 *
 * Heading/label/intro are block attributes; the cards come from the
 * `pd_community` post type. Card description is the post content (block markup);
 * an optional link (URL + text) comes from post meta.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

use WP_Post;

/**
 * The Community block.
 */
class Community extends SectionBlock {

	public const NAME      = 'ploetner-dev/community';
	public const ICON      = 'groups';
	public const POST_TYPE = 'pd_community';
	public const ANCHOR    = 'community';

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'Community', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return Section::attributes(
			__( '04 / Community', 'ploetner-dev-blocks' ),
			__( 'Building together', 'ploetner-dev-blocks' ),
			__( 'WordPress is open source, and so is how I work.', 'ploetner-dev-blocks' )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function empty_message(): string {
		return __( 'Add community items in the dashboard.', 'ploetner-dev-blocks' );
	}

	/**
	 * Build the markup for a single community card.
	 *
	 * @param string $label       Card label.
	 * @param string $title       Card title.
	 * @param string $description Card description (block markup).
	 * @param string $url         Optional link URL.
	 * @param string $link_text   Optional link text (falls back to a default).
	 *
	 * @return string
	 */
	public function community_card( string $label, string $title, string $description, string $url = '', string $link_text = '' ): string {
		$label = esc_html( $label );
		$title = esc_html( $title );

		$link_block = Section::link_block( $url, $link_text, __( 'Learn more ↗', 'ploetner-dev-blocks' ) );

		// $description is post content (block markup); rendered by the section's do_blocks().
		return <<<HTML
<!-- wp:column {"className":"ploetner-card","style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"borderColor":"border","backgroundColor":"surface"} -->
<div class="wp-block-column ploetner-card has-border-color has-border-border-color has-surface-background-color has-background" style="border-width:1px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:paragraph {"fontFamily":"mono","fontSize":"tiny","textColor":"accent"} -->
<p class="has-accent-color has-text-color has-mono-font-family has-tiny-font-size">{$label}</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"textColor":"contrast","fontSize":"medium"} -->
<h3 class="wp-block-heading has-contrast-color has-text-color has-medium-font-size">{$title}</h3>
<!-- /wp:heading -->
<div class="ploetner-card-desc">{$description}</div>
{$link_block}
</div>
<!-- /wp:column -->
HTML;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function render_body( array $posts ): string {
		$cards = '';
		foreach ( $posts as $post ) {
			$cards .= $this->community_card(
				(string) get_post_meta( $post->ID, '_pd_community_label', true ),
				get_the_title( $post ),
				(string) $post->post_content,
				(string) get_post_meta( $post->ID, '_pd_community_url', true ),
				(string) get_post_meta( $post->ID, '_pd_community_link_text', true )
			);
		}

		return <<<HTML
<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns">
{$cards}
</div>
<!-- /wp:columns -->
HTML;
	}
}
