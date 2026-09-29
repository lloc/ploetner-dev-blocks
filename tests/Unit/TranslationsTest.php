<?php
/**
 * Consistency checks for the shipped translation files.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * The sample content is seeded in the site language, so a missing string in
 * one language shows up as English text on that site. German is the reference.
 *
 * @coversNothing
 */
class TranslationsTest extends TestCase {

	/**
	 * Messages of a compiled .l10n.php file.
	 *
	 * @param string $locale Locale, e.g. de_DE.
	 *
	 * @return array<string, string>
	 */
	private static function messages( string $locale ): array {
		$data = require dirname( __DIR__, 2 ) . "/languages/ploetner-dev-blocks-{$locale}.l10n.php";

		return $data['messages'];
	}

	/**
	 * @dataProvider locales
	 *
	 * @param string $locale Locale to compare with de_DE.
	 */
	public function test_locale_translates_the_same_strings_as_german( string $locale ): void {
		$expected = array_keys( self::messages( 'de_DE' ) );
		$actual   = array_keys( self::messages( $locale ) );
		sort( $expected );
		sort( $actual );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @dataProvider locales
	 *
	 * @param string $locale Locale to check.
	 */
	public function test_placeholders_are_kept( string $locale ): void {
		foreach ( self::messages( $locale ) as $original => $translation ) {
			preg_match_all( '/%(?:\d+\$)?[sd]/', (string) $original, $expected );
			preg_match_all( '/%(?:\d+\$)?[sd]/', $translation, $actual );
			sort( $expected[0] );
			sort( $actual[0] );

			$this->assertSame( $expected[0], $actual[0], "Placeholders differ in: {$original}" );
		}
	}

	/**
	 * Shipped locales.
	 *
	 * @return array<string, array{string}>
	 */
	public static function locales(): array {
		return array(
			'de_DE' => array( 'de_DE' ),
			'it_IT' => array( 'it_IT' ),
		);
	}
}
