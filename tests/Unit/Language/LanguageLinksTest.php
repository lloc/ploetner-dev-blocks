<?php
/**
 * Tests for the MLS language links.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit\Language;

use lloc\PloetnerDevBlocks\Language\LanguageLinks;
use lloc\PloetnerDevBlocks\Tests\TestCase;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Language\LanguageLinks
 */
class LanguageLinksTest extends TestCase {

	/**
	 * @covers ::available
	 * @covers ::items
	 */
	public function test_without_mls_there_are_no_items(): void {
		$links = new LanguageLinks();

		$this->assertFalse( $links->available() );
		$this->assertSame( array(), $links->items() );
	}
}
