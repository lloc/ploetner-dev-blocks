<?php
/**
 * Custom post types for the Plötner Dev Blocks plugin.
 *
 * These CPTs back the theme's dynamic blocks. They have no public archive or
 * single output of their own — entries are surfaced only inside blocks.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks\PostTypes;

/**
 * Registers the plugin's custom post types and their meta. Also the single
 * source of truth for the post type and meta field definitions.
 */
class PostTypes {

	/**
	 * Post type definitions: slug => [labels, icon, supports].
	 *
	 * @return array<'pd_expertise'|'pd_project'|'pd_talk'|'pd_community', array{singular: string, plural: string, icon: string, supports: array<int, string>}>
	 */
	public static function definitions(): array {
		return array(
			'pd_expertise' => array(
				'singular' => __( 'Expertise Item', 'ploetner-dev-blocks' ),
				'plural'   => __( 'Expertise', 'ploetner-dev-blocks' ),
				'icon'     => 'dashicons-awards',
				'supports' => array( 'title', 'editor', 'page-attributes' ),
			),
			'pd_project'   => array(
				'singular' => __( 'Project', 'ploetner-dev-blocks' ),
				'plural'   => __( 'Projects', 'ploetner-dev-blocks' ),
				'icon'     => 'dashicons-editor-code',
				'supports' => array( 'title', 'editor', 'page-attributes' ),
			),
			'pd_talk'      => array(
				'singular' => __( 'Talk', 'ploetner-dev-blocks' ),
				'plural'   => __( 'Talks', 'ploetner-dev-blocks' ),
				'icon'     => 'dashicons-microphone',
				'supports' => array( 'title', 'page-attributes' ),
			),
			'pd_community' => array(
				'singular' => __( 'Community Item', 'ploetner-dev-blocks' ),
				'plural'   => __( 'Community', 'ploetner-dev-blocks' ),
				'icon'     => 'dashicons-groups',
				'supports' => array( 'title', 'editor', 'page-attributes' ),
			),
		);
	}

	/**
	 * Meta fields per post type: post_type => [ meta_key => field config ].
	 *
	 * field config: label, input (text|url|textarea), placeholder, description, sanitize.
	 *
	 * @return array<string, array<string, array<string, string>>>
	 */
	public static function meta_fields(): array {
		return array(
			'pd_project'   => array(
				'_pd_project_tech'      => array(
					'label'       => __( 'Tech tags', 'ploetner-dev-blocks' ),
					'placeholder' => 'WordPress · Multisite · i18n',
					'description' => __( 'Separated by · (middot).', 'ploetner-dev-blocks' ),
				),
				'_pd_project_url'       => array(
					'label'       => __( 'Link URL', 'ploetner-dev-blocks' ),
					'input'       => 'url',
					'placeholder' => 'https://…',
					'sanitize'    => 'esc_url_raw',
				),
				'_pd_project_link_text' => array(
					'label'       => __( 'Link text', 'ploetner-dev-blocks' ),
					'placeholder' => 'View on GitHub ↗',
				),
			),
			'pd_talk'      => array(
				'_pd_talk_year'  => array(
					'label'       => __( 'Year', 'ploetner-dev-blocks' ),
					'placeholder' => '2026',
				),
				'_pd_talk_event' => array(
					'label'       => __( 'Event', 'ploetner-dev-blocks' ),
					'placeholder' => 'WordCamp Europe',
				),
			),
			'pd_community' => array(
				'_pd_community_label' => array(
					'label'       => __( 'Card Label', 'ploetner-dev-blocks' ),
					'placeholder' => 'Meetup Organizer',
					'description' => __( 'Short label shown above the card title.', 'ploetner-dev-blocks' ),
				),
			),
		);
	}

	/**
	 * Hook post type and meta registration into `init`.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register all post types.
	 *
	 * @return void
	 */
	public function register_post_types(): void {
		foreach ( self::definitions() as $slug => $config ) {
			register_post_type(
				$slug,
				array(
					'labels'              => array(
						'name'          => $config['plural'],
						'singular_name' => $config['singular'],
						/* translators: %s: singular post type name. */
						'add_new_item'  => sprintf( __( 'Add %s', 'ploetner-dev-blocks' ), $config['singular'] ),
						/* translators: %s: singular post type name. */
						'edit_item'     => sprintf( __( 'Edit %s', 'ploetner-dev-blocks' ), $config['singular'] ),
						'menu_name'     => $config['plural'],
					),
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_rest'        => true,
					'has_archive'         => false,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'rewrite'             => false,
					'menu_icon'           => $config['icon'],
					'supports'            => $config['supports'],
				)
			);
		}
	}

	/**
	 * Register all post meta.
	 *
	 * @return void
	 */
	public function register_meta(): void {
		foreach ( self::meta_fields() as $post_type => $fields ) {
			foreach ( $fields as $key => $field ) {
				$sanitize = $field['sanitize'] ?? 'sanitize_text_field';
				register_post_meta(
					$post_type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => is_callable( $sanitize ) ? $sanitize : 'sanitize_text_field',
						'auth_callback'     => static fn (): bool => current_user_can( 'edit_posts' ),
					)
				);
			}
		}
	}
}
