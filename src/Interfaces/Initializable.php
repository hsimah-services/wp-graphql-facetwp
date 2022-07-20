<?php
/**
 * Interface for classes containing WordPress action/filter hooks.
 *
 * @package WPGraphQL\FacetWP\Interfaces;
 * @since @todo
 */

namespace WPGraphQL\FacetWP\Interfaces;

/**
 * Interface - Initializable
 */
interface Initializable {
	/**
	 * Registers class methods to WordPress.
	 *
	 * WordPress actions/filters should be included here.
	 */
	public static function init() : void;
}
