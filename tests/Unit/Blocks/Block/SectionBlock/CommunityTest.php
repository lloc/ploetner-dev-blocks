<?php
/**
 * Tests for the Community block.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\Community;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use WP_Post;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\Community
 */
class CommunityTest extends TestCase {

	private Community $block;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', 'esc_attr', 'do_blocks', '__' ) );
		Functions\when( 'get_the_title' )->alias( static fn ( WP_Post $post ): string => $post->post_title );
		WP_Query::reset();
		$this->block = new Community();
	}

	/**
	 * @covers ::community_card
	 */
	public function test_card_includes_label_title_and_description(): void {
		$html = $this->block->community_card( 'Meetup Organizer', 'Milan', '<p>desc</p>' );

		$this->assertStringContainsString( 'Meetup Organizer', $html );
		$this->assertStringContainsString( 'Milan', $html );
		$this->assertStringContainsString( '<p>desc</p>', $html );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_outputs_a_card_per_post(): void {
		WP_Query::$next_posts = array(
			new WP_Post(
				array(
					'ID'           => 1,
					'post_title'   => 'Milan',
					'post_content' => '',
				)
			),
			new WP_Post(
				array(
					'ID'           => 2,
					'post_title'   => 'ScuolaWP',
					'post_content' => '',
				)
			),
		);

		Functions\when( 'get_post_meta' )->justReturn( 'Label' );

		$html = $this->block->render( array() );

		$this->assertSame( 2, substr_count( $html, 'ploetner-card has-border-color' ) );
		$this->assertStringContainsString( 'Milan', $html );
		$this->assertStringContainsString( 'ScuolaWP', $html );
	}
}
