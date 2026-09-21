<?php
/**
 * Block pattern registration for the Plötner Dev site.
 *
 * Provides the site's header, footer, front-page composition and section
 * separator as block patterns, so they live with the plugin (theme-switch
 * safe) rather than in a theme.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * Registers the "Plötner.dev" pattern category and its patterns.
 */
class Patterns {

	/**
	 * Shared pattern (and block) category slug.
	 */
	public const CATEGORY = 'ploetner-dev';

	/**
	 * Hook pattern registration into `init`.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_patterns' ) );
	}

	/**
	 * Register the pattern category and every pattern.
	 *
	 * @return void
	 */
	public function register_patterns(): void {
		register_block_pattern_category(
			self::CATEGORY,
			array( 'label' => __( 'Plötner.dev', 'ploetner-dev-blocks' ) )
		);

		foreach ( $this->patterns() as $slug => $pattern ) {
			register_block_pattern( self::CATEGORY . '/' . $slug, $pattern );
		}
	}

	/**
	 * Pattern definitions keyed by slug.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function patterns(): array {
		return array(
			'separator'  => array(
				'title'      => __( 'Section separator', 'ploetner-dev-blocks' ),
				'categories' => array( self::CATEGORY ),
				'content'    => $this->separator(),
			),
			'header'     => array(
				'title'      => __( 'Header', 'ploetner-dev-blocks' ),
				'categories' => array( self::CATEGORY ),
				'blockTypes' => array( 'core/template-part/header' ),
				'content'    => $this->header(),
			),
			'footer'     => array(
				'title'      => __( 'Footer', 'ploetner-dev-blocks' ),
				'categories' => array( self::CATEGORY ),
				'blockTypes' => array( 'core/template-part/footer' ),
				'content'    => $this->footer(),
			),
			'front-page' => array(
				'title'      => __( 'Front page sections', 'ploetner-dev-blocks' ),
				'categories' => array( self::CATEGORY ),
				'content'    => $this->front_page(),
			),
		);
	}

	/**
	 * Section separator markup.
	 *
	 * @return string
	 */
	public function separator(): string {
		return <<<'HTML'
<!-- wp:separator {"align":"wide","className":"is-style-wide","backgroundColor":"border"} -->
<hr class="wp-block-separator alignwide has-text-color has-border-color has-border-background-color has-background is-style-wide"/>
<!-- /wp:separator -->
HTML;
	}

	/**
	 * Sticky header: site title, primary navigation, contact CTA.
	 *
	 * @return string
	 */
	public function header(): string {
		return <<<'HTML'
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"position":{"type":"sticky","top":"0px"}},"backgroundColor":"base","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap"},"className":"ploetner-header"} -->
<div class="wp-block-group alignfull ploetner-header has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:site-title {"level":0} /-->

	<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
	<div class="wp-block-group">
		<!-- wp:navigation {"overlayMenu":"mobile","overlayBackgroundColor":"base","overlayTextColor":"contrast","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"small","fontFamily":"display"} -->
		<!-- wp:navigation-link {"label":"Expertise","url":"#expertise"} /-->
		<!-- wp:navigation-link {"label":"Open Source","url":"#open-source"} /-->
		<!-- wp:navigation-link {"label":"Speaking","url":"#speaking"} /-->
		<!-- wp:navigation-link {"label":"Community","url":"#community"} /-->
		<!-- /wp:navigation -->

		<!-- wp:buttons {"className":"ploetner-nav-cta"} -->
		<div class="wp-block-buttons ploetner-nav-cta">
			<!-- wp:button {"fontSize":"tiny","fontFamily":"mono"} -->
			<div class="wp-block-button has-custom-font-size has-mono-font-family has-tiny-font-size"><a class="wp-block-button__link wp-element-button" href="https://ploetner.cloud">Hire me &rarr;</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
	}

	/**
	 * Footer: social links and copyright.
	 *
	 * @return string
	 */
	public function footer(): string {
		return <<<'HTML'
<!-- wp:group {"align":"full","style":{"border":{"top":{"color":"var:preset|color|border","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"},"className":"ploetner-footer"} -->
<div class="wp-block-group alignfull ploetner-footer" style="border-top-color:var(--wp--preset--color--border);border-top-width:1px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://profiles.wordpress.org/realloc/" target="_blank" rel="noopener">WordPress</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://github.com/lloc" target="_blank" rel="noopener">GitHub</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://www.linkedin.com/in/dploetner/" target="_blank" rel="noopener">LinkedIn</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://mastodon.social/@realloc" target="_blank" rel="me noopener">Mastodon</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://x.com/realloc" target="_blank" rel="noopener">X</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
	<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size">&copy; 2026 Dennis Pl&ouml;tner, VAT number IT13913110964</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
	}

	/**
	 * Front-page content: the section blocks separated by rules.
	 *
	 * @return string
	 */
	public function front_page(): string {
		$sep      = $this->separator();
		$sections = array(
			'<!-- wp:ploetner-dev/hero /-->',
			'<!-- wp:ploetner-dev/expertise /-->',
			'<!-- wp:ploetner-dev/open-source /-->',
			'<!-- wp:ploetner-dev/speaking /-->',
			'<!-- wp:ploetner-dev/community /-->',
			'<!-- wp:ploetner-dev/cta-banner /-->',
		);

		return implode( "\n\n" . $sep . "\n\n", $sections );
	}
}
