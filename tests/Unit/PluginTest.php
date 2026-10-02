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
	public function test_blocks_returns_seven_block_instances(): void {
		$blocks = ( new Plugin() )->blocks();

		$this->assertCount( 7, $blocks );
		foreach ( $blocks as $block ) {
			$this->assertInstanceOf( Block::class, $block );
		}
	}

	/**
	 * @covers ::register
	 */
	public function test_register_wires_hooks(): void {
		// 2 (post types) + 2 (meta box: add_meta_boxes + save_post) + 5 (admin list: pre_get_posts + 4 custom columns)
		// + 1 (admin_init seed catch-up) + 7 (blocks) + 1 (assets) + 1 (patterns) + 1 (meta description) = 20 add_action calls.
		Functions\expect( 'add_action' )->times( 20 );
		Functions\expect( 'add_filter' )->once()->with( 'block_categories_all', \Mockery::type( 'array' ) );
		Functions\expect( 'add_filter' )->once()->with( 'ploetner_theme_favicon', \Mockery::type( 'array' ) );
		// Admin list: columns + sortable columns for each of the 4 post types.
		Functions\expect( 'add_filter' )->times( 8 )->with( \Mockery::pattern( '/^manage_(edit-)?pd_\w+_(posts_)?(sortable_)?columns$/' ), \Mockery::any() );

		( new Plugin() )->register();
	}

	/**
	 * @covers ::favicon
	 */
	public function test_favicon_is_the_dev_brand(): void {
		$this->assertSame( 'dev', ( new Plugin() )->favicon() );
	}
}
