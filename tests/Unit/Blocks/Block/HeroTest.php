<?php
/**
 * Tests for the Hero block.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Blocks;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Blocks\Hero;
use lloc\PloetnerDevBlocks\Tests\TestCase;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Blocks\Hero
 */
class HeroTest extends TestCase {

	private Hero $hero;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( 'esc_html', 'esc_url', 'do_blocks', '__' ) );
		$this->hero = new Hero();
	}

	/**
	 * @covers ::name
	 * @covers ::defaults
	 */
	public function test_block_identity(): void {
		$this->assertSame( 'ploetner-dev/hero', $this->hero->name() );
		$this->assertArrayHasKey( 'tagline', $this->hero->defaults() );
		$this->assertArrayHasKey( 'meta4Value', $this->hero->defaults() );
	}

	/**
	 * @covers ::heading
	 */
	public function test_heading_joins_lines_and_highlights_name(): void {
		$html = $this->hero->heading( 'Born in Germany.', 'Reborn in Italy.', 'I’m', 'Dennis.' );

		$this->assertStringContainsString( 'Born in Germany.<br>Reborn in Italy.', $html );
		$this->assertStringContainsString( 'I’m <mark', $html );
		$this->assertStringContainsString( 'Dennis.', $html );
	}

	/**
	 * @covers ::heading
	 */
	public function test_heading_skips_empty_segments(): void {
		$html = $this->hero->heading( 'Only line.', '', '', '' );

		$this->assertSame( 'Only line.', $html );
		$this->assertStringNotContainsString( '<mark', $html );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_skips_empty_meta_columns(): void {
		$html = $this->hero->render(
			array(
				'tagline'    => 'Engineer',
				'meta1Label' => 'Role',
				'meta1Value' => 'Dev',
				'meta2Label' => '',
				'meta2Value' => '',
			)
		);

		$this->assertStringContainsString( 'Engineer', $html );
		$this->assertStringContainsString( 'Role', $html );
		$this->assertStringContainsString( 'wp:columns', $html );
		$this->assertSame( 1, substr_count( $html, '<!-- wp:column -->' ) );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_without_meta_omits_columns(): void {
		$html = $this->hero->render( array( 'tagline' => 'Engineer' ) );

		$this->assertStringNotContainsString( 'wp:columns', $html );
	}
}
