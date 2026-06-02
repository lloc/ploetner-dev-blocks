<?php
/**
 * Community block — ploetner-dev/community.
 *
 * Heading/label/intro are block attributes; the cards come from the
 * `pd_community` post type. Card description is the post content (block markup).
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
			'04 / Community',
			'Building together',
			'WordPress is open source, and so is how I work.'
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
	 *
	 * @return string
	 */
	public function community_card( string $label, string $title, string $description ): string {
		$label = esc_html( $label );
		$title = esc_html( $title );

		// $description is post content (block markup); rendered by the section's do_blocks().
		return <<<HTML
<!-- wp:column {"className":"ploetner-card","style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"borderColor":"border","backgroundColor":"base-card"} -->
<div class="wp-block-column ploetner-card has-border-color has-border-border-color has-base-card-background-color has-background" style="border-width:1px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:paragraph {"fontFamily":"mono","fontSize":"tiny","textColor":"accent"} -->
<p class="has-accent-color has-text-color has-mono-font-family has-tiny-font-size">{$label}</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">{$title}</h3>
<!-- /wp:heading -->
<div class="ploetner-card-desc">{$description}</div>
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
				(string) $post->post_content
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
