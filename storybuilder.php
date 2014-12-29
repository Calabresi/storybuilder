<?php
/*
Plugin Name: StoryBuilder
Description: Allows editors to batch multiple stories about a single subject into one page.
Version: 0.1
Author: Matthew Calabresi
Description: StoryBuilder
*/

/****
*
* LIST OF TODOS:
*
* Add option to disable CSS
* Add option to edit CSS
*
****/

// Ensure we're being called from WordPress
defined( 'ABSPATH' ) or die;

// Register style sheet
add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );

/* Add the shortcode handlers */
add_shortcode( 'storyname', 'storyname_handler' );
add_shortcode( 'storyname_related', 'storyname_related_handler' );

/* Load common functions. */
require_once( dirname(__FILE__) . '/common/include.php');

/* Load admin stuff if we are in admin mode */
if ( is_admin ) {
	require_once( dirname(__FILE__) . '/admin/storybuilder-admin.php' );
	require_once( dirname(__FILE__) . '/admin/storybuilder-style-admin.php' );
}

/**
 * Register style sheet.
 */
function register_plugin_styles() {
	// Check if the custom stylesheet exists. If it does, load it.
	if ( file_exists( dirname(__FILE__) . '/css/storybuilder-custom.css' ) ) {
		wp_register_style( 'storybuilder', plugins_url( 'storybuilder/css/storybuilder-custom.css' ) );
	} else {
		// Otherwise, load the default stylesheet.
		wp_register_style( 'storybuilder', plugins_url( 'storybuilder/css/storybuilder.css' ) );
	}
	wp_enqueue_style( 'storybuilder' );
}

/* Shortcode handlers */
// [storyname shortname="value"]
// returns list of posts with excerpts with storyname "shortname"
function storyname_handler( $atts ) {
	$a = shortcode_atts( array(
		'shortname' => '',
		), $atts );
	if ( $a['shortname'] != '' ) { // don't run this if there was no shortname specified
		// Check for transient. If none, then execute WP_Query
		if ( false == ( $story = get_transient( 'storybldr_' . $a['shortname'] ) ) ) {
			$story = storybuilder_do_cache( $a['shortname'] );
			set_transient( 'storybldr_' . $a['shortname'], $story );
		}
	} else {
		$story = NULL;
	}
	$return_block = '';
	if ( $story->have_posts() ) {
		$return_block .= '<ul class="storylist storylist_' . $a['shortname'] . '" id="id_storylist_' . $a['shortname'] . '">';
		while ( $story->have_posts() ) {
			$story->the_post();
			$return_block .= '<li class="storylist-story"><div class="storylist-story-title"><a class="storylist-permalink" href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
			$return_block .= '<div class="storylist-excerpt">' . get_the_excerpt() . '</div>';
			$return_block .= '</li>';
		}
		$return_block .= '</ul>';
		wp_reset_postdata();
	}
	return $return_block;
}

// [storyname-related shortname="value"]
// returns list of posts (titles only, minus current post) with storyname "shortname"
function storyname_related_handler( $atts ) {
	$cur_postid = get_the_ID();
	$a = shortcode_atts( array(
		'shortname' => '',
		), $atts );
	if ( $a['shortname'] != '' ) { // don't run this if there was no shortname specified
		// Check for transient. If none, then execute WP_Query
		if ( false === ( $story = get_transient( 'storybldr_' . $a['shortname'] ) ) ) {
			$story = storybuilder_do_cache( $a['shortname'] );
			set_transient( 'storybldr_' . $a['shortname'], $story );
		}
	} else {
		$story = NULL;
	}
	$return_block = '';
	if ( $story->have_posts() ) {
		$return_block .= '<ul class="storylist storylist_' . $a['shortname'] . '" id="id_storylist_' . $a['shortname'] . '">';
		while ( $story->have_posts() ) {
			$story->the_post();
			if ( $cur_postid != get_the_ID() ) {
				$return_block .= '<li class="storylist-story"><div class="storylist-story-title"><a class="storylist-permalink" href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
				$return_block .= '</li>';
			}
		}
		$return_block .= '</ul>';
		wp_reset_postdata();
	}
	return $return_block;
}