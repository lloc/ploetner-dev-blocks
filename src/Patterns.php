<?php
/**
 * Block pattern registration for the Ploetner Dev site.
 *
 * Provides the site's header, footer, front-page composition and section
 * separator as block patterns, so they live with the plugin (theme-switch
 * safe) rather than in a theme.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

use lloc\PloetnerDevBlocks\Blocks\Section;

/**
 * Registers the "ploetner.dev" pattern category and its patterns.
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
			array( 'label' => __( 'ploetner.dev', 'ploetner-dev-blocks' ) )
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
			// No postTypes restriction: with "Show template" on, the page editor
			// filters patterns against wp_template, which would hide this one.
			'legal'      => array(
				'title'      => __( 'Legal notice and privacy policy', 'ploetner-dev-blocks' ),
				'categories' => array( self::CATEGORY ),
				'content'    => ( new LegalPage() )->content(),
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
	 * Sticky header: site title, primary navigation, language switcher, contact CTA.
	 *
	 * @return string
	 */
	public function header(): string {
		$nav      = $this->navigation_links();
		$cta_text = esc_html( __( 'Need help? →', 'ploetner-dev-blocks' ) );
		$cta_url  = esc_url(
			/* translators: Link target of the header button. Point it to the ploetner.cloud version in your language once it exists. */
			_x( 'https://ploetner.cloud/', 'ploetner.cloud URL', 'ploetner-dev-blocks' )
		);

		return <<<HTML
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"position":{"type":"sticky","top":"0px"}},"backgroundColor":"base","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap"},"className":"ploetner-header"} -->
<div class="wp-block-group alignfull ploetner-header has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:site-title {"level":0} /-->

	<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
	<div class="wp-block-group">
		<!-- wp:navigation {"overlayMenu":"mobile","overlayBackgroundColor":"base","overlayTextColor":"contrast","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"fontSize":"small","fontFamily":"display"} -->
{$nav}
		<!-- /wp:navigation -->

		<!-- wp:ploetner-dev/language-switcher /-->

		<!-- wp:buttons {"className":"ploetner-nav-cta"} -->
		<div class="wp-block-buttons ploetner-nav-cta">
			<!-- wp:button {"fontSize":"tiny","fontFamily":"mono"} -->
			<div class="wp-block-button has-custom-font-size has-mono-font-family has-tiny-font-size"><a class="wp-block-button__link wp-element-button" href="{$cta_url}">{$cta_text}</a></div>
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
	 * Header navigation links to the front-page section anchors. Labels are
	 * translated, the anchors stay stable across languages.
	 *
	 * @return string
	 */
	public function navigation_links(): string {
		$links = array(
			'expertise'   => __( 'Expertise', 'ploetner-dev-blocks' ),
			'open-source' => __( 'Open Source', 'ploetner-dev-blocks' ),
			'speaking'    => __( 'Speaking', 'ploetner-dev-blocks' ),
			'community'   => __( 'Community', 'ploetner-dev-blocks' ),
		);

		$markup = array();
		foreach ( $links as $anchor => $label ) {
			$attrs    = (string) wp_json_encode(
				array(
					'label' => $label,
					'url'   => '#' . $anchor,
				),
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP
			);
			$markup[] = "\t\t<!-- wp:navigation-link {$attrs} /-->";
		}

		return implode( "\n", $markup );
	}

	/**
	 * Footer: social links, copyright (name linked to the email address) with VAT
	 * number and legal page link.
	 *
	 * @return string
	 */
	public function footer(): string {
		$new_tab = Section::new_tab_hint();
		$name    = sprintf(
			'<a href="mailto:%1$s">%2$s</a>',
			esc_attr( Contact::EMAIL ),
			esc_html( Contact::NAME )
		);
		/* translators: 1: year, 2: name (linked to the email address), 3: VAT number. */
		$format    = __( '© %1$s %2$s, VAT number %3$s', 'ploetner-dev-blocks' );
		$copyright = sprintf(
			esc_html( $format ),
			gmdate( 'Y' ),
			$name,
			esc_html( Contact::VAT )
		);
		$legal     = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( Contact::legal_url() ),
			esc_html( __( 'Legal notice & privacy', 'ploetner-dev-blocks' ) )
		);

		return <<<HTML
<!-- wp:group {"align":"full","style":{"border":{"top":{"color":"var:preset|color|border","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"},"className":"ploetner-footer"} -->
<div class="wp-block-group alignfull ploetner-footer" style="border-top-color:var(--wp--preset--color--border);border-top-width:1px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://profiles.wordpress.org/realloc/" target="_blank" rel="noopener">WordPress{$new_tab}</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://github.com/lloc" target="_blank" rel="noopener">GitHub{$new_tab}</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://www.linkedin.com/in/dploetner/" target="_blank" rel="noopener">LinkedIn{$new_tab}</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://mastodon.social/@realloc" target="_blank" rel="me noopener">Mastodon{$new_tab}</a></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
		<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size"><a href="https://x.com/realloc" target="_blank" rel="noopener">X{$new_tab}</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"fontSize":"tiny","fontFamily":"mono","textColor":"dim"} -->
	<p class="has-dim-color has-text-color has-mono-font-family has-tiny-font-size">{$copyright} · {$legal}</p>
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
