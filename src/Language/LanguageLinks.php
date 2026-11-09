<?php
/**
 * Language links from the Multisite Language Switcher.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Language;

/**
 * Reads the sites (languages) of the network from the Multisite Language
 * Switcher and resolves the link to the current content in each of them.
 *
 * Kept apart from the block so the markup can be tested without MLS.
 */
class LanguageLinks {

	/**
	 * Whether the Multisite Language Switcher API is available.
	 *
	 * @return bool
	 */
	public function available(): bool {
		return function_exists( 'msls_blog_collection' ) && class_exists( '\lloc\Msls\Options\Options' );
	}

	/**
	 * One entry per language, in the order configured in MLS.
	 *
	 * The URL points to the translation of the current content when MLS knows
	 * one, otherwise to the home page of that language's site, so switching
	 * always leads somewhere.
	 *
	 * @return array<int, array{code: string, name: string, locale: string, url: string, current: bool}>
	 */
	public function items(): array {
		if ( ! $this->available() ) {
			return array();
		}

		$collection = msls_blog_collection();
		$options    = \lloc\Msls\Options\Options::create();
		$items      = array();

		foreach ( $collection->get_objects() as $blog ) {
			$current = $collection->is_current_blog( $blog );
			$url     = (string) $blog->get_url( $options );

			if ( '' === $url ) {
				$url = get_home_url( (int) $blog->userblog_id, '/' );
			}

			$items[] = array(
				'code'    => strtoupper( (string) $blog->get_alpha2() ),
				'name'    => (string) $blog->get_description(),
				'locale'  => str_replace( '_', '-', (string) $blog->get_language() ),
				'url'     => $url,
				'current' => $current,
			);
		}

		return $items;
	}
}
