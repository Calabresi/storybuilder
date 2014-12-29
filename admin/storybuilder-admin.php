<?php
/*
 * Storybuilder admin functions
 * 
 */

// Ensure we're being called from WordPress
defined( 'ABSPATH' ) or die;

/* Enqueue the functions for the box */
add_action( 'add_meta_boxes', 'add_custom_box_wpse_14445' );
add_action( 'save_post', 'save_postdata_wpse_14445', 10, 2 );

/* Adds a box to the main column on the Post and Page edit screens */
function add_custom_box_wpse_14445() {
	add_meta_box(
		'sectionid_wpse_14445',
		__( 'Story Shortname' ),
		'inner_custom_box_wpse_14445',
		'post'
	);
}

/* Prints the box content */
function inner_custom_box_wpse_14445( $post ) {
	// use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'noncename_wpse_14445' );

	// The actual field for data entry
	echo '<label for="new_field_wpse_14445">';
	_e( "Story shortname:" );
	echo '<br />';
	echo '<input type="text" name="new_field_wpse_14445" id="new_field_wpse_14445"></label>';

}

/* When the post is saved, saves our custom data */
function save_postdata_wpse_14445( $post_id, $post_object ) {

	// verify if this came from our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['noncename_wpse_14445'], plugin_basename( __FILE__ ) ) )
		return;

	// Check permissions
	if ( 'post' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	}

	// OK, we're authenticated: we need to find and save the data

	// sanitize user input
	$mydata = mysql_real_escape_string( $_POST['new_field_wpse_14445'] );

	$time = current_time('mysql');
	global $current_user;
	get_currentuserinfo();

	$update_return = update_post_meta( $post_id, 'story_shortname', $mydata );

	/* Now that the post has been successfully saved, let's update the cache with the most current post results */
	$story = storybuilder_do_cache( $mydata );
	set_transient( 'storybldr_' . $mydata, $story );
}

/*
 * Admin menu options
 */
add_action( 'admin_menu', 'storybuilder_plugin_menu' );

function storybuilder_plugin_menu() {
	add_options_page( 'Storybuilder Options', 'Storybuilder', 'manage_options', 'storybuilder_14445', 'storybuilder_options' );
}

function storybuilder_options() {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( __('You do not have sufficient permisions to access this page.' ) );
	}
	echo '<div class="wrap"><h2>Storybuilder Options</h2>';
	echo '<p>Here is where the form would go, if I had one.';
	echo '</div>';
}
?>