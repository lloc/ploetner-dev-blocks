<?php
/**
 * Seed the plugin's sample CPT entries.
 *
 * Thin WP-CLI wrapper over the shared {@see \lloc\PloetnerDevBlocks\Seeder}
 * (the single source of truth). Idempotent: items whose slug already exists are
 * skipped, so it is safe to re-run.
 *
 * Without an argument every post type is seeded; pass one to limit it:
 *
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed.php
 *     wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed.php pd_talk
 *
 * Note: no `declare(strict_types=1)` — WP-CLI eval-file runs this through
 * eval(), where that declaration is illegal. Strict typing lives in the
 * Seeder class itself.
 *
 * @package PloetnerDevBlocks
 */

$post_type = $args[0] ?? null;

$probe = $post_type ?? 'pd_talk';

if ( ! function_exists( 'post_type_exists' ) || ! post_type_exists( $probe ) ) {
	echo "Error: the post type '{$probe}' is not registered. Is the Ploetner Dev Blocks plugin active?\n";
	return;
}

$counts = ( new \lloc\PloetnerDevBlocks\Seeder() )->seed( $post_type );

echo "Done. Created {$counts['created']}, skipped {$counts['skipped']}, failed {$counts['failed']}.\n";
