<?php
/**
 * Seed the Speaking block's CPT entries.
 *
 * Idempotent: items whose title already exists are skipped. Run with WP-CLI:
 *
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-speaking.php
 *
 * Content mirrors patterns/speaking.php (the canonical source).
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( 'pd_talk' ) ) {
	echo "Error: the 'pd_talk' post type is not registered. Is the Plötner Dev Blocks plugin active?\n";
	return;
}

/**
 * Items to seed: talk title, year, event, menu_order.
 *
 * @var array<int, array{0:string,1:string,2:string,3:int}>
 */
$items = array(
	array( 'Dynamic Blocks from 0 to 100 in 30 Minutes', '2026', 'WordCamp Vienna', 0 ),
	array( 'Contributor Day Table Lead', '2026', 'WordCamp Europe, Kraków', 1 ),
	array( 'Regular speaker & attendee', '2017 –', 'WordCamp Europe', 2 ),
);

$created = 0;
$skipped = 0;

foreach ( $items as [$title, $year, $event, $menu_order] ) {
	// Idempotency check: match by slug (stable regardless of title encoding).
	$slug     = sanitize_title( $title );
	$existing = new WP_Query(
		array(
			'post_type'              => 'pd_talk',
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
			'post_type'   => 'pd_talk',
			'post_status' => 'publish',
			'post_title'  => $title,
			'post_name'   => $slug,
			'menu_order'  => $menu_order,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		echo "Failed: {$title} — " . $post_id->get_error_message() . "\n";
		continue;
	}

	update_post_meta( $post_id, '_pd_talk_year', $year );
	update_post_meta( $post_id, '_pd_talk_event', $event );
	echo "Created: {$title} (#{$post_id})\n";
	++$created;
}

echo "\nDone. Created {$created}, skipped {$skipped}.\n";
