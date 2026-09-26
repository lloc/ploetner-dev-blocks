<?php
/**
 * Tests for the PostTypes registrar.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\PostTypes;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\PostTypes\PostTypes;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\PostTypes\PostTypes
 */
class PostTypesTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__', 'sanitize_text_field' ) );
	}

	/**
	 * @covers ::definitions
	 */
	public function test_definitions_cover_all_four_post_types(): void {
		$this->assertSame(
			array( 'pd_expertise', 'pd_project', 'pd_talk', 'pd_community' ),
			array_keys( PostTypes::definitions() )
		);
	}

	/**
	 * @covers ::meta_fields
	 */
	public function test_meta_fields_expose_expected_keys(): void {
		$fields = PostTypes::meta_fields();

		$this->assertSame(
			array( '_pd_project_tech', '_pd_project_url', '_pd_project_link_text' ),
			array_keys( $fields['pd_project'] )
		);
		$this->assertSame( 'esc_url_raw', $fields['pd_project']['_pd_project_url']['sanitize'] );
		$this->assertSame(
			array( '_pd_talk_year', '_pd_talk_event', '_pd_talk_url' ),
			array_keys( $fields['pd_talk'] )
		);
		$this->assertSame( 'esc_url_raw', $fields['pd_talk']['_pd_talk_url']['sanitize'] );
		$this->assertSame(
			array( '_pd_community_label', '_pd_community_url', '_pd_community_link_text' ),
			array_keys( $fields['pd_community'] )
		);
		$this->assertSame( 'esc_url_raw', $fields['pd_community']['_pd_community_url']['sanitize'] );
	}

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_both_init_callbacks(): void {
		Functions\expect( 'add_action' )
			->twice()
			->with( 'init', Mockery::type( 'array' ) );

		( new PostTypes() )->register();
	}

	/**
	 * @covers ::register_post_types
	 */
	public function test_register_post_types_registers_each_type(): void {
		Functions\expect( 'register_post_type' )->times( 4 );

		( new PostTypes() )->register_post_types();
	}

	/**
	 * @covers ::register_meta
	 */
	public function test_register_meta_registers_each_meta_key(): void {
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'register_post_meta' )->times( 9 );

		( new PostTypes() )->register_meta();
	}
}
