<?php
/**
 * Small SEO fixes in the document head.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * - hreflang x-default: points to the English site (Multisite Language
 *   Switcher only adds it when a page exists in a single language).
 * - schema.org Person (Yoast SEO): other spellings of the name and the public
 *   profiles, so search engines connect them to one entity.
 * - No WordPress generator tag.
 */
class Seo {

	/**
	 * Language whose URL becomes hreflang="x-default".
	 */
	public const DEFAULT_LANGUAGE = 'en';

	/**
	 * Hook the filters.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'msls_output_get_alternate_links_arr', array( $this, 'x_default' ) );
		add_filter( 'wpseo_schema_person', array( $this, 'person' ) );
		add_filter( 'the_generator', '__return_empty_string' );
		remove_action( 'wp_head', 'wp_generator' );
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
