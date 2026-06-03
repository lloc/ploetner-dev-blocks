<?php
/**
 * Speaking block — ploetner-dev/speaking.
 *
 * Timeline rows from the `pd_talk` post type: year + talk title + event.
 * Title = post title, year/event come from post meta. The last row gets an
 * extra bottom border to close the timeline (matching the original pattern).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

/**
 * The Speaking block.
 */
class Speaking extends SectionBlock {

	public const NAME      = 'ploetner-dev/speaking';
	public const ICON      = 'microphone';
	public const POST_TYPE = 'pd_talk';
	public const ANCHOR    = 'speaking';

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'Speaking', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return Section::attributes(
			'03 / Speaking',
			'Talks & appearances',
			'Sharing knowledge at WordCamps and meetups across Europe and the US.'
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function empty_message(): string {
		return __( 'Add talks in the dashboard.', 'ploetner-dev-blocks' );
	}

	/**
	 * Build a single speaking timeline row.
	 *
	 * @param string $year  Talk year.
	 * @param string $title Talk title.
	 * @param string $event Event name.
	 * @param bool   $last  Whether this is the last row (adds a bottom border).
	 *
	 * @return string
	 */
	public function speaking_row( string $year, string $title, string $event, bool $last ): string {
		$year  = esc_html( $year );
		$title = esc_html( $title );
		$event = esc_html( $event );

		if ( $last ) {
			$attrs = '{"className":"ploetner-speaking-row","style":{"border":{"top":{"color":"var:preset|color|border","width":"1px"},"bottom":{"color":"var:preset|color|border","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"default"}}';
			$style = 'border-top-color:var(--wp--preset--color--border);border-top-width:1px;border-bottom-color:var(--wp--preset--color--border);border-bottom-width:1px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)';
		} else {
			$attrs = '{"className":"ploetner-speaking-row","style":{"border":{"top":{"color":"var:preset|color|border","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"default"}}';
			$style = 'border-top-color:var(--wp--preset--color--border);border-top-width:1px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)';
		}

		return <<<HTML
<!-- wp:group {$attrs} -->
<div class="wp-block-group ploetner-speaking-row" style="{$style}">
<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column {"width":"120px"} -->
<div class="wp-block-column" style="flex-basis:120px">
<!-- wp:paragraph {"fontFamily":"mono","fontSize":"small","textColor":"text-dim"} -->
<p class="has-text-dim-color has-text-color has-mono-font-family has-small-font-size">{$year}</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">{$title}</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column {"width":"220px"} -->
<div class="wp-block-column" style="flex-basis:220px">
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"300"}},"fontSize":"small","textColor":"text-muted"} -->
<p class="has-text-muted-color has-text-color has-small-font-size" style="font-weight:300">{$event}</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
HTML;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function render_body( array $posts ): string {
		$body  = '';
		$count = count( $posts );
		foreach ( array_values( $posts ) as $index => $post ) {
			$body .= $this->speaking_row(
				(string) get_post_meta( $post->ID, '_pd_talk_year', true ),
				get_the_title( $post ),
				(string) get_post_meta( $post->ID, '_pd_talk_event', true ),
				$index === $count - 1
			);
		}

		return $body;
	}
}
