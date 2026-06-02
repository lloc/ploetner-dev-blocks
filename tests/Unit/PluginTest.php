<?php
/**
 * Tests for the Plugin bootstrap.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\Block;
use lloc\PloetnerDevBlocks\Plugin;
use lloc\PloetnerDevBlocks\Tests\TestCase;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Plugin
 */
class PluginTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__' ) );
	}

	/**
	 * @covers ::blocks
	 */
	public function test_blocks_returns_six_block_instances(): void {
		$blocks = ( new Plugin() )->blocks();

		$this->assertCount( 6, $blocks );
		foreach ( $blocks as $block ) {
			$this->assertInstanceOf( Block::class, $block );
		}
	}

	/**
	 * @covers ::register
	 */
	public function test_register_wires_hooks(): void {
		// 2 (post types) + 2 (meta box: add_meta_boxes + save_post) + 6 (blocks) = 10 add_action calls.
		Functions\expect( 'add_action' )->times( 10 );
		Functions\expect( 'add_filter' )->once()->with( 'block_categories_all', \Mockery::type( 'array' ) );

		( new Plugin() )->register();
	}
}
