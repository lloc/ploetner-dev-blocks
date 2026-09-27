<?php
/**
 * Plugin Name:       Ploetner Dev Blocks
 * Plugin URI:        https://ploetner.dev
 * Description:       Dynamic content blocks (Hero, Expertise, Open Source, Speaking, Community, CTA Banner) and their backing custom post types for the Ploetner Dev site.
 * Version:           1.0.4
 * Requires at least: 7.0
 * Requires PHP:      8.1
 * Author:            Dennis Ploetner
 * Author URI:        https://ploetner.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ploetner-dev-blocks
 * Domain Path:       /languages
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, static fn () => ( new Seeder() )->maybe_seed() );

add_action( 'plugins_loaded', static fn () => ( new Plugin() )->register() );

// Bundled translations. Priority 1: block defaults, post type labels and
// patterns are built on `init` (priority 10) and must already be translated.
add_action(
	'init',
	static function (): void {
		load_plugin_textdomain( 'ploetner-dev-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	},
	1
);
