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
 */
class LegalPage {

	/**
	 * Full page content.
	 *
	 * @return string
	 */
	public function content(): string {
		return implode(
			"\n\n",
			array(
				$this->notice(),
				$this->privacy(),
			)
		);
	}

	/**
	 * Legal notice / Impressum.
	 *
	 * @return string
	 */
	public function notice(): string {
		$email = self::mailto( Contact::EMAIL );

		return implode(
			"\n\n",
			array(
				self::heading( __( 'Legal notice', 'ploetner-dev-blocks' ) ),
				self::paragraph(
					esc_html( Contact::NAME ) . '<br>'
					. esc_html( Contact::ADDRESS ) . '<br>'
					. esc_html( Contact::country() )
				),
				self::paragraph(
					/* translators: %s: email address link. */
					sprintf( esc_html( __( 'Email: %s', 'ploetner-dev-blocks' ) ), $email ) . '<br>'
					/* translators: %s: VAT number. */
					. esc_html( sprintf( __( 'VAT number (Partita IVA): %s', 'ploetner-dev-blocks' ), Contact::VAT ) )
				),
				self::paragraph(
					esc_html( __( 'Responsible for the content of this website: Dennis Plötner, address as above.', 'ploetner-dev-blocks' ) )
				),
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
				self::heading( __( 'Privacy policy', 'ploetner-dev-blocks' ) ),

				self::heading( __( 'Controller', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					/* translators: %s: email address link. */
					sprintf( esc_html( __( 'The controller within the meaning of the GDPR is Dennis Plötner, address as in the legal notice above, email %s.', 'ploetner-dev-blocks' ) ), $email )
				),

				self::heading( __( 'Visiting this website: server logs', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					esc_html( __( 'Each time you open a page, the web server records technical data: IP address, date and time, the requested address, the referring page, browser and operating system (user agent) and the status code. This is needed to deliver the website and to keep it secure (Art. 6(1)(f) GDPR, legitimate interest).', 'ploetner-dev-blocks' ) )
				),
				self::paragraph(
					esc_html(
						sprintf(
							/* translators: 1: number of days, 2: hosting provider and country. */
							__( 'The logs are deleted after %1$d days at the latest, unless an incident has to be investigated. The server is operated by %2$s, which processes the data on my behalf (Art. 28 GDPR).', 'ploetner-dev-blocks' ),
							Contact::LOG_DAYS,
							Contact::hosting()
						)
					)
				),

				self::heading( __( 'Cloudflare', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					esc_html( __( 'The website is delivered through Cloudflare (Cloudflare, Inc., San Francisco, USA), which protects it against attacks and speeds up delivery. Cloudflare processes your IP address and technical request data and may set technically necessary cookies to tell people from bots. Legal basis is Art. 6(1)(f) GDPR. Cloudflare is certified under the EU-U.S. Data Privacy Framework; transfers to the USA are based on it.', 'ploetner-dev-blocks' ) )
				),

				self::heading( __( 'Cookies and tracking', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					esc_html( __( 'This website only uses technically necessary cookies (see Cloudflare). There is no analytics, no tracking and no advertising, which is why you do not see a cookie banner. Fonts are served from this server; no third-party content is loaded.', 'ploetner-dev-blocks' ) )
				),

				self::heading( __( 'External links', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					esc_html( __( 'Links to other websites (e.g. GitHub, LinkedIn, Mastodon) only transfer data once you click them. From then on, the privacy policy of that website applies.', 'ploetner-dev-blocks' ) )
				),

				self::heading( __( 'Contact by email', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					esc_html( __( 'If you write to me, I use your address and message only to answer and to handle your request (Art. 6(1)(b) and (f) GDPR). I delete them when they are no longer needed, unless legal retention periods apply.', 'ploetner-dev-blocks' ) )
				),

				self::heading( __( 'Your rights', 'ploetner-dev-blocks' ), 3 ),
				self::paragraph(
					/* translators: %s: email address link. */
					sprintf( esc_html( __( 'You have the right to access, rectification, erasure, restriction of processing, data portability and to object to processing (Art. 15 to 21 GDPR). Just write to %s.', 'ploetner-dev-blocks' ) ), $email )
				),
				self::paragraph(
					/* translators: %s: name and link of the Italian data protection authority. */
					sprintf( esc_html( __( 'You can also lodge a complaint with a supervisory authority, in Italy the %s.', 'ploetner-dev-blocks' ) ), $garante )
				),

				self::paragraph(
					'<em>' . esc_html( __( 'Last updated: September 2026', 'ploetner-dev-blocks' ) ) . '</em>'
				),
			)
		);
	}

	/**
	 * Heading block.
	 *
	 * @param string $text  Plain text.
	 * @param int    $level Heading level.
	 *
	 * @return string
	 */
	private static function heading( string $text, int $level = 2 ): string {
		$text  = esc_html( $text );
		$attrs = 2 === $level ? '' : ' {"level":' . $level . '}';

		return "<!-- wp:heading{$attrs} -->\n<h{$level} class=\"wp-block-heading\">{$text}</h{$level}>\n<!-- /wp:heading -->";
	}

	/**
	 * Paragraph block around already escaped inline HTML.
	 *
	 * @param string $html Escaped inline HTML.
	 *
	 * @return string
	 */
	private static function paragraph( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
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
