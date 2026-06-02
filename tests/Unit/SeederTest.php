<?php
/**
 * Tests for the content Seeder.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Seeder;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Seeder
 */
class SeederTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', '__' ) );
		WP_Query::reset();
	}

	/**
	 * @covers ::data
	 */
	public function test_data_has_expected_shape(): void {
		$data = Seeder::data();

		$this->assertSame(
			array( 'pd_expertise', 'pd_project', 'pd_talk', 'pd_community' ),
			array_keys( $data )
		);
		$this->assertCount( 6, $data['pd_expertise'] );
		$this->assertCount( 3, $data['pd_project'] );
		$this->assertCount( 3, $data['pd_talk'] );
		$this->assertCount( 3, $data['pd_community'] );

		// Projects carry the three meta keys; talks carry no content.
		$this->assertSame(
			array( '_pd_project_tech', '_pd_project_url', '_pd_project_link_text' ),
			array_keys( $data['pd_project'][0]['meta'] )
		);
		$this->assertArrayNotHasKey( 'content', $data['pd_talk'][0] );
	}

	/**
	 * @covers ::maybe_seed
	 */
	public function test_maybe_seed_skips_when_already_current(): void {
		Functions\when( 'get_option' )->justReturn( Seeder::SEED_VERSION );
		Functions\expect( 'wp_insert_post' )->never();
		Functions\expect( 'update_option' )->never();

		( new Seeder() )->maybe_seed();
	}

	/**
	 * @covers ::maybe_seed
	 */
	public function test_maybe_seed_runs_and_records_version_when_stale(): void {
		Functions\when( 'get_option' )->justReturn( 0 );
		Functions\when( 'sanitize_title' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'esc_url_raw' )->returnArg();
		Functions\when( 'wp_insert_post' )->justReturn( 1 );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'update_post_meta' )->justReturn( true );
		Functions\expect( 'update_option' )->once()->with( Seeder::OPTION, Seeder::SEED_VERSION );

		// No existing posts -> every item is inserted.
		WP_Query::$next_posts = array();

		( new Seeder() )->maybe_seed();
	}

	/**
	 * @covers ::seed
	 */
	public function test_seed_creates_all_items_when_none_exist(): void {
		Functions\when( 'sanitize_title' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'esc_url_raw' )->returnArg();
		Functions\when( 'wp_insert_post' )->justReturn( 1 );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'update_post_meta' )->justReturn( true );

		WP_Query::$next_posts = array();

		$counts = ( new Seeder() )->seed();

		$this->assertSame( 15, $counts['created'] );
		$this->assertSame( 0, $counts['skipped'] );
		$this->assertSame( 0, $counts['failed'] );
	}

	/**
	 * @covers ::seed
	 */
	public function test_seed_is_idempotent_when_items_exist(): void {
		Functions\when( 'sanitize_title' )->returnArg();
		Functions\expect( 'wp_insert_post' )->never();

		// Any non-empty result makes the slug check report "exists".
		WP_Query::$next_posts = array( 1 );

		$counts = ( new Seeder() )->seed( 'pd_expertise' );

		$this->assertSame( 0, $counts['created'] );
		$this->assertSame( 6, $counts['skipped'] );
	}
}
