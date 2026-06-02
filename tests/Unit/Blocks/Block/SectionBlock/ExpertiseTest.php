<?php
/**
 * Tests for the Expertise block.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\Expertise;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use WP_Post;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\Expertise
 */
class ExpertiseTest extends TestCase {

	private Expertise $block;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', 'esc_attr', 'do_blocks', '__' ) );
		Functions\when( 'get_the_title' )->alias( static fn ( WP_Post $post ): string => $post->post_title );
		WP_Query::reset();
		$this->block = new Expertise();
	}

	/**
	 * @covers ::cell
	 */
	public function test_cell_contains_accent_marker_and_title(): void {
		$html = $this->block->cell( 'WordPress Multisite', '<p>desc</p>' );

		$this->assertStringContainsString( '▸', $html );
		$this->assertStringContainsString( 'WordPress Multisite', $html );
		$this->assertStringContainsString( '<p>desc</p>', $html );
	}

	/**
	 * @covers ::row
	 */
	public function test_first_row_has_full_border_others_do_not(): void {
		$first = $this->block->row( 'CELLS', true );
		$rest  = $this->block->row( 'CELLS', false );

		$this->assertStringContainsString( 'border-width:1px"', $first );
		$this->assertStringContainsString( 'border-width:0px 1px 1px 1px', $rest );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_chunks_posts_into_rows_of_three(): void {
		WP_Query::$next_posts = array(
			new WP_Post(
				array(
					'ID'           => 1,
					'post_title'   => 'A',
					'post_content' => '',
				)
			),
			new WP_Post(
				array(
					'ID'           => 2,
					'post_title'   => 'B',
					'post_content' => '',
				)
			),
			new WP_Post(
				array(
					'ID'           => 3,
					'post_title'   => 'C',
					'post_content' => '',
				)
			),
			new WP_Post(
				array(
					'ID'           => 4,
					'post_title'   => 'D',
					'post_content' => '',
				)
			),
		);

		$html = $this->block->render( array() );

		// 4 items => 2 rows (3 + 1).
		$this->assertSame( 2, substr_count( $html, 'ploetner-expertise-grid has-border-color' ) );
		foreach ( array( 'A', 'B', 'C', 'D' ) as $title ) {
			$this->assertStringContainsString( "▸</mark> {$title}", $html );
		}
	}

	/**
	 * @covers \lloc\PloetnerDevBlocks\Blocks\SectionBlock::render
	 */
	public function test_render_shows_empty_notice_without_posts(): void {
		WP_Query::$next_posts = array();

		$html = $this->block->render( array() );

		$this->assertStringContainsString( 'Add expertise items in the dashboard.', $html );
	}
}
