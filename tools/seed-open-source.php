<?php
/**
 * Seed the Open Source block's CPT entries.
 *
 * Idempotent: items whose title already exists are skipped. Run with WP-CLI:
 *
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-open-source.php
 *
 * Content mirrors patterns/open-source.php (the canonical source).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( 'pd_project' ) ) {
	echo "Error: the 'pd_project' post type is not registered. Is the Plötner Dev Blocks plugin active?\n";
	return;
}

/**
 * Items to seed: title, description, tech, url, link_text, menu_order.
 *
 * @var array<int, array{0:string,1:string,2:string,3:string,4:string,5:int}>
 */
$items = array(
	array(
		'Multisite Language Switcher',
		'A multilingual plugin for WordPress Multisite, actively maintained since 2011. Helps thousands of sites run in multiple languages.',
		'WordPress · Multisite · i18n',
		'https://wordpress.org/plugins/multisite-language-switcher/',
		'View on WordPress.org ↗',
		0,
	),
	array(
		'composer-i18n-scripts',
		'A Composer plugin wrapping wp-cli/i18n-command. Exposes WordPress i18n commands directly through Composer scripts.',
		'Composer · PHP · wp-cli',
		'https://github.com/lloc/composer-i18n-scripts',
		'View on GitHub ↗',
		1,
	),
	array(
		'wp-multi-network',
		'Contributor. PHPStan Level 8 and PHPCS compliance. Enabling multiple networks within a single WordPress installation.',
		'WordPress · Multisite · PHPStan',
		'https://github.com/lloc/wp-multi-network',
		'View on GitHub ↗',
		2,
	),
);

$created = 0;
$skipped = 0;

foreach ( $items as [$title, $description, $tech, $url, $link_text, $menu_order] ) {
	// Idempotency check: match by slug (stable regardless of title encoding).
	$slug     = sanitize_title( $title );
	$existing = new WP_Query(
		array(
			'post_type'              => 'pd_project',
			'name'                   => $slug,
			'post_status'            => 'any',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! empty( $existing->posts ) ) {
		echo "Skipped (exists): {$title}\n";
		++$skipped;
		continue;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'pd_project',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $description ) . '</p><!-- /wp:paragraph -->',
			'menu_order'   => $menu_order,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		echo "Failed: {$title} — " . $post_id->get_error_message() . "\n";
		continue;
	}

	update_post_meta( $post_id, '_pd_project_tech', $tech );
	update_post_meta( $post_id, '_pd_project_url', esc_url_raw( $url ) );
	update_post_meta( $post_id, '_pd_project_link_text', $link_text );
	echo "Created: {$title} (#{$post_id})\n";
	++$created;
}

echo "\nDone. Created {$created}, skipped {$skipped}.\n";
