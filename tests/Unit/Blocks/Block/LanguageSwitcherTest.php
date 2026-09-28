<?php
/**
 * Tests for the Language switcher block.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\LanguageSwitcher;
use lloc\PloetnerDevBlocks\Language\LanguageLinks;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\LanguageSwitcher
 */
class LanguageSwitcherTest extends TestCase {

	/**
	 * Two languages, German current.
	 *
	 * @var array<int, array{code: string, name: string, locale: string, url: string, current: bool}>
	 */
	private const ITEMS = array(
		array(
			'code'    => 'EN',
			'name'    => 'English',
			'locale'  => 'en-US',
			'url'     => 'https://ploetner.dev/',
			'current' => false,
		),
		array(
			'code'    => 'DE',
			'name'    => 'Deutsch',
			'locale'  => 'de-DE',
			'url'     => 'https://ploetner.dev/de/',
			'current' => true,
		),
	);

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__', 'esc_html', 'esc_attr', 'esc_url' ) );
	}

	/**
	 * Block with a stubbed link source.
	 *
	 * @param array<int, array<string, mixed>> $items Items returned by the source.
	 *
	 * @return LanguageSwitcher
	 */
	private function block( array $items ): LanguageSwitcher {
		$links = Mockery::mock( LanguageLinks::class );
		$links->shouldReceive( 'items' )->andReturn( $items );

		return new LanguageSwitcher( $links );
	}

	/**
	 * @covers ::render
	 */
	public function test_renders_nothing_with_fewer_than_two_languages(): void {
		Functions\expect( 'wp_enqueue_script' )->never();

		$this->assertSame( '', $this->block( array() )->render( array() ) );
		$this->assertSame( '', $this->block( array( self::ITEMS[0] ) )->render( array() ) );
	}

	/**
	 * @covers ::render
	 * @covers ::markup
	 * @covers ::globe
	 */
	public function test_renders_globe_and_language_list(): void {
		Functions\when( 'plugins_url' )->justReturn( 'https://example.test/language-switcher.js' );
		Functions\expect( 'wp_enqueue_script' )->once();

		$html = $this->block( self::ITEMS )->render( array() );

		$this->assertStringContainsString( '<details class="ploetner-lang">', $html );
		$this->assertStringContainsString( 'aria-label="Choose language"', $html );
		$this->assertStringContainsString( '<svg class="ploetner-lang__icon"', $html );
		$this->assertStringContainsString( '<a href="https://ploetner.dev/" hreflang="en-US" lang="en-US"><span class="ploetner-lang__code">EN</span> English</a>', $html );
		$this->assertStringContainsString( 'hreflang="de-DE" lang="de-DE" aria-current="true"', $html );
		$this->assertSame( 1, substr_count( $html, 'aria-current' ) );
	}

	/**
	 * @covers ::defaults
	 */
	public function test_has_no_attributes(): void {
		$this->assertSame( array(), $this->block( array() )->defaults() );
	}
}
