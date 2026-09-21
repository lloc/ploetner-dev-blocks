<?php
/**
 * Tests for the Assets stylesheet enqueue.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Assets;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Assets
 */
class AssetsTest extends TestCase {

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_enqueue(): void {
		Functions\expect( 'add_action' )
			->once()
			->with( 'enqueue_block_assets', Mockery::type( 'array' ) );

		( new Assets() )->register();
	}

	/**
	 * @covers ::enqueue
	 */
	public function test_enqueue_registers_stylesheet(): void {
		Functions\when( 'is_readable' )->justReturn( false );
		Functions\when( 'plugins_url' )->justReturn( 'https://example.test/assets/blocks.css' );

		Functions\expect( 'wp_enqueue_style' )
			->once()
			->with( 'ploetner-dev-blocks', 'https://example.test/assets/blocks.css', array(), false );

		( new Assets() )->enqueue();
	}
}
