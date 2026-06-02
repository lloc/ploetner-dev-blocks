<?php
/**
 * Hero block — ploetner-dev/hero.
 *
 * A single-instance section (no CPT): tagline, an h1 with line breaks and an
 * accent highlight, a phonetic line, a bio paragraph, and four meta columns.
 * All content lives in block attributes (auto-generated Inspector Controls).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

/**
 * The Hero block.
 */
class Hero extends Block {

	public const NAME = 'ploetner-dev/hero';
	public const ICON = 'admin-users';

	/**
	 * {@inheritDoc}
	 */
	public function title(): string {
		return __( 'Hero', 'ploetner-dev-blocks' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaults(): array {
		return array(
			'tagline'      => array(
				'default' => 'Senior Web Engineer',
				'label'   => __( 'Tagline', 'ploetner-dev-blocks' ),
			),
			'headingLine1' => array(
				'default' => 'Born in Germany.',
				'label'   => __( 'Heading line 1', 'ploetner-dev-blocks' ),
			),
			'headingLine2' => array(
				'default' => 'Reborn in Italy.',
				'label'   => __( 'Heading line 2', 'ploetner-dev-blocks' ),
			),
			'headingLead'  => array(
				'default' => 'I’m',
				'label'   => __( 'Heading lead-in', 'ploetner-dev-blocks' ),
			),
			'headingName'  => array(
				'default' => 'Dennis Plötner.',
				'label'   => __( 'Highlighted name', 'ploetner-dev-blocks' ),
			),
			'phonetic'     => array(
				'default' => '/ˈdenɪs ˈplœtnɐ/',
				'label'   => __( 'Phonetic', 'ploetner-dev-blocks' ),
			),
			'bio'          => array(
				'default' => 'I build enterprise WordPress solutions, maintain open-source projects used by thousands, and help teams ship robust, scalable architectures. Programming professionally since 1997. Speaking at WordCamps across Europe and the US.',
				'label'   => __( 'Bio', 'ploetner-dev-blocks' ),
			),
			'meta1Label'   => array(
				'default' => 'Current role',
				'label'   => __( 'Meta 1 label', 'ploetner-dev-blocks' ),
			),
			'meta1Value'   => array(
				'default' => 'Senior Web Engineer at Syde',
				'label'   => __( 'Meta 1 value', 'ploetner-dev-blocks' ),
			),
			'meta2Label'   => array(
				'default' => 'Based in',
				'label'   => __( 'Meta 2 label', 'ploetner-dev-blocks' ),
			),
			'meta2Value'   => array(
				'default' => 'Italy',
				'label'   => __( 'Meta 2 value', 'ploetner-dev-blocks' ),
			),
			'meta3Label'   => array(
				'default' => 'Languages',
				'label'   => __( 'Meta 3 label', 'ploetner-dev-blocks' ),
			),
			'meta3Value'   => array(
				'default' => 'DE · IT · EN',
				'label'   => __( 'Meta 3 value', 'ploetner-dev-blocks' ),
			),
			'meta4Label'   => array(
				'default' => 'Building since',
				'label'   => __( 'Meta 4 label', 'ploetner-dev-blocks' ),
			),
			'meta4Value'   => array(
				'default' => '1997',
				'label'   => __( 'Meta 4 value', 'ploetner-dev-blocks' ),
			),
		);
	}

	/**
	 * Compose the h1 inner HTML from the heading attributes.
	 *
	 * Lines are joined with <br>; the lead text + highlighted name form the last
	 * line. Empty parts are skipped so a shorter heading still renders cleanly.
	 *
	 * @param string $line1 First heading line.
	 * @param string $line2 Second heading line.
	 * @param string $lead  Lead-in text before the highlighted name.
	 * @param string $name  Highlighted name.
	 *
	 * @return string
	 */
	public function heading( string $line1, string $line2, string $lead, string $name ): string {
		$segments = array();
		if ( '' !== $line1 ) {
			$segments[] = esc_html( $line1 );
		}
		if ( '' !== $line2 ) {
			$segments[] = esc_html( $line2 );
		}

		if ( '' !== $lead || '' !== $name ) {
			$last = esc_html( $lead );
			if ( '' !== $name ) {
				$mark = '<mark style="background-color:transparent" class="has-inline-color has-accent-color">' . esc_html( $name ) . '</mark>';
				$last = '' !== $last ? $last . ' ' . $mark : $mark;
			}
			$segments[] = $last;
		}

		return implode( '<br>', $segments );
	}

	/**
	 * Build one hero meta column (label + value). Empty columns are skipped upstream.
	 *
	 * @param string $label Column label.
	 * @param string $value Column value.
	 *
	 * @return string
	 */
	public function meta_column( string $label, string $value ): string {
		$label = esc_html( $label );
		$value = esc_html( $value );

		return <<<HTML
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.1em"}},"fontSize":"tiny","fontFamily":"mono","textColor":"text-dim"} -->
<p class="has-text-dim-color has-text-color has-mono-font-family has-tiny-font-size" style="letter-spacing:0.1em;text-transform:uppercase">{$label}</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">{$value}</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
HTML;
	}

	/**
	 * {@inheritDoc}
	 */
	public function render( array $attributes ): string {
		$tagline  = esc_html( (string) ( $attributes['tagline'] ?? '' ) );
		$phonetic = esc_html( (string) ( $attributes['phonetic'] ?? '' ) );
		$bio      = esc_html( (string) ( $attributes['bio'] ?? '' ) );
		$heading  = $this->heading(
			(string) ( $attributes['headingLine1'] ?? '' ),
			(string) ( $attributes['headingLine2'] ?? '' ),
			(string) ( $attributes['headingLead'] ?? '' ),
			(string) ( $attributes['headingName'] ?? '' )
		);

		$columns = '';
		for ( $i = 1; $i <= 4; $i++ ) {
			$label = (string) ( $attributes[ "meta{$i}Label" ] ?? '' );
			$value = (string) ( $attributes[ "meta{$i}Value" ] ?? '' );
			if ( '' === $label && '' === $value ) {
				continue;
			}
			$columns .= $this->meta_column( $label, $value );
		}

		$meta_block = '';
		if ( '' !== $columns ) {
			$meta_block = <<<HTML
<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"},"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--50)">
{$columns}
</div>
<!-- /wp:columns -->
HTML;
		}

		$markup = <<<HTML
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)">
<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"500","letterSpacing":"0.1em","textTransform":"uppercase"}},"fontSize":"small","fontFamily":"mono","textColor":"accent"} -->
<p class="has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-style:normal;font-weight:500;letter-spacing:0.1em;text-transform:uppercase">{$tagline}</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">{$heading}</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"}},"fontSize":"small","fontFamily":"mono","textColor":"text-dim"} -->
<p class="has-text-dim-color has-text-color has-mono-font-family has-small-font-size" style="font-style:normal;font-weight:400">{$phonetic}</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"300","lineHeight":"1.7"},"layout":{"selfStretch":"fixed","flexSize":"640px"}},"fontSize":"large","textColor":"text-muted"} -->
<p class="has-text-muted-color has-text-color has-large-font-size" style="font-weight:300;line-height:1.7">{$bio}</p>
<!-- /wp:paragraph -->
{$meta_block}
</div>
<!-- /wp:group -->
HTML;

		return do_blocks( $markup );
	}
}
