<?php

// Don't load directly
if (!defined('ABSPATH')) { exit; }

class R34Snippet {

	// Custom Post Types
	var $cpt = array(
		'snippet' => array(
			'args' => array(
				'labels' => array(
					'name'					=> 'Snippets',
					'singular_name'			=> 'Snippet',
					'menu_name'				=> 'Snippets',
					'name_admin_bar'		=> 'Snippet',
					'add_new'				=> 'Add New',
					'add_new_item'			=> 'Add New Snippet',
					'new_item'				=> 'New Snippet',
					'edit_item'				=> 'Edit Snippet',
					'view_item'				=> 'View Snippet',
					'all_items'				=> 'All Snippets',
					'search_items'			=> 'Search Snippets',
					'parent_item_colon'		=> 'Parent Snippet:',
					'not_found'				=> 'No snippets found.',
					'not_found_in_trash'	=> 'No snippets found in Trash.',
				),
				'capability_type'			=> 'post',
				'description'				=> 'Reusable content snippets.',
				'has_archive'				=> false,
				'hierarchical'				=> false,
				'menu_icon'					=> 'dashicons-format-aside',
				'public'					=> false,
				'rewrite'					=> false,
				'show_ui'					=> true,
				'show_in_nav_menus'			=> true,
				'supports'					=> array('title', 'editor', 'revisions'),
			),
		),
	);
	

	// Custom taxonomies
	var $taxo = array(
		/*
		'snippet_tag' => array(
			'post_type' => array('snippet'),
			'args' => array(
				'labels' => array(
					'name'							=> 'Tags',
					'singular_name'					=> 'Tag',
					'search_items'					=> 'Search Tags',
					'popular_items'					=> 'Popular Tags',
					'all_items'						=> 'All Tags',
					'edit_item'						=> 'Edit Tag',
					'update_item'					=> 'Update Tag',
					'add_new_item'					=> 'Add New Tag',
					'new_item_name'					=> 'New Tag',
					'separate_items_with_commas'	=> 'Separate tags with commas',
					'add_or_remove_items'			=> 'Add or remove tags',
					'choose_from_most_used'			=> 'Choose from the most used tags',
					'not_found'						=> 'No atgs found.',
					'menu_name'						=> 'Tags',
				),
				'hierarchical'						=> false,
				'public'							=> false,
				'publicly_queryable'				=> false,
				'rewrite'							=> false,
				'show_admin_column'					=> true,
				'show_in_menu'						=> true,
				'show_ui'							=> true,
			),
		),
		*/
	);
	
	
	// Initialize plugin
	public function __construct() {

		// Register CPTs
		$this->register_cpts();
		
		// Register taxonomies
		$this->register_taxonomies();
		
		// Add shortcode
		add_shortcode('snippet', array(&$this, 'shortcode'));
		
		// Set up Shortcake (Shortcode UI)
		add_action('init', array(&$this, 'shortcode_ui_detection'));
		add_action('register_shortcode_ui', array(&$this, 'register_shortcode_ui'));
		
	}
	
	
	// Register CPTs
	public function register_cpts() {
		foreach ((array)$this->cpt as $cpt => $params) {
			register_post_type($cpt, $params['args']);
			if (!empty($params['remove_post_type_support'])) {
				foreach ((array)$params['remove_post_type_support'] as $feature) {
					remove_post_type_support($cpt, $feature);
				}
			}
		}
	}
	
	
	// Register taxonomies
	public function register_taxonomies() {
		foreach ((array)$this->taxo as $taxo => $params) {
			register_taxonomy($taxo, $params['post_type'], $params['args']);
		}
	}
	
	
	// Register Shortcode UI
	public function register_shortcode_ui() {
		$fields = array(
			array(
				'label' => 'Select Snippet',
				'attr' => 'id',
				'type' => 'post_select',
				'query' => array('post_type' => 'snippet'),
				'multiple' => false,
			),
			array(
				'label' => 'Show Title',
				'attr' => 'showtitle',
				'type' => 'checkbox',
			),
		);
		$args = array(
			'label' => 'Snippet',
			'listItemImage' => 'dashicons-format-aside',
			'attrs' => $fields,
		);
		shortcode_ui_register_for_shortcode('snippet', $args);
	}
		

	// Shortcode
	public function shortcode($atts) {
		$content = false;
		ini_set('display_errors','On');
		
		// Extract attributes
		extract(shortcode_atts(array(
			'id' => null,
			'showtitle' => false,
		), $atts));

		// Get snippet
		if (!empty($id) && intval($id) > 0) {
			$snippet = get_post($atts['id']);
		}

		// Assemble snippet content
		if (!empty($snippet)) {
			$content = '<div class="r34-snippet">';
			if (!empty($showtitle)) {
				$content .= '<h2 class="r34-snippet-title">' . get_the_title($id) . '</h2>';
			}
			$content .= apply_filters('the_content', $snippet->post_content) . '</div>';
		}
		
		return $content;
	}
	
	
	// Shortcake (Shortcode UI) detection
	public function shortcode_ui_detection() {
		if (! function_exists('shortcode_ui_register_for_shortcode')) {
			add_action('admin_notices', function() {
			if (current_user_can('activate_plugins')) {
				?>
				<div class="error message">
					<p><a href="https://wordpress.org/plugins/shortcode-ui/" target="_blank">Shortcake (Shortcode UI)</a> plugin is missing or deactivated. Shortcode insertion tool for <strong>Content Snippets</strong> will not be available.</p>
				</div>
				<?php
				}
			});
		}
	}
	

}