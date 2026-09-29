<?php
/**
 * Provider and contact details, single source for footer and legal notice.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * Who runs ploetner.dev, as required by Art. 7 D.Lgs. 70/2003 and Art. 13 GDPR.
 */
final class Contact {

	/**
	 * Name as registered for the VAT number (Partita IVA).
	 */
	public const NAME = 'Dennis Ploetner';

	public const ADDRESS = 'Via della Pace, 4, 26839 Zelo Buon Persico (LO)';

	public const COUNTRY_CODE = 'IT';

	public const EMAIL = 're@lloc.de';

	public const VAT = 'IT13913110964';

	public const HOSTING = 'Hostinger';

	public const LOG_DAYS = 14;

	/**
	 * Country name in the site language.
	 *
	 * @return string
	 */
	public static function country(): string {
		return __( 'Italy', 'ploetner-dev-blocks' );
	}

	/**
	 * Hosting provider with server location, in the site language.
	 *
	 * @return string
	 */
	public static function hosting(): string {
		return sprintf(
			/* translators: 1: hosting provider, 2: server location. */
			__( '%1$s (server location: %2$s)', 'ploetner-dev-blocks' ),
			self::HOSTING,
			__( 'Frankfurt am Main, Germany', 'ploetner-dev-blocks' )
		);
	}

	/**
	 * URL of the legal page (legal notice and privacy policy) on the current site.
	 *
	 * @return string
	 */
	public static function legal_url(): string {
		/* translators: Slug of the page with legal notice and privacy policy on the site in your language. */
		return home_url( '/' . _x( 'legal', 'legal page slug', 'ploetner-dev-blocks' ) . '/' );
	}
}
