<?php
/**
 * Open Source block — ploetner-dev/open-source.
 *
 * Project cards from the `pd_project` post type. Title = post title, description
 * = post content, tech line + link URL + link text come from post meta.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

/**
 * The Open Source block.
 */
class OpenSource extends SectionBlock {

	public const NAME      = 'ploetner-dev/open-source';
	public const ICON      = 'editor-code';
	public const POST_TYPE = 'pd_project';
	public const ANCHOR    = 'open-source';

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'Open Source', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return Section::attributes(
			__( '02 / Open Source', 'ploetner-dev-blocks' ),
			__( 'Projects I work on', 'ploetner-dev-blocks' ),
			__( 'Code I build and share with the community.', 'ploetner-dev-blocks' )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function empty_message(): string {
		return __( 'Add projects in the dashboard.', 'ploetner-dev-blocks' );
	}

	/**
	 * Build the markup for a single project card.
	 *
	 * @param string $title       Card title.
	 * @param string $description Card description (block markup).
	 * @param string $tech        Tech tag line.
	 * @param string $url         Link URL.
	 * @param string $link_text   Link text.
	 *
	 * @return string
	 */
	public function project_card( string $title, string $description, string $tech, string $url, string $link_text ): string {
		$title = esc_html( $title );

		$tech_block = '';
		if ( '' !== $tech ) {
			$tech       = esc_html( $tech );
			$tech_block = <<<HTML
<!-- wp:paragraph {"style":{"typography":{"letterSpacing":"0.05em"}},"fontSize":"tiny","fontFamily":"mono","textColor":"text-dim"} -->
<p class="has-text-dim-color has-text-color has-mono-font-family has-tiny-font-size" style="letter-spacing:0.05em">{$tech}</p>
<!-- /wp:paragraph -->
HTML;
		}

		$link_block = '';
		if ( '' !== $url ) {
			$href       = esc_url( $url );
			$text       = esc_html( '' !== $link_text ? $link_text : __( 'View project ↗', 'ploetner-dev-blocks' ) );
			$link_block = <<<HTML
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><a href="{$href}" target="_blank" rel="noopener">{$text}</a></p>
<!-- /wp:paragraph -->
HTML;
		}

		return <<<HTML
<!-- wp:column {"className":"ploetner-card","style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"borderColor":"border","backgroundColor":"base-card"} -->
<div class="wp-block-column ploetner-card has-border-color has-border-border-color has-base-card-background-color has-background" style="border-width:1px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:heading {"level":3,"style":{"typography":{"fontWeight":"400"}},"fontSize":"small","fontFamily":"mono","textColor":"accent"} -->
<h3 class="wp-block-heading has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-weight:400">{$title}</h3>
<!-- /wp:heading -->
<div class="ploetner-card-desc">{$description}</div>
{$tech_block}
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
			$cards .= $this->project_card(
				get_the_title( $post ),
				(string) $post->post_content,
				(string) get_post_meta( $post->ID, '_pd_project_tech', true ),
				(string) get_post_meta( $post->ID, '_pd_project_url', true ),
				(string) get_post_meta( $post->ID, '_pd_project_link_text', true )
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
