<?php

function truethemes_manage_javascripts_scripts() {

if ( !is_admin() ) {
/*-----------------------------------------------------------------------------------*/
/* Enqueue Styles.
/*-----------------------------------------------------------------------------------*/

global $ttso;
$primary_style         =  $ttso->ka_main_scheme;
$secondary_style       =  $ttso->ka_secondary_scheme;
$mobile_style          =  $ttso->ka_responsive;

//default style.css
wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css' );

//primary color css
wp_enqueue_style( 'primary-color', TRUETHEMES_CSS . $primary_style .'.css');

//@since 4.6 - combined secondary and primary CSS into singole primary file - 1 less HTTP request
if('default' != $secondary_style) :
	wp_enqueue_style( 'secondary-color', TRUETHEMES_CSS . $secondary_style .'.css');
endif;

//font-awesome
wp_enqueue_style( 'font-awesome', TRUETHEMES_CSS .'_font-awesome.css');

//woocommerce
if (class_exists('woocommerce')) :
	wp_enqueue_style( 'woocommerce', TRUETHEMES_CSS . '_woocommerce.css');
endif;

//mobile stylesheet
if('false' == $mobile_style) :
	wp_enqueue_style( 'mobile', TRUETHEMES_CSS . '_mobile.css');
endif;

/*-----------------------------------------------------------------------------------*/
/* Enqueue Scripts.
/*-----------------------------------------------------------------------------------*/
wp_enqueue_script( 'jquery');
wp_enqueue_script( 'truethemes-custom', TRUETHEMES_JS .'/custom-main.js', array(), NULL, true );
wp_enqueue_script( 'karma-superfish', TRUETHEMES_JS .'/superfish.js', array(), NULL, true );
wp_enqueue_script( 'retina_js', TRUETHEMES_JS .'/retina.js', array(), NULL, true );
wp_enqueue_script( 'karma-flexslider', TRUETHEMES_JS .'/jquery.flexslider.js', array(), NULL, true );
wp_enqueue_script( 'fitvids', TRUETHEMES_JS .'/jquery.fitvids.js', array(), NULL, true );
wp_enqueue_script( 'isotope', TRUETHEMES_JS .'/jquery.isotope.js', array(), NULL, true );
wp_enqueue_script( 'jquery-ui-core');
wp_enqueue_script( 'jquery-ui-widget');
wp_enqueue_script( 'jquery-ui-tabs');
wp_enqueue_script( 'jquery-ui-accordion');
wp_enqueue_script( 'pretty-photo', TRUETHEMES_JS .'/jquery.prettyPhoto.js', array(), NULL, true );

// Load our modified script if Visual Composer is active.
if( function_exists('vc_set_as_theme') ) {
	wp_deregister_script( 'flexslider' );
}

if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	wp_enqueue_script( 'comment-reply' );
}

/*
 * Grab site options for wp_localize()
 *
 */
//karma jquery slider(s)
$karma_jquery_slideshowSpeed     = $ttso->ka_karma_jquery_timeout;           // slide display time
$karma_jquery_pause_hover        = $ttso->ka_karma_jquery_pause_hover;       // pause jquery on hover?
$karma_jquery_randomize          = $ttso->ka_karma_jquery_randomize;         // randomize slides?
$karma_jquery_directionNav       = $ttso->ka_karma_jquery_directionNav;      // next-previous arrows
$karma_jquery_animation_effect   = $ttso->ka_karma_jquery_animation_effect;  // animation effect
$karma_jquery_animationSpeed     = $ttso->ka_karma_jquery_animationSpeed;    // animation speed
//testimonial slider(s)
$testimonial_randomize           = $ttso->ka_testimonial_randomize;          // randomize slides?
$testimonial_directionNav        = $ttso->ka_testimonial_directionNav;       // next-previous arrows
$testimonial_animation_effect    = $ttso->ka_testimonial_animation_effect;   // animation effect
$testimonial_animationSpeed      = $ttso->ka_testimonial_animationSpeed;     // slide display time
$testimonial_slideshowSpeed      = $ttso->ka_testimonial_timeout;            // animation speed
$testimonial_pause_hover         = $ttso->ka_testimonial_pause_hover;        // pause on hover?
//misc
$mobile_menu_text                = $ttso->ka_mobile_menu_text;               // main menu - mobile version - text (ie. Main Menu)
$mobile_sub_menu_text            = $ttso->ka_mobile_sub_menu_text;           // sub menu - mobile version - dropdown text
//$mobile_horz_dropdown_text       = $ttso->ka_mobile_horz_dropdown_text;      // horizontal sub menu - mobile version - dropdown text
$mobile_horz_dropdown            = $ttso->ka_mobile_horz_dropdown;           // horizontal sub menu - if true, convert to dropdown list
//$ubermenu                        = $ttso->ka_ubermenu_karma_styling;         // if "true" user has activated uberMenu styling.
$sticky_sidebar                  = $ttso->ka_sticky_sidebar;                 // if "true" user has activated sticky-sidebar
$sticky_menu_one                 = $ttso->ka_fix_header_and_menubar; 
$sticky_menu_two                 = $ttso->ka_fix_header_and_menubar_2; 

//pre-define retina logo for backward-compatible
if (@$retina_logo == ''){ @$retina_logo = 'no-retina'; }

//set the data into array to be used in wp_localize()
$data = array(
'mobile_menu_text'                  => $mobile_menu_text,
'mobile_sub_menu_text'              => $mobile_sub_menu_text,
//'mobile_horz_dropdown_text '        => $mobile_horz_dropdown_text,
'mobile_horz_dropdown'              => $mobile_horz_dropdown,
'karma_jquery_slideshowSpeed'       => $karma_jquery_slideshowSpeed,
'karma_jquery_pause_hover'          => $karma_jquery_pause_hover,
'karma_jquery_randomize'            => $karma_jquery_randomize,
'karma_jquery_directionNav'         => $karma_jquery_directionNav,
'karma_jquery_animation_effect'     => $karma_jquery_animation_effect,
'karma_jquery_animationSpeed'       => $karma_jquery_animationSpeed,
'testimonial_slideshowSpeed'        => $testimonial_slideshowSpeed,
'testimonial_pause_hover'           => $testimonial_pause_hover,
'testimonial_randomize'             => $testimonial_randomize,
'testimonial_directionNav'          => $testimonial_directionNav,
'testimonial_animation_effect'      => $testimonial_animation_effect,
'testimonial_animationSpeed'        => $testimonial_animationSpeed,
//'ubermenu_active'                   => $ubermenu_active,
'sticky_sidebar'                    => $sticky_sidebar,
'sticky_menu_one'                   => $sticky_menu_one,
'sticky_menu_two'                   => $sticky_menu_two,
);

//localize custom-main.js (must be placed after enqueue)
wp_localize_script( 'truethemes-custom', 'php_data', $data );

}; // END is_admin()
}; // END truethemes_manage_javascripts_scripts()

add_action( 'wp_enqueue_scripts', 'truethemes_manage_javascripts_scripts' );

/*-----------------------------------------------------------------------------------*/
/* Drag-to-share Social Bookmarking.
/*-----------------------------------------------------------------------------------*/
$dragshare = get_option( 'ka_dragshare' );
if ( $dragshare == "true" ) {

function dragshare_script_enqueue() {

//prettySociable Icons for wp_localize
define('PRETTYSOCIAL', get_template_directory_uri().'/images/_global/prettySociable/social_icons');
$pretty_delicious          = PRETTYSOCIAL.'/delicious.png';
$pretty_digg               = PRETTYSOCIAL.'/digg.png';
$pretty_facebook           = PRETTYSOCIAL.'/facebook.png';
$pretty_linkedin           = PRETTYSOCIAL.'/linkedin.png';
$pretty_reddit             = PRETTYSOCIAL.'/reddit.png';
$pretty_stumbleupon        = PRETTYSOCIAL.'/stumbleupon.png';
$pretty_tumblr             = PRETTYSOCIAL.'/tumblr.png';
$pretty_twitter            = PRETTYSOCIAL.'/twitter.png';

//set the data into array
$pretty_data = array(
'delicious'     => $pretty_delicious,
'digg'          => $pretty_digg,
'facebook'      => $pretty_facebook,
'linkedin'      => $pretty_linkedin,
'reddit'        => $pretty_reddit,
'stumbleupon'   => $pretty_stumbleupon,
'tumblr'        => $pretty_tumblr,
'twitter'       => $pretty_twitter,
);

if( is_single() || is_home() || is_archive() || is_category() || is_tag() || is_author() ) {
	wp_enqueue_script( 'pretty-sociable', TRUETHEMES_JS .'/jquery.prettySociable.js', array(), NULL, true );
	//localize prettySociable.js (must be placed after enqueue)		
	wp_localize_script('pretty-sociable', 'social_data', $pretty_data);
}; // END is_single()

}; // END dragshare_script_enqueue()

add_action( 'wp_enqueue_scripts', 'dragshare_script_enqueue' );

}; // END dragshare -> true
?>