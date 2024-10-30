<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              liquidedge.co.nz
 * @since             1.0
 * @package           Led_Tweaks
 *
 * @wordpress-plugin
 * Plugin Name:       LED Tweaks
 * Plugin URI:        tweaks.liquidedge.co.nz
 * Description:       A suite of common customizations, shortcodes and tweaks for Wordpress and Themeco's Pro theme as used by Liquid Edge.
 * Version:           1.0.7
 * Author:            Brad Tipper
 * Author URI:        liquidedge.co.nz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       led-tweaks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'LED_TWEAKS_VERSION', '1.0.7' );

/**
 * Include styles & scripts.
 */

function led_enqueue_styles() {
	wp_enqueue_style( 'led-tweaks-styles', plugin_dir_url( __FILE__ ) . 'includes/led-tweaks-public.css', array(), $this->version, 'all' );
}
add_action('led_enqueue_styles', 'callback_for_setting_up_scripts');

function led_enqueue_scripts() {
	wp_enqueue_script( 'led-tweaks-scripts', plugin_dir_url( __FILE__ ) . 'includes/led-tweaks-public.js', array( 'jquery' ), $this->version, false );
}
add_action('led_enqueue_scripts', 'callback_for_setting_up_scripts');

/**
 * Extra functions.
 */

// Remove Portfolio CPT
// =============================================================================
add_action( 'after_setup_theme','led_remove_portfolio_cpt', 100 );
function led_remove_portfolio_cpt() {   
  remove_action( 'init', 'x_portfolio_init');    
}

// Year Shortcode
// =============================================================================
function led_year_shortcode() {
  $year = date('Y');
  return $year;
}
add_shortcode('year', 'led_year_shortcode');

// Today Date Shortcode
// =============================================================================
function led_today_shortcode() {
  return current_time( 'timestamp' );
}
add_shortcode('today', 'led_today_shortcode');

// Add Login info & loginout to menu
// =============================================================================
function led_login_nav_menu_item($items) {

	$current_user = wp_get_current_user();
	$name = $current_user->user_login;

	if( $args->theme_location == 'footer-menu')  {

		$info = '<li><a>Logged in as ' . $name . '</a></li>';

		$logout = '<li><a href="' . wp_logout_url() . '">Log out</a></li>';

		$login = '<li><a href="' . wp_login_url() . '">Log in</a></li>';
		
		if ( is_user_logged_in() ) {

			$items = $items . $info . $logout;

		} else {

			$items = $items . $login;

		}

	}

	return $items;

}
add_filter( 'wp_nav_menu_items', 'led_login_nav_menu_item' );

// GTM4WP
// =============================================================================
function led_gtm4wp_insert () {
	if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) {
		gtm4wp_the_gtm_tag();
	}
}
add_action('x_before_site_begin', 'led_gtm4wp_insert');

// Add Searchbox
// =============================================================================
function led_searchform( $form ) {
 
	$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
	<div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
	<input type="text" value="' . get_search_query() . '" name="s" id="s" />
	<input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
	</div>
	</form>';
 
	return $form;
}

add_shortcode('searchform', 'led_searchform');

// Login Status content display
// =============================================================================
function led_login_status_content_display() {

  if( ! current_user_can('administrator') ) {

	echo '<style type="text/css">.showAdmin { display: none !important; }</style>';

  }

  if ( is_user_logged_in() ) {

	echo '<style type="text/css">.showLoggedOut { display: none !important; }</style>';

  } else {

	echo '<style type="text/css">.showLoggedIn { display: none !important; }</style>';

  }

}
add_action('wp_head','led_login_status_content_display',0);

// Remove prefixes
// =============================================================================
function led_strip_archive_prefix( $title ) {

	if ( is_category() ) {

		$title = single_cat_title( '', false );

	} elseif ( is_tag() ) {

		$title = single_tag_title( '', false );

	} elseif ( is_author() ) {

		$title = '<span class="vcard">' . get_the_author() . '</span>';

	} elseif ( is_post_type_archive() ) {

		$title = post_type_archive_title( '', false );

	} elseif ( is_tax() ) {

		$title = single_term_title( '', false );

	}
  
	return $title;
}
 
add_filter( 'get_the_archive_title', 'led_strip_archive_prefix' );

// Fix Page Titles
// =============================================================================
function led_accurate_page_titles() {
 
	if ( is_single() || is_page() ) {

		return get_the_title();

	} elseif ( is_archive() ) {

		return get_the_archive_title( '', '' );

	}

}
add_shortcode( 'page-title', 'led_accurate_page_titles' );

// Child Pages
// =============================================================================
function led_list_child_pages() { 

	global $post; 
	 
	if ( is_page() && $post->post_parent )
		$childpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=' . $post->post_parent . '&echo=0' );
	else
		$childpages = wp_list_pages( 'sort_column=menu_order&title_li=&child_of=' . $post->ID . '&echo=0' );
	if ( $childpages ) {
		$string = '<ul class="led-child-pages">' . $childpages . '</ul>';
	}
	 
	return $string;

}
add_shortcode('child-pages', 'led_list_child_pages');

// URL Fixer
// =============================================================================
function led_url_fix( $atts ) {

	$field = $atts['field'];
	$class = $atts['class'];
	$newtab = $atts['newtab'];

	if ( $field != '') {
		$field_slug = "wpcf-".$field;
	} else {
		$field_slug = "wpcf-website";
	}

	if ( $newtab != 'false' ) {
		$target = 'target="_blank"';
	}

	global $post;
	$post_id = $post->ID;
	$stored_url = get_post_meta( $post_id, $field_slug, true);

	$cleaned_url = rtrim (
						str_replace(
							array('https://www.','https://','http://www.','http://'),
							array('','','',''),
							$stored_url
						),
						'/'
					);

	return '<a class="'.$class.'" '.$target.' href="'.$stored_url.'">'.$cleaned_url.'</a>';

}
add_shortcode( 'fixed-url', 'led_url_fix' );
