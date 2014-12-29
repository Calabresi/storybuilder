<?php
/*
 *
 * storybuilder-style-admin.php
 *
 * The ability to edit styles lives here.
 *
 * Looks for the file css/storybuilder-custom.css. If it doesn't exist, it loads the content of the css/storybuilder.css
 * file into a textarea for editing.
 *
 * Upon save, the contents of the storybuilder.css file is compared with the contents of the text area.
 * If there is a difference, then the contents of the text area is saved as css/storybuilder-custom.css.
 *
 */

// ensure we're being called from WordPress
defined( 'ABSPATH' ) or die;

function storybuilder_build_css_textarea() {
	if ( file_exists( dirname(__FILE__) . '/css/storybuilder-custom.css' ) ) {
		$css_style = file_get_contents( dirname(__FILE__) . 'css/storybuilder-custom.css' );
	} else {
		$css_style = file_get_contents( dirname(__FILE__) . 'css/storybuilder.css') ;
	}
	if ( $css_style === false ) { // neither CSS file exists for some reason, recreate the default css

	}
}