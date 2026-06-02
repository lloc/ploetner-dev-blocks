<?php
/**
 * Tests for the MetaBox save/render flow.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\PostTypes;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\PostTypes\MetaBox;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;
use WP_Post;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\PostTypes\MetaBox
 */
class MetaBoxTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__', 'sanitize_text_field', 'wp_unslash' ) );
		Functions\when( 'wp_is_post_revision' )->justReturn( false );
		$_POST = array();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function tearDown(): void {
		$_POST = array();
		parent::tearDown();
	}

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_meta_boxes_and_save(): void {
		Functions\expect( 'add_action' )->once()->with( 'add_meta_boxes', Mockery::type( 'array' ) );
		Functions\expect( 'add_action' )->once()->with( 'save_post', Mockery::type( 'array' ), 10, 2 );

		( new MetaBox() )->register();
	}

	/**
	 * @covers ::add_meta_boxes
	 */
	public function test_add_meta_boxes_registers_box_per_meta_post_type(): void {
		Functions\expect( 'add_meta_box' )->times( 3 );

		( new MetaBox() )->add_meta_boxes();
	}

	/**
	 * @covers ::save
	 */
	public function test_save_bails_without_nonce(): void {
		Functions\expect( 'update_post_meta' )->never();

		$post = new WP_Post( array( 'post_type' => 'pd_talk' ) );
		( new MetaBox() )->save( 5, $post );
	}

	/**
	 * @covers ::save
	 */
	public function test_save_bails_for_post_type_without_fields(): void {
		Functions\expect( 'update_post_meta' )->never();

		$post = new WP_Post( array( 'post_type' => 'page' ) );
		( new MetaBox() )->save( 5, $post );
	}

	/**
	 * @covers ::save
	 */
	public function test_save_persists_submitted_values(): void {
		$_POST = array(
			'pd_meta_nonce' => 'nonce',
			'_pd_talk_year' => '2026',
		);

		Functions\when( 'wp_verify_nonce' )->justReturn( true );
		Functions\when( 'current_user_can' )->justReturn( true );

		Functions\expect( 'update_post_meta' )
			->once()
			->with( 5, '_pd_talk_year', '2026' );

		$post = new WP_Post( array( 'post_type' => 'pd_talk' ) );
		( new MetaBox() )->save( 5, $post );
	}

	/**
	 * @covers ::save
	 */
	public function test_save_bails_without_capability(): void {
		$_POST = array(
			'pd_meta_nonce' => 'nonce',
			'_pd_talk_year' => '2026',
		);

		Functions\when( 'wp_verify_nonce' )->justReturn( true );
		Functions\when( 'current_user_can' )->justReturn( false );
		Functions\expect( 'update_post_meta' )->never();

		$post = new WP_Post( array( 'post_type' => 'pd_talk' ) );
		( new MetaBox() )->save( 5, $post );
	}
}
