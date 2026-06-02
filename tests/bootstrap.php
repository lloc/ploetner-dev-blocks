<?php
/**
 * PHPUnit bootstrap: Composer autoloader + minimal WordPress class doubles.
 *
 * @package PloetnerDevBlocks
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
require_once __DIR__ . '/Stubs/WP_Post.php';
require_once __DIR__ . '/Stubs/WP_Query.php';
