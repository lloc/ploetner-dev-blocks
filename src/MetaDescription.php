<?php
/**
 * Meta description for the front page.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * Meta description for the front page (the site tagline, Settings > General,
 * so every language site of the network gets its own) and the legal page.
 *
 * Without an SEO plugin it prints the tag itself. With Yoast SEO it only fills
 * in the description (and og:description) where Yoast has none.
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
		add_filter( 'wpseo_metadesc', array( $this, 'fill' ) );
		add_filter( 'wpseo_opengraph_desc', array( $this, 'fill' ) );
	}

	/**
	 * Yoast filter: keep a description set in Yoast, otherwise use ours.
	 *
	 * @param mixed $description Description from Yoast.
	 *
	 * @return mixed
	 */
	public function fill( $description ) {
		if ( is_string( $description ) && '' !== trim( $description ) ) {
			return $description;
		}

		$ours = $this->description();

		return '' === $ours ? $description : $ours;
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
		if ( $this->seo_plugin_active() ) {
			return '';
		}

		return $this->description();
	}

	/**
	 * Description for the current page, or an empty string.
	 *
	 * @return string
	 */
	public function description(): string {
		if ( is_front_page() ) {
			return trim( wp_strip_all_tags( (string) get_bloginfo( 'description' ) ) );
		}

		if ( is_page( _x( 'legal', 'legal page slug', 'ploetner-dev-blocks' ) ) ) {
			return __( 'Legal notice and privacy policy: who runs this website, which data is processed (server logs, Cloudflare) and your rights under the GDPR.', 'ploetner-dev-blocks' );
		}

		return '';
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
