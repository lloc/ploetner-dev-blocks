<?php
/**
 * Plugin Name:       Plötner Dev Blocks
 * Plugin URI:        https://ploetner.dev
 * Description:       Dynamic content blocks (Hero, Expertise, Open Source, Speaking, Community, CTA Banner) and their backing custom post types for the Plötner Dev site.
 * Version:           1.0.0
 * Requires at least: 7.0
 * Requires PHP:      8.1
 * Author:            Dennis Plötner
 * Author URI:        https://ploetner.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ploetner-dev-blocks
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, static fn () => ( new Seeder() )->maybe_seed() );

add_action( 'plugins_loaded', static fn () => ( new Plugin() )->register() );
