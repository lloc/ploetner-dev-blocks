<?php
/**
 * Seed the Expertise block's CPT entries.
 *
 * Idempotent: items whose title already exists are skipped. Run with WP-CLI:
 *
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-expertise.php
 *
 * Content mirrors patterns/expertise.php (the canonical source).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( 'pd_expertise' ) ) {
	echo "Error: the 'pd_expertise' post type is not registered. Is the Plötner Dev Blocks plugin active?\n";
	return;
}

/**
 * Items to seed: title, description, menu_order.
 *
 * @var array<int, array{0:string,1:string,2:int}>
 */
$items = array(
	array( 'WordPress Multisite', 'Complex multisite architectures for enterprise clients. Network management, site scaffolding, cross-site data strategies. Contributor to wp-multi-network.', 0 ),
	array( 'Enterprise Plugins', 'Scalable plugin architectures using the Modularity framework. REST APIs, Block Editor integration, custom database layers, extension systems.', 1 ),
	array( 'Block Editor', 'Dynamic blocks, DataViews, DataForms, custom editor experiences. Bridging PHP backends with modern JavaScript frontends.', 2 ),
	array( 'Code Quality & CI/CD', 'PHPStan Level 8, PHPCS/WPCS, PHPUnit, GitHub Actions, Composer workflows. Automated pipelines that catch problems before they ship.', 3 ),
	array( 'SSO & Authentication', 'OIDC/OAuth2 integrations, Auth0, Keycloak. Secure authentication flows for enterprise WordPress installations.', 4 ),
	array( 'Open-Source Maintenance', '14+ years maintaining public plugins. Community stewardship, backwards compatibility, responsible release cycles.', 5 ),
);

$created = 0;
$skipped = 0;

foreach ( $items as [$title, $description, $menu_order] ) {
	// Idempotency check: match by slug (stable regardless of title encoding).
	$slug     = sanitize_title( $title );
	$existing = new WP_Query(
		array(
			'post_type'              => 'pd_expertise',
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
			'post_type'    => 'pd_expertise',
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

	echo "Created: {$title} (#{$post_id})\n";
	++$created;
}

echo "\nDone. Created {$created}, skipped {$skipped}.\n";
