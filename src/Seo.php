<?php
/**
 * Small SEO fixes in the document head.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

use lloc\SEO\Meta\Description as MetaDescription;
use lloc\SEO\Utils\CleanUp;

/**
 * - Meta description for the front page (the site tagline, Settings > General,
 *   so every language site of the network gets its own) and the legal page.
 * - hreflang x-default: points to the English site (Multisite Language
 *   Switcher only adds it when a page exists in a single language).
 * - schema.org Person (Yoast SEO): other spellings of the name and the public
 *   profiles, so search engines connect them to one entity.
 * - No WordPress generator tag, RSD, wlwmanifest or emoji scripts.
 */
class Seo {

	/**
	 * Language whose URL becomes hreflang="x-default".
	 */
	public const DEFAULT_LANGUAGE = 'en';

	public function __construct(
		protected MetaDescription $meta_description
	) {}

	/**
	 * Hook the filters.
	 *
	 * @return void
	 */
	public function register(): void {
		CleanUp::head();

		$this->meta_description->register();

		add_filter( $this->meta_description::META_DESCRIPTION_FILTER, array( $this, 'description' ) );

		add_filter( 'msls_output_get_alternate_links_arr', array( $this, 'x_default' ) );
		add_filter( 'wpseo_schema_person', array( $this, 'person' ) );
	}

	/**
	 * Description of the front page and the legal page.
	 *
	 * @param string $description Description so far.
	 *
	 * @return string
	 */
	public function description( string $description ): string {
		if ( is_front_page() ) {
			return trim( wp_strip_all_tags( (string) get_bloginfo( 'description' ) ) );
		}

		if ( is_page( _x( 'legal', 'legal page slug', 'ploetner-dev-blocks' ) ) ) {
			return __( 'Legal notice and privacy policy: who runs this website, which data is processed (server logs, Cloudflare) and your rights under the GDPR.', 'ploetner-dev-blocks' );
		}

		return $description;
	}

	/**
	 * Add an x-default alternate link that duplicates the default language.
	 *
	 * @param mixed $links Link tags as built by MLS.
	 *
	 * @return mixed
	 */
	public function x_default( $links ) {
		if ( ! is_array( $links ) ) {
			return $links;
		}

		$default = '';
		foreach ( $links as $link ) {
			if ( ! is_string( $link ) ) {
				continue;
			}
			if ( str_contains( $link, 'hreflang="x-default"' ) ) {
				return $links;
			}
			if ( '' === $default && str_contains( $link, 'hreflang="' . self::DEFAULT_LANGUAGE . '"' ) ) {
				$default = str_replace( 'hreflang="' . self::DEFAULT_LANGUAGE . '"', 'hreflang="x-default"', $link );
			}
		}

		if ( '' !== $default ) {
			$links[] = $default;
		}

		return $links;
	}

	/**
	 * Enrich the Yoast Person piece.
	 *
	 * @param mixed $data Schema piece.
	 *
	 * @return mixed
	 */
	public function person( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		$name      = (string) ( $data['name'] ?? '' );
		$alternate = array_values( array_diff( array_merge( array( Contact::NAME ), Contact::ALTERNATE_NAMES ), array( $name ) ) );
		if ( array() !== $alternate ) {
			$data['alternateName'] = $alternate;
		}

		$same_as        = isset( $data['sameAs'] ) && is_array( $data['sameAs'] ) ? $data['sameAs'] : array();
		$profiles       = array_map( static fn ( array $profile ): string => $profile[0], array_values( Contact::PROFILES ) );
		$data['sameAs'] = array_values( array_unique( array_merge( $same_as, $profiles ) ) );

		return $data;
	}
}
