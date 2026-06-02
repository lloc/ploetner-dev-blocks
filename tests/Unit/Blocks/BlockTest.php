<?php
/**
 * Tests for the abstract Block base (exercised through CtaBanner).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\CtaBanner;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\Block
 */
class BlockTest extends TestCase {

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

		( new CtaBanner() )->register();
	}

	/**
	 * @covers ::register_block
	 */
	public function test_register_block_adds_autoregister_on_wp_7(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '7.0' );

		Functions\expect( 'register_block_type' )
			->once()
			->with(
				'ploetner-dev/cta-banner',
				Mockery::on(
					static function ( array $args ): bool {
						return 'ploetner-dev' === $args['category']
							&& isset( $args['supports']['autoRegister'] )
							&& true === $args['supports']['autoRegister']
							&& 'string' === $args['attributes']['heading']['type'];
					}
				)
			);

		( new CtaBanner() )->register_block();
	}

	/**
	 * @covers ::register_block
	 */
	public function test_register_block_omits_autoregister_before_wp_7(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '6.7' );

		Functions\expect( 'register_block_type' )
			->once()
			->with(
				'ploetner-dev/cta-banner',
				Mockery::on(
					static function ( array $args ): bool {
						return ! isset( $args['supports'] );
					}
				)
			);

		( new CtaBanner() )->register_block();
	}
}
