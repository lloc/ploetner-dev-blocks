<?php
/**
 * Minimal Multisite Language Switcher stubs for PHPStan.
 *
 * Only the API the language switcher block uses. MLS is an optional runtime
 * dependency and not installed via Composer.
 *
 * @package PloetnerDevBlocks
 */

namespace lloc\Msls\Options {

	class Options {
		public static function create( int $id = 0 ): Options {}
	}
}

namespace lloc\Msls\Blog {

	/**
	 * @property int $userblog_id
	 */
	class Blog {
		public function get_url( \lloc\Msls\Options\Options $options ): ?string {}
		public function get_alpha2(): string {}
		public function get_description(): string {}
		public function get_language( string $preset = 'en_US' ): string {}
	}

	class Collection {
		/** @return array<int, Blog> */
		public function get_objects(): array {}
		public function is_current_blog( Blog $blog ): bool {}
	}
}

namespace {

	function msls_blog_collection(): \lloc\Msls\Blog\Collection {}
}
