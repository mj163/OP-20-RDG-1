<?php
/*--------------------------------------------------------------
Custom Functions
--------------------------------------------------------------*/

//insert your custom functions here





/*--------------------------------------------------------------
 * -------------------------------------------------------------
 * -------------------------------------------------------------
 *
 * DO NOT MODIFY THIS CODE - Theme will break
 *
 *
 * @import is no longer best practice for adding the Parent theme's CSS
 * All CSS is now added via wp_enqueue_style()
 * Child Theme's CSS now also loads adter all other CSS to ensure seamless override
 *
 * @since Karma 4.8
 */
function karma_child_enqueue_styles() {
	
	// enqueue child styles, priority set to 12 to load this CSS last
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() .'/style.css', array( 'style' ) );
	
}
add_action( 'wp_enqueue_scripts', 'karma_child_enqueue_styles', 12 );

?>