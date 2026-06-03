<?php
/**
 * Shared section helpers for the Plötner Dev Blocks plugin.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

use WP_Post;
use WP_Query;

/**
 * Markup helpers and the shared query used by the CPT-backed section blocks.
 */
class Section {

	/**
	 * Attribute defaults shared by every section block (section label, heading, intro, max items).
	 *
	 * @param string $section_label Default section label (e.g. "01 / Expertise").
	 * @param string $heading       Default heading.
	 * @param string $intro         Default intro paragraph.
	 *
	 * @return array<string, array{default: mixed, label: string}>
	 */
	public static function attributes( string $section_label, string $heading, string $intro ): array {
		return array(
			'sectionLabel' => array(
				'default' => $section_label,
				'label'   => __( 'Section label', 'ploetner-dev-blocks' ),
			),
			'heading'      => array(
				'default' => $heading,
				'label'   => __( 'Heading', 'ploetner-dev-blocks' ),
			),
			'intro'        => array(
				'default' => $intro,
				'label'   => __( 'Intro text', 'ploetner-dev-blocks' ),
			),
			'maxItems'     => array(
				'default' => 0,
				'label'   => __( 'Max items (0 = all)', 'ploetner-dev-blocks' ),
			),
		);
	}

	/**
	 * Wrap section body markup in the standard full-width, constrained group with
	 * label / heading / intro, followed by a "back to top" arrow link. Returns
	 * rendered HTML (runs do_blocks once).
	 *
	 * The arrow links to the `#top` fragment, which browsers special-case to scroll
	 * to the document top with a single click (no JavaScript). Smooth scrolling is
	 * supplied by the theme via `scroll-behavior: smooth`.
	 *
	 * @param string $section_label Section label text.
	 * @param string $heading       Heading text.
	 * @param string $anchor        Heading anchor / id (also used by the header nav).
	 * @param string $intro         Intro paragraph text.
	 * @param string $body          Inner block markup (not yet rendered).
	 *
	 * @return string
	 */
	public static function wrap( string $section_label, string $heading, string $anchor, string $intro, string $body ): string {
		$section_label = esc_html( $section_label );
		$heading       = esc_html( $heading );
		$intro         = esc_html( $intro );
		$anchor        = esc_attr( $anchor );
		$to_top_label  = esc_html( __( 'Back to top', 'ploetner-dev-blocks' ) );

		$markup = <<<HTML
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)">
<!-- wp:paragraph {"className":"ploetner-section-label","fontSize":"tiny","fontFamily":"mono","textColor":"accent"} -->
<p class="ploetner-section-label has-accent-color has-text-color has-mono-font-family has-tiny-font-size">{$section_label}</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"anchor":"{$anchor}","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" id="{$anchor}">{$heading}</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"300","lineHeight":"1.7"}},"textColor":"text-muted","fontSize":"large"} -->
<p class="has-text-muted-color has-text-color has-large-font-size" style="font-weight:300;line-height:1.7">{$intro}</p>
<!-- /wp:paragraph -->
{$body}
<!-- wp:paragraph {"align":"right","className":"ploetner-back-to-top","fontSize":"tiny","fontFamily":"mono","textColor":"accent"} -->
<p class="ploetner-back-to-top has-accent-color has-text-color has-mono-font-family has-tiny-font-size has-text-align-right"><a href="#top" aria-label="{$to_top_label}">&uarr; {$to_top_label}</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;

		return do_blocks( $markup );
	}

	/**
	 * Build a muted "empty" notice (block markup) shown when a section has no items.
	 *
	 * @param string $text Notice text.
	 *
	 * @return string
	 */
	public static function empty_notice( string $text ): string {
		$text = esc_html( $text );

		return <<<HTML
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"300"}},"textColor":"text-muted","fontSize":"small"} -->
<p class="has-text-muted-color has-text-color has-small-font-size" style="font-weight:300">{$text}</p>
<!-- /wp:paragraph -->
HTML;
	}

	/**
	 * Shared query for a section's items, ordered by menu_order.
	 *
	 * @param string $post_type Post type slug.
	 * @param int    $max_items 0 for all, otherwise the cap.
	 *
	 * @return array<int, WP_Post>
	 */
	public static function items( string $post_type, int $max_items ): array {
		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'post_status'            => 'publish',
				'posts_per_page'         => $max_items > 0 ? $max_items : -1,
				'orderby'                => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
			)
		);

		/** @var array<int, WP_Post> $posts */
		$posts = $query->posts;

		return $posts;
	}
}
