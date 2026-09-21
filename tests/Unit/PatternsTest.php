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
		Functions\stubs( array( '__' ) );
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
	public function test_patterns_expose_the_four_slugs(): void {
		$patterns = ( new Patterns() )->patterns();

		$this->assertSame(
			array( 'separator', 'header', 'footer', 'front-page' ),
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
		Functions\expect( 'register_block_pattern' )->times( 4 );

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
}
