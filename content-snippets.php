<?php
/*
Plugin Name: Content Snippets
Plugin URI: https://github.com/nwcybersolutions/Content-Snippets
Description: Create reusable snippets of content that can be inserted into any page or post.
Author: Northwest Cyber Solutions
Author URI: https://nwcybersolutions.com
Version: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT
*/


// Don't load directly
if (!defined('ABSPATH')) { exit; }

// Load plugin classes
require_once(dirname(__FILE__) . '/class-r34-snippet.php');

// Instantiate
add_action('init', function() {
	global $R34Snippet;
	$R34Snippet = new R34Snippet();
});

// Resolve Select2 conflict between Shortcake (Shortcode UI) and ACF
add_action('init', function() { define('SELECT2_NOCONFLICT', true); }, 1);

// Flush rewrite rules on activation
register_activation_hook(__FILE__, function() { flush_rewrite_rules(); });
