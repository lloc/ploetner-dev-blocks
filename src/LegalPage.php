<?php
/**
 * Legal notice and privacy policy as block markup (pattern content).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

/**
 * Builds the content of the "Legal" page: legal notice (Art. 7 D.Lgs. 70/2003)
 * and privacy policy (Art. 13 GDPR). All strings are translatable, contact
 * details come from Contact. The texts describe the current setup: server
 * logs, Cloudflare, technically necessary cookies only, local fonts.
 *
 * Layout (styled in assets/blocks.css, like the Speaking rows): numbered
 * section labels, the provider details as a key/value card and each privacy
 * topic as a row with its title on the left and the text on the right. Only
 * core blocks with class names, so the page stays editable.
 */
class LegalPage {

	/**
	 * Full page content.
	 *
	 * @return string
	 */
	public function content(): string {
		return self::group( 'ploetner-legal', $this->notice() . "\n\n" . $this->privacy() );
	}

	/**
	 * Legal notice / Impressum.
	 *
	 * @return string
	 */
	public function notice(): string {
		$rows = array(
			array( __( 'Provider', 'ploetner-dev-blocks' ), esc_html( Contact::NAME ) ),
			array( __( 'Address', 'ploetner-dev-blocks' ), esc_html( Contact::ADDRESS ) . '<br>' . esc_html( Contact::country() ) ),
			array( __( 'Email', 'ploetner-dev-blocks' ), self::mailto( Contact::EMAIL ) ),
			array( __( 'VAT number', 'ploetner-dev-blocks' ), esc_html( Contact::VAT ) ),
			array( __( 'Responsible for content', 'ploetner-dev-blocks' ), esc_html( Contact::NAME ) ),
		);

		$facts = array();
		foreach ( $rows as list( $key, $value ) ) {
			$facts[] = self::group(
				'ploetner-legal-row',
				self::paragraph( esc_html( $key ), 'ploetner-legal-key' ) . "\n" . self::paragraph( $value, 'ploetner-legal-value' )
			);
		}

		return implode(
			"\n\n",
			array(
				self::label( '01', __( 'Legal notice', 'ploetner-dev-blocks' ) ),
				self::heading( __( 'Legal notice', 'ploetner-dev-blocks' ), 'legal-notice' ),
				self::group( 'ploetner-legal-facts', implode( "\n", $facts ) ),
			)
		);
	}

	/**
	 * Privacy policy.
	 *
	 * @return string
	 */
	public function privacy(): string {
		$email   = self::mailto( Contact::EMAIL );
		$garante = '<a href="https://www.garanteprivacy.it/">Garante per la protezione dei dati personali</a>';

		return implode(
			"\n\n",
			array(
				self::label( '02', __( 'Privacy policy', 'ploetner-dev-blocks' ) ),
				self::heading( __( 'Privacy policy', 'ploetner-dev-blocks' ), 'privacy' ),

				self::section(
					__( 'Controller', 'ploetner-dev-blocks' ),
					sprintf(
						esc_html(
							/* translators: 1: name of the controller, 2: email address link. */
							__( 'The controller within the meaning of the GDPR is %1$s, address as in the legal notice above, email %2$s.', 'ploetner-dev-blocks' )
						),
						esc_html( Contact::NAME ),
						$email
					)
				),

				self::section(
					__( 'Visiting this website: server logs', 'ploetner-dev-blocks' ),
					esc_html( __( 'Each time you open a page, the web server records technical data: IP address, date and time, the requested address, the referring page, browser and operating system (user agent), and the status code. This is needed to deliver the website and to keep it secure (Art. 6(1)(f) GDPR, legitimate interest).', 'ploetner-dev-blocks' ) ),
					esc_html(
						sprintf(
							/* translators: 1: number of days, 2: hosting provider and country. */
							__( 'We delete the logs after %1$d days at the latest, unless we need to investigate an incident. The server is operated by %2$s, which processes the data on my behalf (Art. 28 GDPR).', 'ploetner-dev-blocks' ),
							Contact::LOG_DAYS,
							Contact::hosting()
						)
					)
				),

				self::section(
					__( 'Cloudflare', 'ploetner-dev-blocks' ),
					esc_html( __( 'The website is delivered through Cloudflare (Cloudflare, Inc., San Francisco, USA), which protects it against attacks and speeds up delivery. Cloudflare processes your IP address and technical request data and may set technically necessary cookies to distinguish people from bots. The legal basis is Art. 6(1)(f) GDPR. Cloudflare is certified under the EU-U.S. Data Privacy Framework; transfers to the USA are based on it.', 'ploetner-dev-blocks' ) )
				),

				self::section(
					__( 'Cookies and tracking', 'ploetner-dev-blocks' ),
					esc_html( __( 'This website only uses technically necessary cookies (see Cloudflare). There is no analytics, no tracking, and no advertising, which is why you do not see a cookie banner. Fonts are served from this server; no third-party content is loaded.', 'ploetner-dev-blocks' ) )
				),

				self::section(
					__( 'External links', 'ploetner-dev-blocks' ),
					esc_html( __( 'Links to other websites (e.g. GitHub, LinkedIn, Mastodon) only transfer data once you click them. From then on, the privacy policy of that website applies.', 'ploetner-dev-blocks' ) )
				),

				self::section(
					__( 'Contact by email', 'ploetner-dev-blocks' ),
					esc_html( __( 'If you write to me, I use your address and message only to answer and to handle your request (Art. 6(1)(b) and (f) GDPR). I delete them when they are no longer needed, unless legal retention periods apply.', 'ploetner-dev-blocks' ) )
				),

				self::section(
					__( 'Your rights', 'ploetner-dev-blocks' ),
					sprintf(
						esc_html(
							/* translators: %s: email address link. */
							__( 'You have the right to access, rectification, erasure, restriction of processing, data portability and to object to processing (Art. 15 to 21 GDPR). Just write to %s.', 'ploetner-dev-blocks' )
						),
						$email
					),
					sprintf(
						esc_html(
							/* translators: %s: name and link of the Italian data protection authority. */
							__( 'You can also lodge a complaint with a supervisory authority; in Italy, the %s.', 'ploetner-dev-blocks' )
						),
						$garante
					)
				),

				self::paragraph( esc_html( __( 'Last updated: September 2026', 'ploetner-dev-blocks' ) ), 'ploetner-legal-updated' ),
			)
		);
	}

	/**
	 * Numbered section label, like the front page sections ("01 / Legal notice").
	 *
	 * @param string $number Two-digit number.
	 * @param string $text   Label text.
	 *
	 * @return string
	 */
	private static function label( string $number, string $text ): string {
		$text = esc_html( $number . ' / ' . $text );

		return <<<HTML
<!-- wp:paragraph {"className":"ploetner-section-label","fontSize":"tiny","fontFamily":"mono","textColor":"accent"} -->
<p class="ploetner-section-label has-accent-color has-text-color has-mono-font-family has-tiny-font-size">{$text}</p>
<!-- /wp:paragraph -->
HTML;
	}

	/**
	 * Section heading (h2) with an anchor, sized like the front page sections.
	 *
	 * @param string $text   Plain text.
	 * @param string $anchor HTML id.
	 *
	 * @return string
	 */
	private static function heading( string $text, string $anchor ): string {
		$text   = esc_html( $text );
		$anchor = esc_attr( $anchor );

		return <<<HTML
<!-- wp:heading {"anchor":"{$anchor}","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" id="{$anchor}">{$text}</h2>
<!-- /wp:heading -->
HTML;
	}

	/**
	 * Privacy topic: title (h3) on the left, paragraphs on the right.
	 *
	 * @param string $title         Plain-text title.
	 * @param string ...$paragraphs Escaped inline HTML, one per paragraph.
	 *
	 * @return string
	 */
	private static function section( string $title, string ...$paragraphs ): string {
		$title = esc_html( $title );
		$text  = implode( "\n", array_map( static fn ( string $html ): string => self::paragraph( $html ), $paragraphs ) );

		$heading = <<<HTML
<!-- wp:heading {"level":3,"className":"ploetner-legal-title"} -->
<h3 class="wp-block-heading ploetner-legal-title">{$title}</h3>
<!-- /wp:heading -->
HTML;

		return self::group( 'ploetner-legal-section', $heading . "\n" . self::group( 'ploetner-legal-text', $text ) );
	}

	/**
	 * Group block with a class name.
	 *
	 * @param string $class_name CSS class.
	 * @param string $inner      Inner block markup.
	 *
	 * @return string
	 */
	private static function group( string $class_name, string $inner ): string {
		$class_name = esc_attr( $class_name );

		return "<!-- wp:group {\"className\":\"{$class_name}\"} -->\n<div class=\"wp-block-group {$class_name}\">\n{$inner}\n</div>\n<!-- /wp:group -->";
	}

	/**
	 * Paragraph block around already escaped inline HTML.
	 *
	 * @param string $html       Escaped inline HTML.
	 * @param string $class_name Optional CSS class.
	 *
	 * @return string
	 */
	private static function paragraph( string $html, string $class_name = '' ): string {
		if ( '' === $class_name ) {
			return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
		}

		$class_name = esc_attr( $class_name );

		return "<!-- wp:paragraph {\"className\":\"{$class_name}\"} -->\n<p class=\"{$class_name}\">{$html}</p>\n<!-- /wp:paragraph -->";
	}

	/**
	 * Mailto link.
	 *
	 * @param string $email Email address.
	 *
	 * @return string
	 */
	public static function mailto( string $email ): string {
		return sprintf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
	}
}
