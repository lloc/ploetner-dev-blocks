<?php
/**
 * Tests for the admin list table tweaks.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\PostTypes;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\PostTypes\AdminList;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use Mockery;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\PostTypes\AdminList
 */
class AdminListTest extends TestCase {

	private AdminList $list;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__', 'esc_html' ) );
		$this->list = new AdminList();
	}

	/**
	 * Build a query mock for the given post type and orderby.
	 *
	 * @param mixed $post_type Queried post type.
	 * @param mixed $orderby   Queried orderby.
	 * @param bool  $main      Whether it is the main query.
	 *
	 * @return WP_Query&\Mockery\MockInterface
	 */
	private function query( $post_type, $orderby, bool $main = true ) {
		$query = Mockery::mock( WP_Query::class );
		$query->shouldReceive( 'is_main_query' )->andReturn( $main );
		$query->shouldReceive( 'get' )->with( 'post_type' )->andReturn( $post_type );
		$query->shouldReceive( 'get' )->with( 'orderby' )->andReturn( $orderby );

		return $query;
	}

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_query_and_columns_for_every_post_type(): void {
		Functions\expect( 'add_action' )->once()->with( 'pre_get_posts', Mockery::type( 'array' ) );
		Functions\expect( 'add_action' )->times( 4 )->with( Mockery::pattern( '/^manage_pd_\w+_posts_custom_column$/' ), Mockery::any(), 10, 2 );
		Functions\expect( 'add_filter' )->times( 8 );

		$this->list->register();
	}

	/**
	 * @covers ::order
	 */
	public function test_order_defaults_to_menu_order_for_own_post_types(): void {
		Functions\when( 'is_admin' )->justReturn( true );

		$query = $this->query( 'pd_talk', '' );
		$query->shouldReceive( 'set' )->once()->with(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			)
		);

		$this->list->order( $query );
	}

	/**
	 * @covers ::order
	 */
	public function test_order_respects_explicit_orderby(): void {
		Functions\when( 'is_admin' )->justReturn( true );

		$query = $this->query( 'pd_talk', 'title' );
		$query->shouldNotReceive( 'set' );

		$this->list->order( $query );
	}

	/**
	 * @covers ::order
	 */
	public function test_order_ignores_other_post_types(): void {
		Functions\when( 'is_admin' )->justReturn( true );

		$query = $this->query( 'post', '' );
		$query->shouldNotReceive( 'set' );

		$this->list->order( $query );
	}

	/**
	 * @covers ::order
	 */
	public function test_order_ignores_front_end(): void {
		Functions\when( 'is_admin' )->justReturn( false );

		$query = Mockery::mock( WP_Query::class );
		$query->shouldNotReceive( 'set' );

		$this->list->order( $query );
	}

	/**
	 * @covers ::columns
	 */
	public function test_talk_columns_add_year_event_and_order_after_title(): void {
		Functions\stubs( array( 'sanitize_text_field' ) );

		$columns = $this->list->columns(
			array(
				'cb'    => '<input type="checkbox" />',
				'title' => 'Title',
				'date'  => 'Date',
			),
			'pd_talk'
		);

		$this->assertSame( array( 'cb', 'title', 'pd_talk_year', 'pd_talk_event', 'pd_order', 'date' ), array_keys( $columns ) );
		$this->assertSame( 'Year', $columns['pd_talk_year'] );
		$this->assertSame( 'Event', $columns['pd_talk_event'] );
	}

	/**
	 * @covers ::columns
	 */
	public function test_other_post_types_only_get_order_column(): void {
		$columns = $this->list->columns(
			array(
				'title' => 'Title',
				'date'  => 'Date',
			),
			'pd_community'
		);

		$this->assertSame( array( 'title', 'pd_order', 'date' ), array_keys( $columns ) );
	}

	/**
	 * @covers ::sortable_columns
	 */
	public function test_order_column_is_sortable_by_menu_order(): void {
		$this->assertSame( array( 'menu_order', false ), $this->list->sortable_columns( array() )['pd_order'] );
	}

	/**
	 * @covers ::render_column
	 */
	public function test_render_column_outputs_meta_and_order(): void {
		Functions\expect( 'get_post_meta' )->once()->with( 7, '_pd_talk_event', true )->andReturn( 'WordCamp Europe' );
		Functions\expect( 'get_post_field' )->once()->with( 'menu_order', 7 )->andReturn( 3 );

		$this->expectOutputString( 'WordCamp Europe3' );

		$this->list->render_column( 'pd_talk_event', 7 );
		$this->list->render_column( 'pd_order', 7 );
	}
}
