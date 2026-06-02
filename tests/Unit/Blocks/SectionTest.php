<?php
/**
 * Tests for the Section helper.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\Section;
use lloc\PloetnerDevBlocks\Tests\TestCase;
use WP_Post;
use WP_Query;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\Section
 */
class SectionTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', 'esc_attr', 'do_blocks', '__' ) );
		WP_Query::reset();
	}

	/**
	 * @covers ::attributes
	 */
	public function test_attributes_shape(): void {
		$attributes = Section::attributes( 'L', 'H', 'I' );

		$this->assertSame( array( 'sectionLabel', 'heading', 'intro', 'maxItems' ), array_keys( $attributes ) );
		$this->assertSame( 'L', $attributes['sectionLabel']['default'] );
		$this->assertSame( 'H', $attributes['heading']['default'] );
		$this->assertSame( 'I', $attributes['intro']['default'] );
		$this->assertSame( 0, $attributes['maxItems']['default'] );
	}

	/**
	 * @covers ::empty_notice
	 */
	public function test_empty_notice_wraps_escaped_text(): void {
		$html = Section::empty_notice( 'Nothing here' );

		$this->assertStringContainsString( 'wp:paragraph', $html );
		$this->assertStringContainsString( 'Nothing here', $html );
	}

	/**
	 * @covers ::wrap
	 */
	public function test_wrap_injects_all_parts(): void {
		$html = Section::wrap( 'My label', 'My heading', 'my-anchor', 'My intro', '<!-- body -->' );

		$this->assertStringContainsString( 'My label', $html );
		$this->assertStringContainsString( 'My heading', $html );
		$this->assertStringContainsString( 'id="my-anchor"', $html );
		$this->assertStringContainsString( 'My intro', $html );
		$this->assertStringContainsString( '<!-- body -->', $html );
	}

	/**
	 * @covers ::items
	 */
	public function test_items_returns_query_posts(): void {
		$post                 = new WP_Post( array( 'ID' => 7 ) );
		WP_Query::$next_posts = array( $post );

		$result = Section::items( 'pd_expertise', 5 );

		$this->assertSame( array( $post ), $result );

		$args = WP_Query::$captured_args[0];
		$this->assertSame( 'pd_expertise', $args['post_type'] );
		$this->assertSame( 'publish', $args['post_status'] );
		$this->assertSame( 5, $args['posts_per_page'] );
	}

	/**
	 * @covers ::items
	 */
	public function test_items_unbounded_uses_minus_one(): void {
		Section::items( 'pd_talk', 0 );

		$this->assertSame( -1, WP_Query::$captured_args[0]['posts_per_page'] );
	}
}
