<?php
/**
 * CTA Banner block — ploetner-dev/cta-banner.
 *
 * A single-instance call-to-action (no CPT): heading, paragraph, and one button.
 * All content lives in block attributes (auto-generated Inspector Controls).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

/**
 * The CTA Banner block.
 */
class CtaBanner extends Block {

	public const NAME = 'ploetner-dev/cta-banner';
	public const ICON = 'megaphone';

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'CTA Banner', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return array(
			'heading'    => array(
				'default' => 'Need a WordPress technical audit?',
				'label'   => __( 'Heading', 'ploetner-dev-blocks' ),
			),
			'text'       => array(
				'default' => 'I help enterprise teams find and fix critical issues in their WordPress infrastructure. Fast turnaround, structured results.',
				'label'   => __( 'Text', 'ploetner-dev-blocks' ),
			),
			'buttonText' => array(
				'default' => 'Learn more at ploetner.cloud →',
				'label'   => __( 'Button text', 'ploetner-dev-blocks' ),
			),
			'buttonUrl'  => array(
				'default' => 'https://ploetner.cloud',
				'label'   => __( 'Button URL', 'ploetner-dev-blocks' ),
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( array $attributes ): string {
		$heading     = esc_html( (string) ( $attributes['heading'] ?? '' ) );
		$text        = esc_html( (string) ( $attributes['text'] ?? '' ) );
		$button_text = esc_html( (string) ( $attributes['buttonText'] ?? '' ) );
		$button_url  = esc_url( (string) ( $attributes['buttonUrl'] ?? '' ) );

		$markup = <<<HTML
<!-- wp:group {"align":"wide","style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"borderColor":"accent","backgroundColor":"accent-dim","layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group alignwide has-border-color has-accent-border-color has-accent-dim-background-color has-background" style="border-width:1px;margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50)">
<!-- wp:heading {"textAlign":"center","style":{"typography":{"letterSpacing":"-0.01em"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="letter-spacing:-0.01em">{$heading}</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontWeight":"300"}},"textColor":"text-muted"} -->
<p class="has-text-align-center has-text-muted-color has-text-color" style="font-weight:300">{$text}</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"accent","textColor":"base","style":{"typography":{"letterSpacing":"0.03em","fontStyle":"normal","fontWeight":"500"},"border":{"radius":"2px"}},"fontFamily":"mono","fontSize":"small"} -->
<div class="wp-block-button has-custom-font-size has-mono-font-family has-small-font-size" style="font-style:normal;font-weight:500;letter-spacing:0.03em"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" href="{$button_url}" style="border-radius:2px">{$button_text}</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
HTML;

		return do_blocks( $markup );
	}
}
