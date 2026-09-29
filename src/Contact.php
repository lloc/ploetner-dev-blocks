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

	public const NAME = 'Dennis Plötner';

	public const ADDRESS = '[STREET NO.], [POSTCODE] [CITY] ([PROVINCE])';

	public const COUNTRY_CODE = 'IT';

	public const EMAIL = 're@lloc.de';

	public const VAT = 'IT13913110964';

	public const HOSTING = '[HOSTING PROVIDER, COUNTRY]';

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
	 * URL of the legal page (legal notice and privacy policy) on the current site.
	 *
	 * @return string
	 */
	public static function legal_url(): string {
		/* translators: Slug of the page with legal notice and privacy policy on the site in your language. */
		return home_url( '/' . _x( 'legal', 'legal page slug', 'ploetner-dev-blocks' ) . '/' );
	}
}
