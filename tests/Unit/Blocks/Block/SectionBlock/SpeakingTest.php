<?php
/**
 * Tests for the Speaking block.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\Speaking;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use WP_Post;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\Speaking
 */
class SpeakingTest extends TestCase {

	private Speaking $block;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', 'esc_attr', 'do_blocks', '__' ) );
		Functions\when( 'get_the_title' )->alias( static fn ( WP_Post $post ): string => $post->post_title );
		WP_Query::reset();
		$this->block = new Speaking();
	}

	/**
	 * @covers ::speaking_row
	 */
	public function test_last_row_gets_bottom_border(): void {
		$row  = $this->block->speaking_row( '2026', 'Talk', 'WordCamp', false );
		$last = $this->block->speaking_row( '2026', 'Talk', 'WordCamp', true );

		$this->assertStringNotContainsString( 'border-bottom-width:1px', $row );
		$this->assertStringContainsString( 'border-bottom-width:1px', $last );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_marks_only_the_last_row(): void {
		WP_Query::$next_posts = array(
			new WP_Post(
				array(
					'ID'         => 1,
					'post_title' => 'First',
				)
			),
			new WP_Post(
				array(
					'ID'         => 2,
					'post_title' => 'Last',
				)
			),
		);

		Functions\when( 'get_post_meta' )->justReturn( '2026' );

		$html = $this->block->render( array() );

		$this->assertSame( 1, substr_count( $html, 'border-bottom-width:1px' ) );
		$this->assertStringContainsString( 'First', $html );
		$this->assertStringContainsString( 'Last', $html );
	}
}
