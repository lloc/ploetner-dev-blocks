<?php
/**
 * Tests for the legal page content.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Tests\Unit;

use Brain\Monkey\Functions;
use lloc\PloetnerDevBlocks\Contact;
use lloc\PloetnerDevBlocks\LegalPage;
use lloc\PloetnerDevBlocks\Tests\TestCase;

/**
 * @coversDefaultClass \lloc\PloetnerDevBlocks\LegalPage
 */
class LegalPageTest extends TestCase {

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubs( array( '__', 'esc_html', 'esc_attr' ) );
	}

	/**
	 * @covers ::content
	 * @covers ::notice
	 */
	public function test_notice_contains_mandatory_provider_details(): void {
		$html = ( new LegalPage() )->notice();

		$this->assertStringContainsString( Contact::NAME, $html );
		$this->assertStringContainsString( Contact::ADDRESS, $html );
		$this->assertStringContainsString( 'mailto:' . Contact::EMAIL, $html );
		$this->assertStringContainsString( Contact::VAT, $html );
	}

	/**
	 * @covers ::privacy
	 */
	public function test_privacy_covers_logs_cloudflare_cookies_and_rights(): void {
		$html = ( new LegalPage() )->privacy();

		foreach ( array( 'server logs', 'Cloudflare', 'technically necessary cookies', 'Art. 15 to 21 GDPR', 'garanteprivacy.it', Contact::HOSTING, 'Frankfurt am Main' ) as $needle ) {
			$this->assertStringContainsString( $needle, $html );
		}
		$this->assertStringContainsString( (string) Contact::LOG_DAYS . ' days', $html );
	}

	/**
	 * @covers ::content
	 */
	public function test_content_is_balanced_block_markup(): void {
		$html = ( new LegalPage() )->content();

		foreach ( array( 'group', 'heading', 'paragraph' ) as $block ) {
			$this->assertSame(
				preg_match_all( '/<!-- wp:' . $block . '[ -]/', $html ),
				substr_count( $html, '<!-- /wp:' . $block . ' -->' ),
				"Unbalanced {$block} blocks"
			);
		}
		$this->assertSame( substr_count( $html, '<div' ), substr_count( $html, '</div>' ) );
	}

	/**
	 * @covers ::content
	 * @covers ::notice
	 * @covers ::privacy
	 */
	public function test_content_uses_the_styled_layout(): void {
		$html = ( new LegalPage() )->content();

		$this->assertStringStartsWith( '<!-- wp:group {"className":"ploetner-legal"} -->', $html );
		$this->assertSame( 5, substr_count( $html, '<div class="wp-block-group ploetner-legal-row">' ) );
		$this->assertSame( 7, substr_count( $html, '<div class="wp-block-group ploetner-legal-section">' ) );
		$this->assertStringContainsString( '01 / Legal notice', $html );
		$this->assertStringContainsString( '02 / Privacy policy', $html );
		$this->assertStringContainsString( 'id="legal-notice"', $html );
		$this->assertStringContainsString( 'id="privacy"', $html );
	}
}
