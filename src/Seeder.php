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

use WP_Query;

/**
 * Writes the sample CPT entries once per seed version, skipping items that
 * already exist (matched by slug).
 */
class Seeder {

	/**
	 * Current seed version. Bump whenever new items are added to data() so
	 * existing installs pick them up on the next activation or admin load.
	 */
	public const SEED_VERSION = 1;

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

		foreach ( self::data() as $post_type => $items ) {
			if ( null !== $only && $only !== $post_type ) {
				continue;
			}

			foreach ( $items as $item ) {
				$this->seed_item( $post_type, $item, $counts );
			}
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

		foreach ( $item['meta'] ?? array() as $key => $value ) {
			$sanitized = '_pd_project_url' === $key ? esc_url_raw( $value ) : sanitize_text_field( $value );
			update_post_meta( $post_id, $key, $sanitized );
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
					'content'    => self::paragraph( 'Complex multisite architectures for enterprise clients. Network management, site scaffolding, cross-site data strategies. Contributor to wp-multi-network.' ),
					'menu_order' => 0,
				),
				array(
					'title'      => 'Enterprise Plugins',
					'content'    => self::paragraph( 'Scalable plugin architectures using the Modularity framework. REST APIs, Block Editor integration, custom database layers, extension systems.' ),
					'menu_order' => 1,
				),
				array(
					'title'      => 'Block Editor',
					'content'    => self::paragraph( 'Dynamic blocks, DataViews, DataForms, custom editor experiences. Bridging PHP backends with modern JavaScript frontends.' ),
					'menu_order' => 2,
				),
				array(
					'title'      => 'Code Quality & CI/CD',
					'content'    => self::paragraph( 'PHPStan Level 8, PHPCS/WPCS, PHPUnit, GitHub Actions, Composer workflows. Automated pipelines that catch problems before they ship.' ),
					'menu_order' => 3,
				),
				array(
					'title'      => 'SSO & Authentication',
					'content'    => self::paragraph( 'OIDC/OAuth2 integrations, Auth0, Keycloak. Secure authentication flows for enterprise WordPress installations.' ),
					'menu_order' => 4,
				),
				array(
					'title'      => 'Open-Source Maintenance',
					'content'    => self::paragraph( '14+ years maintaining public plugins. Community stewardship, backwards compatibility, responsible release cycles.' ),
					'menu_order' => 5,
				),
			),
			'pd_project'   => array(
				array(
					'title'      => 'Multisite Language Switcher',
					'content'    => self::paragraph( 'A multilingual plugin for WordPress Multisite, actively maintained since 2011. Helps thousands of sites run in multiple languages.' ),
					'menu_order' => 0,
					'meta'       => array(
						'_pd_project_tech'      => 'WordPress · Multisite · i18n',
						'_pd_project_url'       => 'https://wordpress.org/plugins/multisite-language-switcher/',
						'_pd_project_link_text' => 'View on WordPress.org ↗',
					),
				),
				array(
					'title'      => 'composer-i18n-scripts',
					'content'    => self::paragraph( 'A Composer plugin wrapping wp-cli/i18n-command. Exposes WordPress i18n commands directly through Composer scripts.' ),
					'menu_order' => 1,
					'meta'       => array(
						'_pd_project_tech'      => 'Composer · PHP · wp-cli',
						'_pd_project_url'       => 'https://github.com/lloc/composer-i18n-scripts',
						'_pd_project_link_text' => 'View on GitHub ↗',
					),
				),
				array(
					'title'      => 'wp-multi-network',
					'content'    => self::paragraph( 'Contributor. PHPStan Level 8 and PHPCS compliance. Enabling multiple networks within a single WordPress installation.' ),
					'menu_order' => 2,
					'meta'       => array(
						'_pd_project_tech'      => 'WordPress · Multisite · PHPStan',
						'_pd_project_url'       => 'https://github.com/lloc/wp-multi-network',
						'_pd_project_link_text' => 'View on GitHub ↗',
					),
				),
			),
			'pd_talk'      => array(
				array(
					'title'      => 'Dynamic Blocks from 0 to 100 in 30 Minutes',
					'menu_order' => 0,
					'meta'       => array(
						'_pd_talk_year'  => '2026',
						'_pd_talk_event' => 'WordCamp Vienna',
					),
				),
				array(
					'title'      => 'Contributor Day Table Lead',
					'menu_order' => 1,
					'meta'       => array(
						'_pd_talk_year'  => '2026',
						'_pd_talk_event' => 'WordCamp Europe, Kraków',
					),
				),
				array(
					'title'      => 'Regular speaker & attendee',
					'menu_order' => 2,
					'meta'       => array(
						'_pd_talk_year'  => '2017 –',
						'_pd_talk_event' => 'WordCamp Europe',
					),
				),
			),
			'pd_community' => array(
				array(
					'title'      => 'WordPress Meetup Milan',
					'content'    => self::paragraph( 'Co-organizing the local WordPress community in Milan. Regular events, knowledge sharing, and connecting developers with the broader ecosystem.' ),
					'menu_order' => 0,
					'meta'       => array(
						'_pd_community_label' => 'Meetup Organizer',
					),
				),
				array(
					'title'      => 'ScuolaWP',
					'content'    => self::paragraph( 'Co-founder of an Italian-language blog for WordPress developers. Technical article series covering Git, Composer, CI/CD, static analysis, and testing.' ),
					'menu_order' => 1,
					'meta'       => array(
						'_pd_community_label' => 'Education',
					),
				),
				array(
					'title'      => 'WordPress VIP',
					'content'    => self::paragraph( 'Advanced Professional WordPress Developer Certification. Recognized expertise in enterprise-grade WordPress development.' ),
					'menu_order' => 2,
					'meta'       => array(
						'_pd_community_label' => 'Certification',
					),
				),
			),
		);
	}
}
