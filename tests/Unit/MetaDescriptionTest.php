<?php
/**
 * Tests for the front-page meta description.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\MetaDescription;
use lloc\PloetnerDevBlocks\Tests\TestCase;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\MetaDescription
 */
class MetaDescriptionTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_attr', 'wp_strip_all_tags', '__' ) );
		Functions\when( '_x' )->returnArg();
		Functions\when( 'is_page' )->justReturn( false );
	}

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_wp_head_early(): void {
		Functions\expect( 'add_action' )->once()->with( 'wp_head', \Mockery::type( 'array' ), 1 );

		( new MetaDescription() )->register();
	}

	/**
	 * @covers ::render
	 * @covers ::content
	 */
	public function test_prints_tagline_on_front_page(): void {
		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_bloginfo' )->justReturn( 'Enterprise WordPress, multisite & open source' );

		$this->expectOutputString( '<meta name="description" content="Enterprise WordPress, multisite & open source" />' . "\n" );

		( new MetaDescription() )->render();
	}

	/**
	 * @covers ::render
	 * @covers ::content
	 */
	public function test_prints_nothing_elsewhere_or_without_tagline(): void {
		Functions\when( 'is_front_page' )->justReturn( false );
		$this->assertSame( '', ( new MetaDescription() )->content() );

		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_bloginfo' )->justReturn( '' );
		$this->expectOutputString( '' );
		( new MetaDescription() )->render();
	}

	/**
	 * @covers ::description
	 */
	public function test_legal_page_gets_a_description(): void {
		Functions\when( 'is_front_page' )->justReturn( false );
		Functions\when( 'is_page' )->alias( static fn ( $slug ): bool => 'legal' === $slug );

		$this->assertStringStartsWith( 'Legal notice and privacy policy', ( new MetaDescription() )->description() );
	}

	/**
	 * @covers ::fill
	 */
	public function test_fill_keeps_yoast_description_and_fills_empty_ones(): void {
		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_bloginfo' )->justReturn( 'Tagline' );
		$meta = new MetaDescription();

		$this->assertSame( 'From Yoast', $meta->fill( 'From Yoast' ) );
		$this->assertSame( 'Tagline', $meta->fill( '' ) );
		$this->assertSame( 'Tagline', $meta->fill( null ) );

		Functions\when( 'is_front_page' )->justReturn( false );
		$this->assertSame( '', $meta->fill( '' ) );
	}
}
