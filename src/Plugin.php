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
use lloc\PloetnerDevBlocks\Blocks\OpenSource;
use lloc\PloetnerDevBlocks\Blocks\Speaking;
use lloc\PloetnerDevBlocks\PostTypes\MetaBox;
use lloc\PloetnerDevBlocks\PostTypes\PostTypes;

/**
 * Wires every component of the plugin to WordPress.
 */
class Plugin {

	/**
	 * The plugin's six blocks.
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
		);
	}

	/**
	 * Register every component.
	 *
	 * @return void
	 */
	public function register(): void {
		( new PostTypes() )->register();
		( new MetaBox() )->register();
		( new BlockCategory() )->register();

		foreach ( $this->blocks() as $block ) {
			$block->register();
		}
	}
}
