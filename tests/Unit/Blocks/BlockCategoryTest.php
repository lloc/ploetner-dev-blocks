<?php
/**
 * Tests for the BlockCategory filter.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\BlockCategory;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\BlockCategory
 */
class BlockCategoryTest extends TestCase {

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
	public function test_register_hooks_filter(): void {
		Functions\expect( 'add_filter' )
			->once()
			->with( 'block_categories_all', Mockery::type( 'array' ) );

		( new BlockCategory() )->register();
	}

	/**
	 * @covers ::add_category
	 */
	public function test_add_category_prepends_ploetner_dev(): void {
		$result = ( new BlockCategory() )->add_category( array( array( 'slug' => 'common' ) ) );

		$this->assertSame( 'ploetner-dev', $result[0]['slug'] );
		$this->assertSame( 'common', $result[1]['slug'] );
	}
}
