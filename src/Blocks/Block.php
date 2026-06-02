<?php
/**
 * Abstract base for the plugin's PHP-only (autoRegister) blocks.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\Blocks;

/**
 * Base class wiring a single PHP-only block to the `init` hook.
 */
abstract class Block {

	/**
	 * Block name (e.g. "ploetner-dev/hero"). Each block overrides this.
	 */
	public const NAME = '';

	/**
	 * Dashicon slug for the block. Each block overrides this.
	 */
	public const ICON = '';

	/**
	 * Block name (e.g. "ploetner-dev/hero").
	 *
	 * @return string
	 */
	public function name(): string {
		return static::NAME;
	}

	/**
	 * Block title shown in the inserter.
	 *
	 * @return string
	 */
	abstract public function title(): string;

	/**
	 * Dashicon slug for the block.
	 *
	 * @return string
	 */
	public function icon(): string {
		return static::ICON;
	}

	/**
	 * Attribute defaults: key => [ default, label ].
	 *
	 * @return array<string, array{default: mixed, label: string}>
	 */
	abstract public function defaults(): array;

	/**
	 * Render callback for the block.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 *
	 * @return string
	 */
	abstract public function render( array $attributes ): string;

	/**
	 * Register the block on the `init` hook.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register a PHP-only (autoRegister) block shared by the theme's sections.
	 *
	 * Attribute types are inferred from each default (int => integer, else string),
	 * which is what WordPress 7.0 uses to auto-generate the Inspector Controls.
	 *
	 * @return void
	 */
	public function register_block(): void {
		$attributes = array();
		foreach ( $this->defaults() as $key => $def ) {
			$attributes[ $key ] = array(
				'type'    => is_int( $def['default'] ) ? 'integer' : 'string',
				'default' => $def['default'],
				'label'   => $def['label'],
			);
		}

		$args = array(
			'title'           => $this->title(),
			'category'        => 'ploetner-dev',
			'icon'            => $this->icon(),
			'attributes'      => $attributes,
			'render_callback' => array( $this, 'render' ),
		);

		// PHP-only editor experience (auto preview + auto-generated controls) is WP 7.0+.
		if ( version_compare( get_bloginfo( 'version' ), '7.0', '>=' ) ) {
			$args['supports'] = array( 'autoRegister' => true );
		}

		register_block_type( $this->name(), $args );
	}
}
