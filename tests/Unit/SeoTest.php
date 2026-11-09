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
use lloc\SEO\Meta\Description as MetaDescription;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\Seo
 */
class SeoTest extends TestCase {

	/**
	 * Seo with the real meta description.
	 *
	 * @return Seo
	 */
	private function seo(): Seo {
		return new Seo( new MetaDescription() );
	}

	/**
	 * @covers ::__construct
	 * @covers ::register
	 */
	public function test_register_hooks_filters_and_cleans_up_head(): void {
		Functions\expect( 'remove_action' )->once()->with( 'wp_head', 'wp_generator' );
		Functions\expect( 'remove_action' )->times( 6 ); // The other removals in CleanUp.

		$this->seo()->register();

		$this->assertNotFalse( has_filter( MetaDescription::META_DESCRIPTION_FILTER, Seo::class . '->description()' ) );
		$this->assertNotFalse( has_filter( 'wpseo_metadesc', MetaDescription::class . '->fill()' ) );
		$this->assertNotFalse( has_filter( 'msls_output_get_alternate_links_arr', Seo::class . '->x_default()' ) );
		$this->assertNotFalse( has_filter( 'wpseo_schema_person', Seo::class . '->person()' ) );
		$this->assertNotFalse( has_filter( 'the_generator', '__return_empty_string' ) );
	}

	/**
	 * @covers ::description
	 */
	public function test_description(): void {
		Functions\stubs( array( 'wp_strip_all_tags', '__' ) );
		Functions\when( '_x' )->returnArg();
		Functions\when( 'is_page' )->alias( static fn ( $slug ): bool => 'legal' === $slug );
		$seo = $this->seo();

		Functions\when( 'is_front_page' )->justReturn( true );
		Functions\when( 'get_bloginfo' )->justReturn( ' Enterprise WordPress, multisite & open source ' );
		$this->assertSame( 'Enterprise WordPress, multisite & open source', $seo->description( '' ) );

		Functions\when( 'is_front_page' )->justReturn( false );
		$this->assertStringStartsWith( 'Legal notice and privacy policy', $seo->description( '' ) );

		Functions\when( 'is_page' )->justReturn( false );
		$this->assertSame( 'Kept', $seo->description( 'Kept' ) );
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

		$result = $this->seo()->x_default( $links );

		$this->assertCount( 4, $result );
		$this->assertSame( '<link rel="alternate" href="https://ploetner.dev/" hreflang="x-default" />', $result[3] );
	}

	/**
	 * @covers ::x_default
	 */
	public function test_x_default_is_not_added_twice_or_without_english(): void {
		$seo  = $this->seo();
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
		$data = $this->seo()->person(
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
		$data = $this->seo()->person( array( 'name' => 'realloc' ) );

		$this->assertSame( array( Contact::NAME, 'Dennis Plötner' ), $data['alternateName'] );
	}
}
