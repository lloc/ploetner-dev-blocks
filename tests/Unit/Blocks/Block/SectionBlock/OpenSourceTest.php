<?php
/**
 * Tests for the Open Source block.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\OpenSource;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use WP_Post;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\OpenSource
 */
class OpenSourceTest extends TestCase {

	private OpenSource $block;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', 'esc_attr', 'esc_url', 'do_blocks', '__' ) );
		Functions\when( 'get_the_title' )->alias( static fn ( WP_Post $post ): string => $post->post_title );
		WP_Query::reset();
		$this->block = new OpenSource();
	}

	/**
	 * @covers ::project_card
	 */
	public function test_card_includes_tech_and_link_when_present(): void {
		$html = $this->block->project_card( 'MSLS', '<p>desc</p>', 'WordPress · i18n', 'https://example.test', 'View ↗' );

		$this->assertStringContainsString( 'MSLS', $html );
		$this->assertStringContainsString( 'WordPress · i18n', $html );
		$this->assertStringContainsString( 'href="https://example.test"', $html );
		$this->assertStringContainsString( 'View ↗', $html );
	}

	/**
	 * @covers ::project_card
	 */
	public function test_card_omits_optional_blocks_when_empty(): void {
		$html = $this->block->project_card( 'MSLS', '<p>desc</p>', '', '', '' );

		$this->assertStringNotContainsString( 'letter-spacing:0.05em', $html );
		$this->assertStringNotContainsString( '<a href=', $html );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_reads_meta_for_each_post(): void {
		WP_Query::$next_posts = array(
			new WP_Post(
				array(
					'ID'           => 11,
					'post_title'   => 'Project',
					'post_content' => '',
				)
			),
		);

		Functions\expect( 'get_post_meta' )
			->with( 11, \Mockery::type( 'string' ), true )
			->andReturnUsing(
				static function ( int $id, string $key ): string {
					$map = array(
						'_pd_project_tech'      => 'PHP',
						'_pd_project_url'       => 'https://example.test',
						'_pd_project_link_text' => 'Go',
					);

					return $map[ $key ] ?? '';
				}
			);

		$html = $this->block->render( array() );

		$this->assertStringContainsString( 'PHP', $html );
		$this->assertStringContainsString( 'https://example.test', $html );
		$this->assertStringContainsString( 'Go', $html );
	}
}
