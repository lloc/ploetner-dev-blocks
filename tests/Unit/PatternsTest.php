<?php
/**
 * Tests for pattern registration.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Patterns;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Patterns
 */
class PatternsTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__', 'esc_html', 'esc_url', 'esc_attr' ) );
		Functions\when( 'home_url' )->alias( static fn ( string $path = '' ): string => 'https://ploetner.dev' . $path );
		Functions\when( '_x' )->returnArg();
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );
	}

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_init(): void {
		Functions\expect( 'add_action' )
			->once()
			->with( 'init', Mockery::type( 'array' ) );

		( new Patterns() )->register();
	}

	/**
	 * @covers ::patterns
	 */
	public function test_patterns_expose_the_five_slugs(): void {
		$patterns = ( new Patterns() )->patterns();

		$this->assertSame(
			array( 'separator', 'header', 'footer', 'front-page', 'legal' ),
			array_keys( $patterns )
		);
		foreach ( $patterns as $pattern ) {
			$this->assertNotEmpty( $pattern['content'] );
		}
	}

	/**
	 * @covers ::register_patterns
	 */
	public function test_register_patterns_registers_category_and_each_pattern(): void {
		Functions\expect( 'register_block_pattern_category' )
			->once()
			->with( 'ploetner-dev', Mockery::type( 'array' ) );
		Functions\expect( 'register_block_pattern' )->times( 5 );

		( new Patterns() )->register_patterns();
	}

	/**
	 * @covers ::front_page
	 */
	public function test_front_page_contains_all_section_blocks(): void {
		$html = ( new Patterns() )->front_page();

		foreach ( array( 'hero', 'expertise', 'open-source', 'speaking', 'community', 'cta-banner' ) as $block ) {
			$this->assertStringContainsString( "wp:ploetner-dev/{$block}", $html );
		}
	}

	/**
	 * @covers ::navigation_links
	 * @covers ::header
	 */
	public function test_header_uses_translated_labels_with_stable_anchors(): void {
		Functions\when( '__' )->alias(
			static fn ( string $text ): string => array(
				'Speaking'     => 'Vorträge',
				'Need help? →' => 'Hilfe gebraucht? →',
			)[ $text ] ?? $text
		);

		$html = ( new Patterns() )->header();

		$this->assertStringContainsString( '<!-- wp:navigation-link {"label":"Vorträge","url":"#speaking"} /-->', $html );
		$this->assertStringContainsString( '<!-- wp:navigation-link {"label":"Expertise","url":"#expertise"} /-->', $html );
		$this->assertStringContainsString( '>Hilfe gebraucht? →</a>', $html );
	}

	/**
	 * @covers ::footer
	 */
	public function test_footer_copyright_is_translatable(): void {
		Functions\when( '__' )->justReturn( '© %1$s %2$s, USt-IdNr. %3$s' );

		$this->assertStringContainsString( '© ' . gmdate( 'Y' ) . ' <a href="mailto:re@lloc.de">Dennis Ploetner</a>, USt-IdNr. IT13913110964', ( new Patterns() )->footer() );
	}

	/**
	 * @covers ::header
	 */
	public function test_header_cta_url_is_translatable_with_context(): void {
		Functions\when( '_x' )->alias(
			static fn ( string $text, string $context ): string => 'ploetner.cloud URL' === $context ? 'https://ploetner.cloud/de/' : $text
		);

		$this->assertStringContainsString( 'href="https://ploetner.cloud/de/"', ( new Patterns() )->header() );
	}

	/**
	 * @covers ::footer
	 */
	public function test_footer_social_links_announce_new_tab(): void {
		$html = ( new Patterns() )->footer();

		$this->assertSame( 5, substr_count( $html, '<span class="ploetner-sr-only">(opens in a new tab)</span></a>' ) );
	}

	/**
	 * @covers ::footer
	 */
	public function test_footer_has_email_and_legal_link(): void {
		$html = ( new Patterns() )->footer();

		$this->assertStringContainsString( '<a href="mailto:re@lloc.de">Dennis Ploetner</a>', $html );
		$this->assertStringNotContainsString( '>re@lloc.de</a>', $html );
		$this->assertStringContainsString( '<a href="https://ploetner.dev/legal/">Legal notice & privacy</a>', $html );
	}
}
