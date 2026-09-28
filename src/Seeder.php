<?php
/**
 * Idempotent, version-aware seeder for the plugin's sample content.
 *
 * Single source of truth for the demo entries that back the dynamic blocks.
 * Runs on activation (and as a cheap catch-up on admin_init after a version
 * bump) and is also the engine behind the tools/seed-*.php WP-CLI wrappers.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

use lloc\PloetnerDevBlocks\PostTypes\PostTypes;
use WP_Query;

/**
 * Writes the sample CPT entries once per seed version, skipping items that
 * already exist (matched by slug).
 */
class Seeder {

	/**
	 * Current seed version. Bump whenever new items are added to data() so
	 * existing sites pick them up on their next admin load.
	 *
	 * 2: talks with links (WCEU Basel, WCUS Portland), talk list in sync with
	 *    the live site.
	 */
	public const SEED_VERSION = 2;

	/**
	 * Option recording the last seed version written to this site.
	 */
	public const OPTION = 'pd_blocks_seed_version';

	/**
	 * Seed the sample content unless this site is already on the current
	 * version. Cheap enough to call on every admin request — the guard is a
	 * single (autoloaded) option read.
	 *
	 * @return void
	 */
	public function maybe_seed(): void {
		if ( (int) get_option( self::OPTION, 0 ) >= self::SEED_VERSION ) {
			return;
		}

		$this->seed();

		update_option( self::OPTION, self::SEED_VERSION );
	}

	/**
	 * Insert any missing sample entries. Idempotent: items whose slug already
	 * exists are skipped, so it is always safe to re-run.
	 *
	 * Titles and texts are translated into the site language. Slugs derive
	 * from the translated title, so re-seeding a site after changing its
	 * language creates a second, translated set.
	 *
	 * @param string|null $only Limit seeding to a single post type, or null for all.
	 *
	 * @return array{created: int, skipped: int, failed: int}
	 */
	public function seed( ?string $only = null ): array {
		$counts = array(
			'created' => 0,
			'skipped' => 0,
			'failed'  => 0,
		);

		// Seed in the site language, not the (admin) user language, so a German
		// site in the network gets German sample content.
		$switched = switch_to_locale( get_locale() );

		foreach ( self::data() as $post_type => $items ) {
			if ( null !== $only && $only !== $post_type ) {
				continue;
			}

			foreach ( $items as $item ) {
				$this->seed_item( $post_type, $item, $counts );
			}
		}

		if ( $switched ) {
			restore_previous_locale();
		}

		return $counts;
	}

	/**
	 * Insert a single item if it does not yet exist, updating the counts by ref.
	 *
	 * @param string                                                                      $post_type Target post type.
	 * @param array{title: string, content?: string, menu_order: int, meta?: array<string, string>} $item   Item definition.
	 * @param array{created: int, skipped: int, failed: int}                              $counts    Running totals (by reference).
	 *
	 * @return void
	 */
	private function seed_item( string $post_type, array $item, array &$counts ): void {
		$slug = sanitize_title( $item['title'] );

		if ( $this->exists( $post_type, $slug ) ) {
			++$counts['skipped'];
			return;
		}

		$postarr = array(
			'post_type'   => $post_type,
			'post_status' => 'publish',
			'post_title'  => $item['title'],
			'post_name'   => $slug,
			'menu_order'  => $item['menu_order'],
		);

		if ( isset( $item['content'] ) ) {
			$postarr['post_content'] = $item['content'];
		}

		$post_id = wp_insert_post( $postarr, true );

		if ( is_wp_error( $post_id ) ) {
			++$counts['failed'];
			return;
		}

		$fields = PostTypes::meta_fields()[ $post_type ] ?? array();
		foreach ( $item['meta'] ?? array() as $key => $value ) {
			$sanitize = $fields[ $key ]['sanitize'] ?? 'sanitize_text_field';
			if ( is_callable( $sanitize ) ) {
				update_post_meta( $post_id, $key, $sanitize( $value ) );
			}
		}

		++$counts['created'];
	}

	/**
	 * Whether an entry with the given slug already exists for the post type.
	 *
	 * @param string $post_type Post type to check.
	 * @param string $slug      Post slug (post_name) to match.
	 *
	 * @return bool
	 */
	private function exists( string $post_type, string $slug ): bool {
		$existing = new WP_Query(
			array(
				'post_type'              => $post_type,
				'name'                   => $slug,
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		return ! empty( $existing->posts );
	}

	/**
	 * Wrap a description in the paragraph block markup the editor authors.
	 *
	 * @param string $description Plain-text description.
	 *
	 * @return string
	 */
	private static function paragraph( string $description ): string {
		return '<!-- wp:paragraph --><p>' . esc_html( $description ) . '</p><!-- /wp:paragraph -->';
	}

	/**
	 * All sample entries, keyed by post type. Single source of truth — mirrors
	 * the canonical patterns/*.php content.
	 *
	 * @return array<string, array<int, array{title: string, content?: string, menu_order: int, meta?: array<string, string>}>>
	 */
	public static function data(): array {
		return array(
			'pd_expertise' => array(
				array(
					'title'      => 'WordPress Multisite',
					'content'    => self::paragraph( __( 'Complex multisite architectures for enterprise clients. Multi-network installations, network management, automated site provisioning, cross-site data strategies.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 0,
				),
				array(
					'title'      => 'Enterprise Plugins',
					'content'    => self::paragraph( __( 'Scalable plugin architectures that stay maintainable for years. Modular, testable, extensible. REST APIs, Block Editor integration, custom database layers, extension systems.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 1,
				),
				array(
					'title'      => 'Block Editor',
					'content'    => self::paragraph( __( 'Dynamic blocks, DataViews, DataForms, custom editor experiences. Bridging PHP backends with modern JavaScript frontends.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 2,
				),
				array(
					'title'      => __( 'Code Quality & CI/CD', 'ploetner-dev-blocks' ),
					'content'    => self::paragraph( __( 'Static analysis with PHPStan, coding standards with PHPCS/WPCS, tests with PHPUnit, automation with GitHub Actions and Composer. Pipelines that catch problems before they ship.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 3,
				),
				array(
					'title'      => __( 'SSO & Authentication', 'ploetner-dev-blocks' ),
					'content'    => self::paragraph( __( 'Single sign-on for WordPress in the enterprise. OpenID Connect and OAuth 2.0, connected to existing identity providers. Secure, centrally managed, no extra passwords.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 4,
				),
				array(
					'title'      => __( 'Open-Source Maintenance', 'ploetner-dev-blocks' ),
					'content'    => self::paragraph( __( 'Maintaining public plugins used by thousands of sites since 2011. Updates that don\'t break things, an open ear for the community and releases you can rely on.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 5,
				),
			),
			'pd_project'   => array(
				array(
					'title'      => 'Multisite Language Switcher',
					'content'    => self::paragraph( __( 'A multilingual plugin for WordPress Multisite, actively maintained since 2011. Helps thousands of sites run in multiple languages.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 0,
					'meta'       => array(
						'_pd_project_tech'      => 'WordPress · Multisite · i18n',
						'_pd_project_url'       => 'https://wordpress.org/plugins/multisite-language-switcher/',
						'_pd_project_link_text' => __( 'View on WordPress.org ↗', 'ploetner-dev-blocks' ),
					),
				),
				array(
					'title'      => 'composer-i18n-scripts',
					'content'    => self::paragraph( __( 'A Composer plugin wrapping wp-cli/i18n-command. Exposes WordPress i18n commands directly through Composer scripts.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 1,
					'meta'       => array(
						'_pd_project_tech'      => 'Composer · PHP · wp-cli',
						'_pd_project_url'       => 'https://github.com/lloc/composer-i18n-scripts',
						'_pd_project_link_text' => __( 'View on GitHub ↗', 'ploetner-dev-blocks' ),
					),
				),
				array(
					'title'      => 'wp-multi-network',
					'content'    => self::paragraph( __( 'Contributor. PHPStan Level 8 and PHPCS compliance. Enabling multiple networks within a single WordPress installation.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 2,
					'meta'       => array(
						'_pd_project_tech'      => 'WordPress · Multisite · PHPStan',
						'_pd_project_url'       => 'https://github.com/lloc/wp-multi-network',
						'_pd_project_link_text' => __( 'View on GitHub ↗', 'ploetner-dev-blocks' ),
					),
				),
			),
			'pd_talk'      => array(
				array(
					'title'      => 'Multilingual WordPress for developers',
					'menu_order' => 0,
					'meta'       => array(
						'_pd_talk_year'  => '2025',
						'_pd_talk_event' => 'WordCamp Europe, Basel (CH)',
						'_pd_talk_url'   => 'https://wordpress.tv/2025/06/07/multilingual-wordpress-for-developers/',
					),
				),
				array(
					'title'      => 'Improving WordPress Multisite: Simplifying Features and Enhancing the User Experience',
					'menu_order' => 1,
					'meta'       => array(
						'_pd_talk_year'  => '2025',
						'_pd_talk_event' => 'WordCamp US, Portland (OR)',
						'_pd_talk_url'   => 'https://wordpress.tv/2025/09/03/improving-wordpress-multisite-simplifying-features-and-enhancing-the-user-experience/',
					),
				),
				array(
					'title'      => 'Dynamic Blocks from 0 to 100 in 30 Minutes',
					'menu_order' => 2,
					'meta'       => array(
						'_pd_talk_year'  => '2026',
						'_pd_talk_event' => 'WordCamp Vienna',
						'_pd_talk_url'   => 'https://wordpress.tv/2026/04/25/dynamic-blocks-from-0-to-100-in-30-minutes/',
					),
				),
				array(
					'title'      => 'Contributor Day Table Lead',
					'menu_order' => 3,
					'meta'       => array(
						'_pd_talk_year'  => '2026',
						'_pd_talk_event' => 'WordCamp Europe, Kraków',
						'_pd_talk_url'   => 'https://europe.wordcamp.org/2026/community/contributor-day/',
					),
				),
			),
			'pd_community' => array(
				array(
					'title'      => __( 'WordPress Meetup Milan', 'ploetner-dev-blocks' ),
					'content'    => self::paragraph( __( 'Co-organizing the local WordPress community in Milan. Regular events, knowledge sharing, and connecting developers with the broader ecosystem.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 0,
					'meta'       => array(
						'_pd_community_label' => __( 'Meetup Organizer', 'ploetner-dev-blocks' ),
					),
				),
				array(
					'title'      => 'ScuolaWP',
					'content'    => self::paragraph( __( 'Co-founder of an Italian-language blog for WordPress developers. Technical article series covering Git, Composer, CI/CD, static analysis, and testing.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 1,
					'meta'       => array(
						'_pd_community_label' => __( 'Education', 'ploetner-dev-blocks' ),
					),
				),
				array(
					'title'      => 'WordPress VIP',
					'content'    => self::paragraph( __( 'Advanced Professional WordPress Developer Certification. Recognized expertise in enterprise-grade WordPress development.', 'ploetner-dev-blocks' ) ),
					'menu_order' => 2,
					'meta'       => array(
						'_pd_community_label' => __( 'Certification', 'ploetner-dev-blocks' ),
					),
				),
			),
		);
	}
}
