<?php
/**
 * Language switcher block: ploetner-dev/language-switcher.
 *
 * A minimal globe in the header. A click opens the list of languages that the
 * Multisite Language Switcher knows for this network. Built on <details> so it
 * works without JavaScript; a small script only closes it on outside click or
 * Escape.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

use lloc\PloetnerDevBlocks\Assets;
use lloc\PloetnerDevBlocks\Language\LanguageLinks;

/**
 * The Language switcher block.
 */
class LanguageSwitcher extends Block {

	public const NAME   = 'ploetner-dev/language-switcher';
	public const ICON   = 'translation';
	public const SCRIPT = 'ploetner-dev-language-switcher';

	/**
	 * Source of the language links.
	 *
	 * @var LanguageLinks
	 */
	private LanguageLinks $links;

	/**
	 * Constructor.
	 *
	 * @param LanguageLinks|null $links Source of the language links.
	 */
	public function __construct( ?LanguageLinks $links = null ) {
		$this->links = $links ?? new LanguageLinks();
	}

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'Language switcher', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( array $attributes ): string {
		$items = $this->links->items();

		// A switcher needs at least two languages.
		if ( count( $items ) < 2 ) {
			return '';
		}

		$this->enqueue_script();

		return $this->markup( $items );
	}

	/**
	 * Build the switcher markup.
	 *
	 * @param array<int, array{code: string, name: string, locale: string, url: string, current: bool}> $items Language entries.
	 *
	 * @return string
	 */
	public function markup( array $items ): string {
		$label = esc_attr( __( 'Choose language', 'ploetner-dev-blocks' ) );
		$icon  = self::globe();

		$list = '';
		foreach ( $items as $item ) {
			$current = $item['current'] ? ' aria-current="true"' : '';
			$list   .= sprintf(
				'<li><a href="%1$s" hreflang="%2$s" lang="%2$s"%3$s><span class="ploetner-lang__code">%4$s</span> %5$s</a></li>',
				esc_url( $item['url'] ),
				esc_attr( $item['locale'] ),
				$current,
				esc_html( $item['code'] ),
				esc_html( $item['name'] )
			);
		}

		return <<<HTML
<details class="ploetner-lang">
<summary class="ploetner-lang__toggle" aria-label="{$label}" title="{$label}">{$icon}</summary>
<ul class="ploetner-lang__list">{$list}</ul>
</details>
HTML;
	}

	/**
	 * Minimal globe icon (inline SVG, inherits the text colour).
	 *
	 * @return string
	 */
	public static function globe(): string {
		return '<svg class="ploetner-lang__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true" focusable="false">'
			. '<circle cx="12" cy="12" r="9"/>'
			. '<ellipse cx="12" cy="12" rx="4" ry="9"/>'
			. '<path d="M3 12h18M4.5 7.5h15M4.5 16.5h15"/>'
			. '</svg>';
	}

	/**
	 * Enqueue the tiny close-on-outside-click script (footer, deferred).
	 *
	 * @return void
	 */
	private function enqueue_script(): void {
		list( $url, $ver ) = Assets::asset( 'assets/language-switcher.js' );

		wp_enqueue_script(
			self::SCRIPT,
			$url,
			array(),
			$ver,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
