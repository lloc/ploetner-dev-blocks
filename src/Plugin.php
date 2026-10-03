<?php
/**
 * Plugin bootstrap.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

namespace lloc\PloetnerDevBlocks;

use lloc\PloetnerDevBlocks\Blocks\Block;
use lloc\PloetnerDevBlocks\Blocks\BlockCategory;
use lloc\PloetnerDevBlocks\Blocks\Community;
use lloc\PloetnerDevBlocks\Blocks\CtaBanner;
use lloc\PloetnerDevBlocks\Blocks\Expertise;
use lloc\PloetnerDevBlocks\Blocks\Hero;
use lloc\PloetnerDevBlocks\Blocks\LanguageSwitcher;
use lloc\PloetnerDevBlocks\Blocks\OpenSource;
use lloc\PloetnerDevBlocks\Blocks\Speaking;
use lloc\PloetnerDevBlocks\PostTypes\AdminList;
use lloc\PloetnerDevBlocks\PostTypes\MetaBox;
use lloc\PloetnerDevBlocks\PostTypes\PostTypes;
use lloc\PloetnerDevBlocks\Seeder;

/**
 * Wires every component of the plugin to WordPress.
 */
class Plugin {

	/**
	 * The plugin's blocks.
	 *
	 * @return array<int, Block>
	 */
	public function blocks(): array {
		return array(
			new Hero(),
			new CtaBanner(),
			new Expertise(),
			new OpenSource(),
			new Speaking(),
			new Community(),
			new LanguageSwitcher(),
		);
	}

	/**
	 * Register every component.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'ploetner_theme_favicon', array( $this, 'favicon' ) );

		( new PostTypes() )->register();
		( new MetaBox() )->register();
		( new AdminList() )->register();
		( new BlockCategory() )->register();
		( new Assets() )->register();
		( new Patterns() )->register();
		( new MetaDescription() )->register();
		( new Seo() )->register();

		// Not on activation: post types and translations are not available yet
		// there. Runs on the redirect to plugins.php instead, guarded by an option.
		add_action( 'admin_init', array( new Seeder(), 'maybe_seed' ) );

		foreach ( $this->blocks() as $block ) {
			$block->register();
		}
	}

	/**
	 * Brand favicon of ploetner-theme for sites running this plugin.
	 *
	 * @return string
	 */
	public function favicon(): string {
		return 'dev';
	}
}
