<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Wpunit extends \Codeception\Module
{
	
	/**
	 * Get the default checkbox facet args.
	 */
	public function get_default_checkbox_facet_args() : array {
		return [
			'count'           => '20',
			'ghosts'          => 'no',
			'hierarchical'    => 'no',
			'label'           => 'Checkboxes',
			'modifier_values' => '',
			'name'            => 'checkboxes',
			'operator'        => 'and',
			'orderby'         => 'count',
			'parent_term'     => '',
			'preserve_ghosts' => 'no',
			'show_expanded'   => 'no',
			'soft_limit'      => 5,
			'source'          => 'tax/category',
			'type'            => 'checkboxes',
		];
	}

	/**
	 * Get the default dropdown facet args.
	 */
	public function get_default_dropdown_facet_args() : array {
		return [
			'count'           => '10',
			'hierarchical'    => 'no',
			'label'           => 'Dropdown',
			'label_any'       => 'Any',
			'modifier_type'   => 'off',
			'modifier_values' => '',
			'name'            => 'dropdown',
			'orderby'         => 'count',
			'parent_term'     => '',
			'source'          => 'post_type',
			'type'            => 'dropdown',
		];
	}

	/**
	 * Get the default radio facet args.
	 */
	public function get_default_radio_facet_args() : array {
		return [
			'count'           => '10',
			'ghosts'          => 'no',
			'label_any'       => 'Any',
			'label'           => 'Radio',
			'modifier_type'   => 'off',
			'modifier_values' => '',
			'name'            => 'radio',
			'orderby'         => 'count',
			'parent_term'     => '',
			'preserve_ghosts' => 'no',
			'source'          => 'post_type',
			'type'            => 'radio',
		];
	}

	/**
	 * Get the default fselect facet args.
	 */
	public function get_default_fselect_facet_args() : array {
		return [
			'name'            => 'fselect',
			'label'           => 'fSelect',
			'type'            => 'fselect',
			'source'          => 'post_type',
			'label_any'       => 'Any',
			'parent_term'     => '',
			'modifier_type'   => 'off',
			'modifier_values' => '',
			'hierarchical'    => 'no',
			'multiple'        => 'no',
			'ghosts'          => 'no',
			'preserve_ghosts' => 'no',
			'operator'        => 'and',
			'orderby'         => 'count',
			'count'           => '10',
		];
	}

	/**
	 * Get the default hierarchy facet args.
	 */
	public function get_default_hierarchy_facet_args() : array {
		return [
			'count'           => '10',
			'label_any'       => 'Any',
			'label'           => 'Hierarchy',
			'modifier_type'   => 'off',
			'modifier_values' => '',
			'name'            => 'hierarchy',
			'orderby'         => 'count',
			'source'          => 'post_type',
			'type'            => 'hierarchy',
		];
	}

	/**
	 * Get the default slider facet args.
	 */
	public function get_default_slider_facet_args() : array {
		return [
			'compare_type' => '',
			'format'       => '0,0',
			'label'        => 'Slider',
			'name'         => 'slider',
			'prefix'       => '',
			'reset_text'   => 'Reset',
			'source'       => 'post_type',
			'step'         => '1',
			'suffix'       => '',
			'type'         => 'slider',
		];
	}

	/**
	 * Get the default search facet args.
	 */
	public function get_default_search_facet_args() : array {
		return [
			'auto_refresh'  => 'no',
			'label'         => 'Search',
			'name'          => 'search',
			'placeholder'   => '',
			'search_engine' => '',
			'type'          => 'search',
		];
	}

	/**
	 * Get the default autocomplete facet args.
	 */
	public function get_default_autocomplete_facet_args() : array {
		return [
			'label'       => 'Autocomplete',
			'name'        => 'autocomplete',
			'placeholder' => '',
			'source'      => 'post_type',
			'type'        => 'autocomplete',
		];
	}

	/**
	 * Get the default date_range facet args.
	 */
	public function get_default_date_range_facet_args() : array {
		return [
			'compare_type' => '',
			'fields'       => 'both',
			'format'       => '',
			'label'        => 'DateRange',
			'name'         => 'daterange',
			'source'       => 'post_type',
			'type'         => 'date_range',
		];
	}

	/**
	 * Get the default number_range facet args.
	 */
	public function get_default_number_range_facet_args() : array {
		return [
			'compare_type' => '',
			'fields'       => 'both',
			'label'        => 'NumberRange',
			'name'         => 'numberrange',
			'source_other' => 'post_modified',
			'source'       => 'post_type',
			'type'         => 'number_range',
		];
	}

	/**
	 * Get the default rating facet args.
	 */
	public function get_default_rating_facet_args() : array {
		return [
			'label'  => 'StarRating',
			'name'   => 'starrating',
			'source' => 'post_type',
			'type'   => 'rating',
		];
	}

	/**
	 * Get the default proximity facet args.
	 */
	public function get_default_proximity_facet_args() : array {
		return [
			'label'          => 'Proximity',
			'name'           => 'proximity',
			'radius_default' => '25',
			'radius_max'     => '50',
			'radius_min'     => '1',
			'radius_options' => '10, 25, 50, 100, 250',
			'radius_ui'      => 'dropdown',
			'source'         => 'post_type',
			'type'           => 'proximity',
			'unit'           => 'mi',
		];
	}

	/**
	 * Get the default pager facet args.
	 */
	public function get_default_pager_facet_args() : array {
		return [
			'count_text_none'     => 'No results',
			'count_text_plural'   => '[lower] - [upper] of [total] results',
			'count_text_singular' => '1 result',
			'default_label'       => 'Per page',
			'dots_label'          => '…',
			'inner_size'          => '2',
			'label'               => 'Pager_facet',
			'load_more_text'      => 'Load more',
			'loading_text'        => 'Loading...',
			'name'                => 'pager_',
			'next_label'          => 'Next »',
			'pager_type'          => 'numbers',
			'per_page_options'    => '10, 25, 50, 100',
			'prev_label'          => '« Prev',
			'type'                => 'pager',
		];
	}

	/**
	 * Get the default reset facet args.
	 */
	public function get_default_reset_facet_args() : array {
		return [
			'auto_hide'    => 'no',
			'label'        => 'Reset',
			'name'         => 'reset',
			'reset_facets' => [],
			'reset_mode'   => 'off',
			'reset_text'   => 'Reset',
			'reset_ui'     => 'button',
			'type'         => 'reset',
		];
	}

	/**
	 * Get the default sort facet args.
	 */
	public function get_default_sort_facet_args() : array {
		return [
			'default_label' => 'Sort by',
			'label'         => 'Sort_facet',
			'name'          => 'sort_facet',
			'type'          => 'sort',
			'sort_options'  => [],
		];
	}
}
