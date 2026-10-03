<?php
/**
 * Tests for the SEO head fixes.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Contact;
use lloc\PloetnerDevBlocks\Seo;
use lloc\PloetnerDevBlocks\Tests\TestCase;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Seo
 */
class SeoTest extends TestCase {

	/**
	 * @covers ::register
	 */
	public function test_register_hooks_filters_and_drops_generator(): void {
		Functions\expect( 'remove_action' )->once()->with( 'wp_head', 'wp_generator' );

		( new Seo() )->register();

		$this->assertNotFalse( has_filter( 'msls_output_get_alternate_links_arr', Seo::class . '->x_default()' ) );
		$this->assertNotFalse( has_filter( 'wpseo_schema_person', Seo::class . '->person()' ) );
		$this->assertNotFalse( has_filter( 'the_generator', '__return_empty_string' ) );
	}

	/**
	 * @covers ::x_default
	 */
	public function test_x_default_duplicates_the_english_link(): void {
		$links = array(
			'<link rel="alternate" href="https://ploetner.dev/de/" hreflang="de" />',
			'<link rel="alternate" href="https://ploetner.dev/" hreflang="en" />',
			'<link rel="alternate" href="https://ploetner.dev/it/" hreflang="it" />',
		);

		$result = ( new Seo() )->x_default( $links );

		$this->assertCount( 4, $result );
		$this->assertSame( '<link rel="alternate" href="https://ploetner.dev/" hreflang="x-default" />', $result[3] );
	}

	/**
	 * @covers ::x_default
	 */
	public function test_x_default_is_not_added_twice_or_without_english(): void {
		$seo  = new Seo();
		$with = array(
			'<link rel="alternate" href="https://ploetner.dev/" hreflang="en" />',
			'<link rel="alternate" href="https://ploetner.dev/" hreflang="x-default" />',
		);
		$this->assertSame( $with, $seo->x_default( $with ) );

		$without = array( '<link rel="alternate" href="https://ploetner.dev/de/" hreflang="de" />' );
		$this->assertSame( $without, $seo->x_default( $without ) );
		$this->assertSame( 'not an array', $seo->x_default( 'not an array' ) );
	}

	/**
	 * @covers ::person
	 */
	public function test_person_gets_alternate_names_and_profiles(): void {
		$data = ( new Seo() )->person(
			array(
				'name'   => Contact::NAME,
				'sameAs' => array( 'https://ploetner.cloud', 'https://github.com/lloc' ),
			)
		);

		$this->assertSame( Contact::ALTERNATE_NAMES, $data['alternateName'] );
		$this->assertContains( 'https://ploetner.cloud', $data['sameAs'] );
		$this->assertContains( 'https://mastodon.social/@realloc', $data['sameAs'] );
		$this->assertSame( count( $data['sameAs'] ), count( array_unique( $data['sameAs'] ) ) );
	}

	/**
	 * @covers ::person
	 */
	public function test_person_lists_the_registered_name_when_yoast_uses_another(): void {
		$data = ( new Seo() )->person( array( 'name' => 'realloc' ) );

		$this->assertSame( array( Contact::NAME, 'Dennis Plötner' ), $data['alternateName'] );
	}
}
