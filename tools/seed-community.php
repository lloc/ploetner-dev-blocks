<?php
/**
 * Seed the Community block's CPT entries.
 *
 * Idempotent: items whose title already exists are skipped, so it is safe to
 * re-run. Intended for WP-CLI:
 *
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-community.php
 *
 * Content mirrors patterns/community.php (the canonical source).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( 'pd_community' ) ) {
	echo "Error: the 'pd_community' post type is not registered. Is the Plötner Dev Blocks plugin active?\n";
	return;
}

/**
 * Items to seed: title, description, label, menu_order.
 *
 * @var array<int, array{0:string,1:string,2:string,3:int}>
 */
$items = array(
	array(
		'WordPress Meetup Milan',
		'Co-organizing the local WordPress community in Milan. Regular events, knowledge sharing, and connecting developers with the broader ecosystem.',
		'Meetup Organizer',
		0,
	),
	array(
		'ScuolaWP',
		'Co-founder of an Italian-language blog for WordPress developers. Technical article series covering Git, Composer, CI/CD, static analysis, and testing.',
		'Education',
		1,
	),
	array(
		'WordPress VIP',
		'Advanced Professional WordPress Developer Certification. Recognized expertise in enterprise-grade WordPress development.',
		'Certification',
		2,
	),
);

$created = 0;
$skipped = 0;

foreach ( $items as [$title, $description, $label, $menu_order] ) {
	// Idempotency check: match by slug (stable regardless of title encoding).
	$slug     = sanitize_title( $title );
	$existing = new WP_Query(
		array(
			'post_type'              => 'pd_community',
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
			'post_type'    => 'pd_community',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			// Stored as block markup, matching how the block editor authors the description.
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $description ) . '</p><!-- /wp:paragraph -->',
			'menu_order'   => $menu_order,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		echo "Failed: {$title} — " . $post_id->get_error_message() . "\n";
		continue;
	}

	update_post_meta( $post_id, '_pd_community_label', $label );
	echo "Created: {$title} (#{$post_id}, label: {$label})\n";
	++$created;
}

echo "\nDone. Created {$created}, skipped {$skipped}.\n";
