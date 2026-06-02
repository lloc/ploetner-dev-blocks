<?php
/**
 * Seed the Open Source block's CPT entries.
 *
 * Thin WP-CLI wrapper over the shared {@see \lloc\PloetnerDevBlocks\Seeder}
 * (the single source of truth). Idempotent: items whose slug already exists are
 * skipped, so it is safe to re-run.
 *
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-open-source.php
 *
 * Note: no `declare(strict_types=1)` — WP-CLI eval-file runs this through
 * eval(), where that declaration is illegal. Strict typing lives in the
 * Seeder class itself.
 *
 * @package PloetnerDevBlocks
 */

if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( 'pd_project' ) ) {
	echo "Error: the 'pd_project' post type is not registered. Is the Plötner Dev Blocks plugin active?\n";
	return;
}

$counts = ( new \lloc\PloetnerDevBlocks\Seeder() )->seed( 'pd_project' );

echo "Done. Created {$counts['created']}, skipped {$counts['skipped']}, failed {$counts['failed']}.\n";
