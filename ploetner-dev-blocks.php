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

/*
 * Register the bundled translations. WordPress 6.8+ does this on its own from
 * the Domain Path header, but only for plugins activated per site: the loop
 * for network-activated plugins in wp-settings.php skips it. This only records
 * the path; strings are still loaded just in time.
 */
load_plugin_textdomain( 'ploetner-dev-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

add_action( 'plugins_loaded', static fn () => ( new Plugin() )->register() );
