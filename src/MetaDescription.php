<?php
/**
 * Meta description for the front page.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * Prints <meta name="description"> on the front page, taken from the site
 * tagline (Settings > General), so every language site of the network gets its
 * own description. Steps aside when an SEO plugin handles it.
 */
class MetaDescription {

	/**
	 * Constants or functions that indicate an SEO plugin printing its own tag.
	 */
	private const SEO_PLUGINS = array(
		'WPSEO_VERSION',        // Yoast SEO.
		'RANK_MATH_VERSION',    // Rank Math.
		'AIOSEO_VERSION',       // All in One SEO.
		'SEOPRESS_VERSION',     // SEOPress.
		'THE_SEO_FRAMEWORK_VERSION',
	);

	/**
	 * Hook into wp_head.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_head', array( $this, 'render' ), 1 );
	}

	/**
	 * Print the tag when applicable.
	 *
	 * @return void
	 */
	public function render(): void {
		$content = $this->content();
		if ( '' === $content ) {
			return;
		}

		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $content ) );
	}

	/**
	 * The description to print, or an empty string.
	 *
	 * @return string
	 */
	public function content(): string {
		if ( ! is_front_page() || $this->seo_plugin_active() ) {
			return '';
		}

		return trim( wp_strip_all_tags( (string) get_bloginfo( 'description' ) ) );
	}

	/**
	 * Whether a known SEO plugin is active.
	 *
	 * @return bool
	 */
	private function seo_plugin_active(): bool {
		foreach ( self::SEO_PLUGINS as $constant ) {
			if ( defined( $constant ) ) {
				return true;
			}
		}

		return false;
	}
}
