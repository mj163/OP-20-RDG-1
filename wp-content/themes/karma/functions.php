<?php
/*-----------------------------------------------------------------------------------
Caution: The Sky may fall if you edit this file. Please proceed with caution. :)
-------------------------------------------------------------------------------------*/
define('TRUETHEMES_GLOBAL',         get_template_directory()     . '/framework');
define('TRUETHEMES_FRAMEWORK',      get_template_directory_uri() . '/framework');
define('TRUETHEMES_ADMIN',          get_template_directory()     . '/framework/admin');
define('TRUETHEMES_EXTENDED',       get_template_directory()     . '/framework/extended');
define('TRUETHEMES',                get_template_directory()     . '/framework/truethemes');
define('TRUETHEMES_JS',             get_template_directory_uri() . '/js');
define('TRUETHEMES_CSS',            get_template_directory_uri() . '/css/');
define('TIMTHUMB_SCRIPT',           get_template_directory_uri() . '/framework/extended/timthumb/timthumb.php');
define('TIMTHUMB_SCRIPT_MULTISITE', get_template_directory_uri() . '/framework/extended/timthumb/timthumb.php');
define('TRUETHEMES_HOME',           get_template_directory_uri());
$admin_url = admin_url(); // Used in site options.
/*-----------------------------------------------------------------------------------*/
/* Fire up the theme.
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'truethemes_karma_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function truethemes_karma_setup() {

	// Make theme available for translation.
	load_theme_textdomain( 'truethemes_localize' , get_template_directory() . '/languages');

	// This theme styles the visual editor to resemble the theme style.
	add_editor_style( 'custom-editor-style.css' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array(
		'image', 'video', 'audio', 'quote', 'gallery',
	) );

	// Allow shortcodes inside widgets
	add_filter('widget_text', 'do_shortcode');

	// Add support for WooCommerce.
	if ( class_exists( 'woocommerce' ) ) {
		require_once(TRUETHEMES_EXTENDED . '/woocommerce.php');
		add_theme_support( 'woocommerce' );
		add_theme_support( 'post-thumbnails' , array( 'post','product' ) );
	}

	// This theme uses wp_nav_menu() in three locations.
	register_nav_menus( array(
		'Primary Navigation'     => __( 'Main Menu', 'truethemes_localize' ),
		'Footer Navigation'      => __( 'Footer Menu', 'truethemes_localize' ),
		'Top Toolbar Navigation' => __( 'Top Toolbar Menu', 'truethemes_localize' ),
	) );

	// Remove junk from head.
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'feed_links_extra');
	remove_action('wp_head', 'feed_links');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

	// Set content width in pixels.
	if ( ! isset( $content_width ) ) $content_width = 960;

	// Disable branding notice -> one-click-importer plugin.
	add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
}
endif;
add_action( 'after_setup_theme', 'truethemes_karma_setup' );
/*-----------------------------------------------------------------------------------*/
/* TrueThemes Framework init
/*-----------------------------------------------------------------------------------*/
// Load Theme-Specific Functions
require_once(TRUETHEMES_GLOBAL   . '/script-enqueue.php');
require_once(TRUETHEMES_GLOBAL   . '/site-options.php');
require_once(TRUETHEMES_GLOBAL   . '/site-options-functions.php'); //formerly "admin-functions.php"
require_once(TRUETHEMES_GLOBAL   . '/custom-metaboxes.php');
require_once(TRUETHEMES_GLOBAL   . '/template-tags.php');
//check if plugin is active
//reference https://codex.wordpress.org/Function_Reference/is_plugin_active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'karma_builder/karma_builder.php' ) ) {
	//plugin is not active, we load the following files.
	// Load Global Elements
	require_once(TRUETHEMES_GLOBAL   . '/shortcodes.php');
	require_once(TRUETHEMES_GLOBAL   . '/shortcodes-old.php');
}
require_once(TRUETHEMES_GLOBAL   . '/widgets.php');
require_once(TRUETHEMES_GLOBAL   . '/theme-functions.php');
require_once(TRUETHEMES_GLOBAL   . '/nav-output.php');
require_once(TRUETHEMES_GLOBAL   . '/hooks.php');
// Load Karma Mega Menu
require_once(TRUETHEMES_GLOBAL   . '/karma-megamenu.php');
new karma_megamenu();
// Load TrueThemes Functions
require_once(TRUETHEMES          . '/wysiwyg/wysiwyg.php');
require_once(TRUETHEMES          . '/image-thumbs.php');
require_once(TRUETHEMES          . '/metabox/init.php');
// Load Site Options Admin
require_once(TRUETHEMES_ADMIN    . '/admin-functions.php');
require_once(TRUETHEMES_ADMIN    . '/admin-interface.php');
// Load Extended Functionality
require_once(TRUETHEMES_EXTENDED . '/tgm-plugin-activation/class-tgm-plugin-activation.php');
require_once(TRUETHEMES_EXTENDED . '/pricing-tables/pricing.php');
require_once(TRUETHEMES_EXTENDED . '/multiple_sidebars.php');
require_once(TRUETHEMES_EXTENDED . '/breadcrumbs.php');
require_once(TRUETHEMES_EXTENDED . '/3d-tag-cloud/wp-cumulus.php');
require_once(TRUETHEMES_EXTENDED . '/latest-tweets.php');
if ( ! class_exists( 'CWS_PageLinksTo' ) ) :
	require_once(TRUETHEMES_EXTENDED . '/page-links-to/page-links-to.php');
endif;
if ( ! function_exists( 'wp_pagenavi' ) ) :
	require_once(TRUETHEMES_EXTENDED . '/wp-pagenavi.php');
endif;
//check if user enables our theme contact form plugin, if yes, we use it.
$ka_formbuilder = get_option( 'ka_formbuilder' );
if ( $ka_formbuilder == "true" ) :
	require_once(TRUETHEMES_EXTENDED . '/grunion-contact-form/grunion-contact-form.php');
endif;
//TrueThemes Framework Global Variable
if(!isset($ttso)){
	$truethemes_site_option = new truethemes_site_option(); 
	$ttso                   = $truethemes_site_option->set_all(); // <-- very important
	}
/*-----------------------------------------------------------------------------------*/
/* Admin Enqueue Scripts
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'karma_admin_enqueue_scripts' ) ) :
/*
 * Hides old page templates from Page Attributes metabox
 * Hides old "Custom Settings" metabox from Post Editing screen
 *
 */
function karma_admin_enqueue_scripts() {

	// ----- karma 4.0
	wp_register_script( 'remove_old_karma', TRUETHEMES_JS .'/admin-remove-deprecated.js', array('jquery'),'1.0');
	wp_enqueue_script( 'remove_old_karma'); //this jQuery hides page templates + post metaboxes
	global $ttso;
	$old_karma_active = $ttso->ka_old_karma_active;
	if ('true' ==  $old_karma_active): 
		wp_dequeue_script( 'remove_old_karma'); //remove JS so old options appear
	endif;

	// ----- turns metaboxes into tabbed interface
	$screen = get_current_screen();
	if($screen->id == 'page'){
		wp_enqueue_script('B_metabox_tabber', TRUETHEMES_JS . '/B_metabox_tabber.js', array('jquery', 'jquery-ui-tabs'), false, true);
	}

}
endif;
add_action( 'admin_enqueue_scripts', 'karma_admin_enqueue_scripts' );
/*-----------------------------------------------------------------------------------*/
/* Fallback [raw][/raw] code
/*-----------------------------------------------------------------------------------*/
function fall_back_raw( $atts, $content = null ) {
	return do_shortcode($content);
}
add_shortcode('raw', 'fall_back_raw');
/*-----------------------------------------------------------------------------------*/
/* Mini shortcode formatter
/*-----------------------------------------------------------------------------------*/
add_filter('the_content', 'karma_shortcode_empty_paragraph_fix');
function karma_shortcode_empty_paragraph_fix($content) {   
    $array = array (
        '<p>[' => '[', 
        ']</p>' => ']', 
        ']<br />' => ']'
    );
    $content = strtr($content, $array);
	return $content;
}
/*----------------------------------------------------------------*/
/* JetPack
/*----------------------------------------------------------------*/
/*
 * since vesion 4.8.8
 * removed check for specific modules (causing PHP error)
 * now display when JetPack class exists
 *
 */
function tt_jetpack_contact_form_notice() {
$message = '
<div class="notice notice-warning is-dismissible"> 
	<p>Karma has detected the JetPack Contact Form enabled on this website. To ensure proper functioning please disable Karma\'s contact form if you prefer to use JetPack\'s contact form.<br /><br /><strong>Karma Settings:</strong> <a href="'.admin_url("themes.php?page=siteoptions").'">Appearance > Site Options</a> > Forms</p>
</div>';
echo $message;
}
if( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'contact-form' ) ) {
		if( current_user_can('administrator')){ //show only to admin and not subscribers
			add_action( 'admin_notices','tt_jetpack_contact_form_notice' );
		}
}
/*-----------------------------------------------------------------------------------*/
/* <!--more--> disable scroll
/*-----------------------------------------------------------------------------------*/
function remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );
/*-----------------------------------------------------------------------------------*/
/*	WP Theme Customizer
/*-----------------------------------------------------------------------------------*/
//add seections
function truethemes_customizer( $wp_customize ) {
    $wp_customize->add_section(
        'tt_customizer_footer_copyright',
        array(
            'title'       => __( 'Footer Copyright' , 'truethemes_localize'),
            'description' => __( 'Add Copyright information to the Footer.' , 'truethemes_localize'),
            'priority'    => 1000,
        )
    );	
//add settings
	$wp_customize->add_setting(
    	'footer_copyright_textbox',
		array(
        'type' => 'theme_mod',
    	)
);
//add controls (settings will not display with a control)
	$wp_customize->add_control(
    	'footer_copyright_textbox',
    	array(
        'label'   => __( 'Footer &copy; Copyright Text' , 'truethemes_localize'),
        'section' => 'tt_customizer_footer_copyright',
        'type'    => 'text',
    )
);
}
add_action( 'customize_register', 'truethemes_customizer' );
/*-----------------------------------------------------------------------------------*/
/* Register Sidebars
/*-----------------------------------------------------------------------------------*/
function karma_widgets_init() {
	register_sidebar( array(
	'id'            => 'sidebar-1',
	'name'          => 'Toolbar - Left Side',
	'description'   => 'Add a Widget to this region or easily assign a Menu by clicking on Appearance > Menus.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<p class="top-block-title">',
	'after_title'   => '</p>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-2',
	'name'          => 'Toolbar - Right Side',
	'description'   => 'This region is located on the right side above the main navigation',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<p class="top-block-title">',
	'after_title'   => '</p>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-3',
	'name'          => 'Blog Sidebar',
	'description'   => 'This sidebar is displayed on all Blog pages.',
	'before_widget' => '<div class="sidebar-widget">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-4',
	'name'          => 'Search Results Sidebar',
	'description'   => 'This sidebar is displayed on the Search Results page.',
	'before_widget' => '<div class="sidebar-widget">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-5',
	'name'          => 'Contact Sidebar (iPhone)',
	'description'   => 'This sidebar is displayed within the iPhone screen on the Contact page.',
	'before_widget' => '<div class="sidebar-widget sidebar-smartphone">',
	'after_widget'  => '</div>',
	'before_title'  => '<h4 class="smartphone-header">',
	'after_title'   => '</h4>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-6',
	'name'          => 'First Footer Column',
	'description'   => 'First Footer Column.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-7',
	'name'          => 'Second Footer Column',
	'description'   => 'Second Footer Column.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-8',
	'name'          => 'Third Footer Column',
	'description'   => 'Third Footer Column.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-9',
	'name'          => 'Fourth Footer Column',
	'description'   => 'Fourth Footer Column.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-10',
	'name'          => 'Fifth Footer Column',
	'description'   => 'Fifth Footer Column.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-11',
	'name'          => 'Sixth Footer Column',
	'description'   => 'Sixth Footer Column.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-12',
	'name'          =>  'Footer Copyright - Left Side',
	'description'   => 'Add copyright info by clicking on Appearance > Customize.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '',
	'after_title'   => '',
	));
	register_sidebar( array(
	'id'            => 'sidebar-13',
	'name'          =>  'Footer Menu - Right Side',
	'description'   => 'Assign a menu to this region by clicking on Appearance > Menus.',
	'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '',
	'after_title'   => '',
	));
	register_sidebar( array(
	'id'            => 'sidebar-14',
	'name'          => 'WooCommerce Sidebar',
	'description'   => 'This sidebar is displayed on your WooCommerce pages.',
	'before_widget' => '<div class="sidebar-widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'sidebar-15',
	'name'          => 'WooCommerce - Cart + Checkout',
	'description'   => 'This sidebar is displayed on your WooCommerce Shopping Cart and Checkout pages.',
	'before_widget' => '<div class="sidebar-widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
	register_sidebar( array(
	'id'            => 'bb-press-sidebar',
	'name'          => 'bbPress Forum',
	'description'   => 'This sidebar is displayed on bbPress Forum pages.',
	'before_widget' => '<div class="sidebar-widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
	));
} // END karma_widgets_init()
add_action( 'widgets_init', 'karma_widgets_init' );
/*-----------------------------------------------------------------------------------*/
/* Register Custom Taxonomies
/*-----------------------------------------------------------------------------------*/
//Slider Taxonomy
function truethemes_karma_slider_taxonomy() {
	register_taxonomy(
		'karma-slider-category',
		'karma-slider',
		array(
			'label'        => __('Categories' , 'truethemes_localize'),
			'sort'         => true,
			'hierarchical' => true,
			'args'         => array( 'orderby' => 'term_order' ),
			'rewrite'      => array( 'slug'    => 'karma-slider-category' )
		)
	);
}
add_action( 'init', 'truethemes_karma_slider_taxonomy' );
//Gallery Taxonomy
function truethemes_karma_gallery_taxonomy() {
	register_taxonomy(
		'truethemes-gallery-category',
		'tt-gallery',
		array(
			'label'        => __('Categories' , 'truethemes_localize'),
			'sort'         => true,
			'hierarchical' => true,
			'args'         => array( 'orderby' => 'term_order' ),
			'rewrite'      => array( 'slug'    => 'truethemes-gallery-category' )
		)
	);
}
add_action( 'init', 'truethemes_karma_gallery_taxonomy' );
/*-----------------------------------------------------------------------------------*/
/*	Register Custom Post Types
/*-----------------------------------------------------------------------------------*/
//Slider Post Type
function truethemes_post_type_slider() {
	$labels = array(
		'name'          => __( 'Slider Posts' , 'truethemes_localize'),
		'singular_name' => __( 'Slider Post' , 'truethemes_localize'),
		'rewrite'       => array(
		'slug'               => __( 'slider' , 'truethemes_localize')),
		'add_new'            => __('Add New' , 'truethemes_localize'),
		'add_new_item'       => __('Add New Slider Post' , 'truethemes_localize'),
		'edit_item'          => __('Edit Slider Post' , 'truethemes_localize'),
		'new_item'           => __('New Slider Post' , 'truethemes_localize'),
		'view_item'          => __('View Slider Post' , 'truethemes_localize'),
		'search_items'       => __('Search Slider Posts' , 'truethemes_localize'),
		'not_found'          =>  __('No Slider Posts found' , 'truethemes_localize'),
		'not_found_in_trash' => __('No Slider Posts found in Trash' , 'truethemes_localize'), 
		'parent_item_colon'  => ''
	  );
	  $args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true, 
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => 6,
		'supports'           => array('title' , 'editor')
	  ); 
	  
	  register_post_type( 'karma-slider', $args );
}
add_action( 'init', 'truethemes_post_type_slider' );
//Gallery Post Type
function truethemes_post_type_gallery() 
{
	$labels = array(
		'name'               => __( 'Gallery Posts' , 'truethemes_localize'),
		'singular_name'      => __( 'Gallery Post' , 'truethemes_localize'),
		'rewrite'            => array(
		'slug'               => __( 'gallery' , 'truethemes_localize')),
		'add_new'            => __('Add New' , 'truethemes_localize'),
		'add_new_item'       => __('Add New Gallery Post' , 'truethemes_localize'),
		'edit_item'          => __('Edit Gallery Post' , 'truethemes_localize'),
		'new_item'           => __('New Gallery Post' , 'truethemes_localize'),
		'view_item'          => __('View Gallery Post' , 'truethemes_localize'),
		'search_items'       => __('Search Gallery Posts' , 'truethemes_localize'),
		'not_found'          =>  __('No Gallery Posts found' , 'truethemes_localize'),
		'not_found_in_trash' => __('No Gallery Posts found in Trash' , 'truethemes_localize'), 
		'parent_item_colon'  => ''
	  );
	  $args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true, 
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => 5,
		'supports'           => array('title')
	  ); 
	  
	  register_post_type( 'tt-gallery' ,$args );
}
add_action( 'init', 'truethemes_post_type_gallery' );
/*-----------------------------------------------------------------------------------*/
/*	WP-Admin CSS for Custom Post Types + Karma Welcome Screen
/*-----------------------------------------------------------------------------------*/
function truethemes_custom_admin_css(){
	echo '<style>
/*--------------------------------------*/
/* Karma - Pages - Metabox Tabs
/*--------------------------------------*/
#b_tabbed_meta_boxes .ui-tabs-nav {
	text-align: left;
	margin-bottom: 0 !important;
}
#b_tabbed_meta_boxes .ui-tabs-nav li {
	background-color: #E5E5E5;
	display: inline-block;
	margin: 0;
	margin-right: 3px;
}
#b_tabbed_meta_boxes .ui-tabs-nav .ui-tabs-active a {
	background: #FFF !important;
	color: #23282d;
}
#b_tabbed_meta_boxes .ui-tabs-nav li.ui-tabs-active {
	margin-bottom: -1px;
	z-index: 1;
}
#b_tabbed_meta_boxes .ui-tabs-nav li a {
	box-shadow: none !important;
	color: #555;
	display: inline-block;
	font-size: 14px;
	font-weight: 600;
	outline: none;
    padding: 9px 14px;
    text-decoration: none;
}
#b_tabbed_meta_boxes .ui-tabs-nav li a:hover {
	background: #CCC;
}
#b_tabbed_meta_boxes h2.hndle.ui-sortable-handle span,
#b_tabbed_meta_boxes h2.hndle.ui-sortable-handle {
	border-bottom: 0;
	visibility: hidden;
}
#b_tabbed_meta_boxes .postbox {
	border-top: 0;
}
/*--------------------------------------*/
/* Karma Admin Styles - Post Types
/*--------------------------------------*/
#adminmenu #menu-posts-karma-slider .menu-icon-post div.wp-menu-image:before {
	content: "\f169";
	/* content: "\f181"; */
}
#adminmenu #menu-posts-tt-gallery .menu-icon-post div.wp-menu-image:before {
	content: "\f233";
}
#adminmenu #menu-posts-feedback .menu-icon-post div.wp-menu-image:before {
	content: "\f175";
}
.wp-media-buttons .tt-add-form span.wp-media-buttons-icon:before {
	font: 400 17px/1 dashicons;
	content: "\f175";
	margin-left:-1px;
}
/* Social Media Widget select field */
	.wp-admin #tt-social-widget-dropdown {
	width:95% !important;	
}
/* hide revolution slider notice */
.rs-update-notice-wrap {
	display: none;
}
/*--------------------------------------*/
/* Karma Admin Styles - Welcome Screen
/*--------------------------------------*/
body.appearance_page_karma-welcome .wp-badge {
	background: #FFF url('.get_template_directory_uri().'/images/_global/karma-welcome-logo.jpg) center center no-repeat !important;
}
.karma_welcome-feature {
	background: #fff none repeat scroll 0 0;
	border: 1px solid rgb(221, 221, 221);
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    margin: 20px 0;
    padding: 30px;
}
.karma-feature-section {
	padding-bottom: 30px;
	overflow: auto;
}
.karma-feature-section .three-col {
    float: left;
    margin-right: 5%;
    position: relative;
    width: 29.95%;
}
.karma-feature-section .three-col.last {
    margin-right: 0;
}
.karma_welcome-feature span.dashicons {
	background: #86b946 none repeat scroll 0 0;
    border-radius: 50px;
    color: #fff;
    display: inline-block;
    font-size: 24px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    width: 50px;
}
/*-------------------------------------------------------------- 
Karma Mega Menu
--------------------------------------------------------------*/
.karma_mega_active.menu-item-depth-2 .field-description,
.karma_mega_active.menu-item-depth-3 .field-description,
.karma_mega_active.menu-item-depth-4 .field-description,
.karma_mega_active.menu-item-depth-5 .field-description,
.karma_mega_active.menu-item-depth-6 .field-description,
.karma_mega_active.menu-item-depth-7 .field-description,
.karma_mega_active.menu-item-depth-8 .field-description,
.menu-item-depth-0 .karma_mega_menu_d0,
.karma_mega_active.menu-item-depth-1 .karma_mega_menu_d1,
.karma_mega_active.menu-item-depth-2 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-3 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-4 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-5 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-6 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-7 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-8 .karma_mega_menu_d2,
.karma_mega_active.menu-item-depth-1 .item-type-karma,
.karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section {
 display: block;
}
.item-type-karma-mega-section,
.karma_mega_menu,
.item-type-karma,
.karma_mega_label,
.karma_mega_active.menu-item-depth-2 .description-title,
.karma_mega_active.menu-item-depth-3 .description-title,
.karma_mega_active.menu-item-depth-1 .karma_default_label,
.karma_mega_active.menu-item-depth-1 .field-url,
.karma_mega_active.menu-item-depth-1 .field-description,
.karma_mega_active.menu-item-depth-0 .item-type-default,
.karma_mega_active.menu-item-depth-1 .link-to-original,
.karma_mega_active.menu-item-depth-1 .description-title,
.karma_mega_active.menu-item-depth-1 .item-type-default {
	display: none;
}
.karma_mega_active.menu-item-depth-1 .karma_mega_label {
	display: inline;
}
.karma_mega_active.menu-item-depth-1 .item-type-karma,
.karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section {
    border-radius: 100px;
    color: #FFF;
    font-size: 10px;
    margin: 9px 8px 11px 11px;
    padding: 3px 10px;
}
/* default */
.karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #222; }
.karma_mega_active.menu-item-depth-1 .item-type-karma { background: #0074A2; }
/* light */
.admin-color-light .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #d64e07; }
.admin-color-light .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #888; }
/* blue */
.admin-color-blue .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #52ACCC; }
.admin-color-blue .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #096484; }
/* coffee */
.admin-color-coffee .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #59524C; }
.admin-color-coffee .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #C7A589; }
/* ectoplasm */
.admin-color-ectoplasm .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #523F6D; }
.admin-color-ectoplasm .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #A3B745; }
/* midnight */
.admin-color-midnight .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #363B3F; }
.admin-color-midnight .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #E14D43; }
/* ocean */
.admin-color-ocean .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #738E96; }
.admin-color-ocean .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #9EBAA0; }
/* sunrise */
.admin-color-sunrise .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section { background: #CF4944; }
.admin-color-sunrise .karma_mega_active.menu-item-depth-1 .item-type-karma { background: #DD823B; }
#wpwrap .karma_mega_menu_options .karma_checkbox input{
	display: block;
	float: left;
	margin: 2px 7px 11px 0;
}
.karma_conditional_checkbox input {
	display: block;
	float: left;
	margin-bottom: 11px;
	margin-right: 7px;
}
.karma_long_desc{
	display: block;
	overflow: hidden;
}
/* .karma_mega_active .karma_label_desc_on_active .karma_small_desc {
	color: #777;
	display: inline-block;
	font-size: 12px;
	padding-bottom: 5px;
} */
.branch-3-4 .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section
.branch-3-5 .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section
.branch-3-6 .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section
.branch-3-7 .karma_mega_active.menu-item-depth-0 .item-type-karma-mega-section {
	display:inline;
	font-size: 9px;
	font-style: italic;
}
.karma_mega_active.menu-item-depth-1 .karma_label_desc_on_active {
	height:auto;
	width: auto;
	float: none;
}
.menu-item-edit-active .menu-item-settings {overflow: auto;}
/*--------------------------------------------*/
/* One Click Demo Import
/*--------------------------------------------*/
div.ocdi.wrap.about-wrap h1 {
    font-size: 2.5em;
    margin: 0.2em 200px 15px 0;
}
div.ocdi__intro-text { display: none; }
div.ocdi__demo-import-notice.js-ocdi-demo-import-notice p.about-description { margin-top:0; }
div.ocdi__demo-import-notice.js-ocdi-demo-import-notice ul {list-style-type: square;padding: 0 0 0 15px;}
div.ocdi__demo-import-notice.js-ocdi-demo-import-notice li {margin-bottom: 8px;}
	</style>';        
}
add_action('admin_head','truethemes_custom_admin_css');
/*-----------------------------------------------------------------------------------*/
/* TGM Plugin Activation (LayerSlider, etc)
/*-----------------------------------------------------------------------------------*/
add_action( 'tgmpa_register', 'truethemes_register_required_plugins' );
function truethemes_register_required_plugins() {
	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		// Include Premium Plugins:
		array(
			'name'     				=> 'Visual Composer', // The plugin name
			'slug'     				=> 'js_composer', // The plugin slug (typically the folder name)
			'source'   				=> 'http://s3.truethemes.net/theme-included-plugins/js_composer.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Karma Builder', // The plugin name
			'slug'     				=> 'karma_builder', // The plugin slug (typically the folder name)
			'source'   				=> 'http://s3.truethemes.net/theme-included-plugins/karma_builder.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'LayerSlider', // The plugin name
			'slug'     				=> 'LayerSlider', // The plugin slug (typically the folder name)
			'source'   				=> 'http://s3.truethemes.net/theme-included-plugins/layersliderwp.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		array(
			'name'     				=> 'Revolution Slider', // The plugin name
			'slug'     				=> 'revslider', // The plugin slug (typically the folder name)
			'source'   				=> 'http://s3.truethemes.net/theme-included-plugins/revslider.zip', // The plugin source
			'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		// Plugins from the WordPress Plugin Repository:
		array(
			'name' 		=> 'One Click Demo Import',
			'slug' 		=> 'one-click-demo-import',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'WooCommerce',
			'slug' 		=> 'woocommerce',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'bbPress',
			'slug' 		=> 'bbpress',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'MailChimp',
			'slug' 		=> 'mailchimp',
			'required' 	=> false,
		),
		array(
			'name' 		=> 'All in One SEO Pack',
			'slug' 		=> 'all-in-one-seo-pack',
			'required' 	=> false,
		),
	);
	// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 'truethemes_localize';
	$config = array(
		'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                    // Automatically activate plugins after installation or not.
		'message' 	   => '', 					   // Message to output right before the plugins table */
		'strings'      => array(
			'page_title'                      => __( 'Install Plugins', $theme_text_domain ),
			'menu_title'                      => __( 'Install Plugins', $theme_text_domain ),
			'installing'                      => __( 'Installing Plugin: %s', $theme_text_domain ), // %s = plugin name.
			'updating'                        => __( 'Updating Plugin: %s', $theme_text_domain ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', $theme_text_domain ),
			'notice_can_install_required'     => _n_noop(
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				$theme_text_domain
			), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop(
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				$theme_text_domain
			), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop(
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				$theme_text_domain
			), // %1$s = plugin name(s).
			'notice_ask_to_update_maybe'      => _n_noop(
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				$theme_text_domain
			), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop(
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				$theme_text_domain
			), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop(
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				$theme_text_domain
			), // %1$s = plugin name(s).
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				$theme_text_domain
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				$theme_text_domain
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				$theme_text_domain
			),
			'return'                          => __( 'Return to Plugin Installer', $theme_text_domain ),
			'plugin_activated'                => __( 'Plugin activated successfully.', $theme_text_domain ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', $theme_text_domain ),
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', $theme_text_domain ),  // %1$s = plugin name(s).
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', $theme_text_domain ),  // %1$s = plugin name(s).
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', $theme_text_domain ), // %s = dashboard link.
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', $theme_text_domain ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', $theme_text_domain ),
			'nag_type'                        => 'updated', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
	);
	if(current_user_can('administrator')){ //do this only for admin and not subscribers
		tgmpa( $plugins, $config );
	}
}
/*-----------------------------------------------------------------------------------*/
/* One Click Demo Importer
/*-----------------------------------------------------------------------------------*/
//@since 4.8.5
function truethemes_karma_import_files() {
    return array(
        array(
            'import_file_name'           => 'Main Demo',
            //'categories'                 => array( 'Category 1', 'Category 2' ),
            'import_file_url'            => 'http://s3.truethemes.net.s3.amazonaws.com/demo-importer-content/karma/karma-default-demo-content.xml',
            'import_widget_file_url'     => 'http://s3.truethemes.net.s3.amazonaws.com/demo-importer-content/karma/karma-default-demo-widgets.wie',
            //'import_customizer_file_url' => 'http://www.your_domain.com/ocdi/customizer.dat',
            /* 'import_redux'               => array(
                array(
                    'file_url'    => 'http://www.your_domain.com/ocdi/redux.json',
                    'option_name' => 'redux_option_name',
                ),
            ), */
            //'import_preview_image_url'   => 'http://www.your_domain.com/ocdi/preview_import_image1.jpg',
            'import_notice'              => __( '<h3>Welcome</h3><p class="about-description">Importing demo data is the easiest way to setup your theme. It will allow you to quickly edit everything instead of creating content from scratch. Posts, pages, images, widgets, menus and other theme settings will get automatically imported. Please be patient as it may take a few minutes to complete.</p><h3>Installation</h3><ul><li>Start the Import by clicking the button below. Please click the button only one time</li><li>You\'ll receive a notification on this page once the Import has completed.</li><li>Enjoy :)</li><ul>', 'tt_theme_framework' ),
        ),
    );
}
add_filter( 'pt-ocdi/import_files', 'truethemes_karma_import_files' );
// ----- Set Menus and Front Page
function truethemes_karma_after_import_setup() {
    // Assign menus to their locations.
    $main_menu    = get_term_by( 'name', 'Main Menu', 'nav_menu' );
    $footer_menu  = get_term_by( 'name', 'Footer Menu', 'nav_menu' );
    $toolbar_menu = get_term_by( 'name', 'Top Toolbar Menu', 'nav_menu' );
    set_theme_mod( 'nav_menu_locations', array(
            'Primary Navigation'     => $main_menu->term_id,
            'Footer Navigation'      => $footer_menu->term_id,
            'Top Toolbar Navigation' => $toolbar_menu->term_id,
        )
    );
    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    $blog_page_id  = get_page_by_title( 'Blog' );
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );
}
add_action( 'pt-ocdi/after_import', 'truethemes_karma_after_import_setup' );
/*-----------------------------------------------------------------------------------*/
/* Miscellaneous Settings
/*-----------------------------------------------------------------------------------*/
//
// ----- Remove rel="category" for HTML5 validation
//
add_filter( 'the_category', 'add_nofollow_cat' ); 
function add_nofollow_cat( $text ) {
$text = str_replace('rel="category tag"', "", $text); return $text;
}
//
// ----- Custom content length for blog page
//
function limit_content($content_length = 250, $allowtags = true, $allowedtags = '') {
global $post;
$content = $post->post_content;
$content = strip_shortcodes($content);
$content = apply_filters('the_content', $content);
if (!$allowtags){
	$allowedtags .= '<style>';
	$content = strip_tags($content, $allowedtags);
}
$wordarray = explode(' ', $content, $content_length + 1);
if(count($wordarray) > $content_length) :
	array_pop($wordarray);
	array_push($wordarray, '...');
	$content = implode(' ', $wordarray);
	$content = force_balance_tags($content);
endif;
echo $content;
}
//
// ----- Modify blog post excerpt length
//
function wp_new_excerpt($text)
{
	if ($text == '')
	{
		$text = get_the_content('');
		$text = strip_shortcodes( $text );
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]>', $text);
		$text = strip_tags($text);
		$text = nl2br($text);
		$excerpt_length = apply_filters('excerpt_length', 80);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			array_push($words, '...');
			$text = implode(' ', $words);
		}
	}
	return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wp_new_excerpt');
//
// ----- Modify Tag Cloud Widget
//
function truethemes_tag_cloud_widget($args) {
	$args['largest']  = 15;
	$args['smallest'] = 12;
	$args['unit']     = 'px';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'truethemes_tag_cloud_widget' );
//
// ----- Hide unnecessary user profile fields
//
add_filter('user_contactmethods','hide_profile_fields',10,1);
function hide_profile_fields( $contactmethods ) {
unset($contactmethods['aim']);
unset($contactmethods['jabber']);
unset($contactmethods['yim']);
return $contactmethods;
}
//walker class for filtered gallery template
class truethemes_gallery_walker extends Walker_Category {
   function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
      extract($args);
      $cat_name = esc_attr( $category->name);
      $cat_name = apply_filters( 'list_cats', $cat_name, $category );
	  $link = '<a href="#" data-filter=".term-'.$category->term_id.'" ';
      if ( $use_desc_for_title == 0 || empty($category->description) )
         $link .= 'title="' . sprintf(__( 'View all items filed under %s' , 'truethemes_localize'), $cat_name) . '"';
      else
         $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
      $link .= '>';
      // $link .= $cat_name . '</a>';
      $link .= $cat_name;
      if(!empty($category->description)) {
         $link .= ' <span>'.$category->description.'</span>';
      }
      $link .= '</a>';
      if ( (! empty($feed_image)) || (! empty($feed)) ) {
         $link .= ' ';
         if ( empty($feed_image) )
            $link .= '(';
         $link .= '<a href="' . get_category_feed_link($category->term_id, $feed_type) . '"';
         if ( empty($feed) )
            $alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' , 'truethemes_localize'), esc_attr( $cat_name ) ) . '"';
         else {
            $title = ' title="' . $feed . '"';
            $alt = ' alt="' . $feed . '"';
            $name = $feed;
            $link .= $title;
         }
         $link .= '>';
         if ( empty($feed_image) )
            $link .= $name;
         else
            $link .= "<img src='" . esc_url( $feed_image ) . "'$alt$title" . ' />';
         $link .= '</a>';
         if ( empty($feed_image) )
            $link .= ')';
      }
      if ( isset($show_count) && $show_count )
         $link .= ' (' . intval($category->count) . ')';
      if ( isset($show_date) && $show_date ) {
         $link .= ' ' . gmdate('Y-m-d', $category->last_update_timestamp);
      }
      if ( isset($current_category) && $current_category )
         $_current_category = get_category( $current_category );
      if ( 'list' == $args['style'] ) {
          $output .= '<li class="segment-'.rand(2, 99).'"';
          $class = 'cat-item cat-item-'.$category->term_id;
          if ( isset($current_category) && $current_category && ($category->term_id == $current_category) )
             $class .=  ' current-cat';
          elseif ( isset($_current_category) && $_current_category && ($category->term_id == $_current_category->parent) )
             $class .=  ' current-cat-parent';
          $output .=  '';
          $output .= ">$link\n";
       } else {
          $output .= "\t$link<br />\n";
       }
   }
}
//
// ----- IE9+ YouTube Video Fix
//
add_filter("embed_oembed_html", "add_wmode");
function add_wmode($html) {
	$html = str_replace("feature=oembed", "feature=oembed&wmode=transparent", $html);
	return $html;
}
/*-----------------------------------------------------------------------------------*/
/* Sticky Menu
/*-----------------------------------------------------------------------------------*/
/*
* function to hook jQuery to footer to activate sticky menu according to site option setting.
*/
if ( ! function_exists( 'tt_hook_sticky_menu' ) ) :
	function tt_hook_sticky_menu(){
		$activate_sticky_menu   = get_option('ka_fix_header_and_menubar');
		$activate_sticky_menu_2 = get_option('ka_fix_header_and_menubar_2');
		$activate_sticky_menu   = apply_filters('stickymenu',$activate_sticky_menu); //karma filter
		$activate_sticky_menu_2 = apply_filters('stickymenu2',$activate_sticky_menu_2); //karma filter
		$menu_type = 1;
		if ($activate_sticky_menu_2 == 'true') { $menu_type = 2; }
			if($activate_sticky_menu == 'true' || $activate_sticky_menu_2 == 'true'){
			wp_enqueue_script( 'scrollwatch', TRUETHEMES_JS .'/scrollWatch.js', array(), NULL, true );
			echo "\n<script type='text/javascript'>jQuery(document).ready(function(){if (jQuery(window).width() > 770) {truethemes_StickyMenu(" . $menu_type . ");}});</script>\n";
			}
	}
endif;
add_action( 'wp_footer' , 'tt_hook_sticky_menu' );
/*-----------------------------------------------------------------------------------*/
/* User Site Options that tap into wp_footer()
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'tt_karma_wp_footer' ) ) :
	function tt_karma_wp_footer() {
		// grab values
		$tt_analytics           = get_option('ka_google_analytics'); //analytics code
		$tt_custom_script       = get_option('ka_customcode_body'); //custom code
		$ka_scrolltoplink       = get_option('ka_scrolltoplink'); //scroll-to-top link

		// do stuff
		if( !empty ( $tt_analytics )): echo stripslashes( $tt_analytics ). "\n"; endif;
		if( !empty ( $tt_custom_script )): echo stripslashes( $tt_custom_script ). "\n"; endif;
		if( !empty ( $ka_scrolltoplink )): echo '<a href="#0" class="karma-scroll-top"><i class="fa fa-chevron-up"></i></a>'. "\n"; endif;
	}
endif;
add_action( 'wp_footer' , 'tt_karma_wp_footer' , 1000 );
/*-----------------------------------------------------------------------------------*/
/* Flexslider slide animation RTL css fixes
/* @since 4.0.5 dev 7
/*-----------------------------------------------------------------------------------*/
function tt_flexslider_rtl_slide_animation_css_fixes(){
$css = "\n\n<style type='text/css'>\n";
$css .= ".flex-viewport { direction: ltr; }\n";
$css .= ".flex-viewport .slider-content-main{text-align:right}";
$css .= ".jquery2-slider-wrap .slider-content-sub img, .jquery2-slider-wrap .slider-content-sub-full-width img{margin:10px 0 0 10px !important;}";
$css .= ".jquery2-slider-wrap .slider-content-sub-full-width {margin: 40px 0 0 30px;}";
$css .="</style>\n\n";
echo $css;
}
if(is_rtl()){
	$slide_animation = get_option('ka_karma_jquery_animation_effect');
	if($slide_animation == 'slide'){
	add_action('wp_head','tt_flexslider_rtl_slide_animation_css_fixes',99);
	}
}
/*-----------------------------------------------------------------------------------*/
/* Custom body_class()
/*-----------------------------------------------------------------------------------*/
function tt_add_body_classes( $classes ) {
$disable_indicator_arrows   = get_option('ka_nav_indicator_arrows');
$disable_nav_description    = get_option('ka_nav_description');
$disable_nav_description    = apply_filters('nodesc',$disable_nav_description); //karma filter
$disable_dropdown           = get_option('ka_dropdown');
$disable_post_date          = get_option('ka_post_date');
$disable_gradient           = get_option('ka_true_content_gradient');
$body_footer_bottom         = get_option('ka_footer_layout');
$disable_mobile_subs        = get_option('ka_mobile_menu_subs');
$karma_global_flat          = get_option('ka_karma_global_flat');
$karma_header_style         = get_option('ka_header_design_style');
$karma_foot_center          = get_option('ka_footer_center_copyright');
$karma_foot_center          = apply_filters('footcenter',$karma_foot_center); //karma filter
$karma_disable_horz_menu    = get_option('ka_disable_horz_menu');//@since 4.8
$karma_active_mega_menu     = get_option('ka_active_mega_menu');//@since 4.8
$karma_nav_left_border      = get_option('ka_nav_left_border');//@since 4.8
$karma_nav_left_border      = apply_filters('navborder',$karma_nav_left_border); //karma filter
$karma_mobile_horz_dropdown = get_option('ka_mobile_horz_dropdown');//@since 4.8.2
$karma_post_date_year		= get_option('ka_post_date_year');
$sticky_menu_thin_style		= get_option('ka_sticky_menu_thin_style');
//$karma_header_style       = apply_filters('headerstyle',$headerstyle); //karma filter
if( $karma_nav_left_border      == 'true') { $classes[] = 'karma-no-nav-border'; }
if( $karma_active_mega_menu     == 'true' ) { $classes[] = 'karma-body-mega-menu'; }
if( $disable_indicator_arrows   == 'true' ) { $classes[] = 'karma-menu-no-indicator'; }
if( $disable_nav_description    == 'true' ) { $classes[] = 'karma-menu-no-description'; }
if( $disable_dropdown           == 'true' ) { $classes[] = 'karma-menu-no-dropdown'; }
if( $disable_post_date          == 'true' ) { $classes[] = 'karma-no-post-date'; }
if( $body_footer_bottom         == 'bottom' ) { $classes[] = 'karma-footer-bottom'; }
if( $disable_gradient           == 'true' ) { $classes[] = 'karma-no-content-gradient'; }
if( $disable_mobile_subs        == 'true' ) { $classes[] = 'karma-no-mobile-submenu'; }
if( $karma_global_flat          == 'true' ) { $classes[] = 'karma-flat-cs'; }
if( $karma_header_style         == 'default' ) { $classes[] = 'karma-header-gradient'; }
if( $karma_header_style         == 'light' ) { $classes[] = 'karma-header-light'; }
if( $karma_header_style         == 'dark' ) { $classes[] = 'karma-header-dark'; }
if( ($karma_header_style        == 'dark') || ($karma_header_style == 'light') ) { $classes[] = 'karma-header-custom'; }
if( $karma_foot_center          == 'true' ) { $classes[] = 'karma-foot-center'; }
if( $karma_disable_horz_menu    == 'true' ) { $classes[] = 'karma-disable-horz-menu'; }
if( $karma_mobile_horz_dropdown == 'true' ) { $classes[] = 'karma-mobile-horz-dropdown'; }
if( $karma_post_date_year       == 'true' ) { $classes[] = 'karma-post-year'; }
if( $sticky_menu_thin_style     == 'true' ) { $classes[] = 'karma-short-sticky'; }
    
    $classes = apply_filters('__karma_body_class',$classes);//added by denzel for use in function karma_filter_global_flat_design
	return $classes;
}
add_filter( 'body_class', 'tt_add_body_classes' );
//This function lets user select gradient or flat design from post meta.
function karma_filter_global_flat_design($classes){
	if(is_page()){
	
		global $post;
		$truethemes_gradient_style = get_post_meta($post->ID,'truethemes_gradient_style',true);
		$truethemes_flat_style     = get_post_meta($post->ID,'truethemes_flat_style',true);
		
		if($truethemes_gradient_style == 'on'){
			if(($key = array_search('karma-flat-cs', $classes)) !== false) {
				unset($classes[$key]);
			}
		}	
		if($truethemes_flat_style == 'on'){
			$classes[] = 'karma-flat-cs';
		}
		
	}
return $classes;
}
add_filter('__karma_body_class','karma_filter_global_flat_design');
/**
 * Set the size attribute to 'large' in the gallery shortcode.
 * This function all only be run in add_filter in gallery.php and default-gallery.php
 * original codes from http://wordpress.stackexchange.com/questions/141896/define-size-for-get-post-gallery-images-they-seem-to-have-been-resized-to-150#answer-141907
 */
function tt_shortcode_atts_gallery( $out ){
    remove_filter( current_filter(), __FUNCTION__ );
    $out['size'] = 'large';
    return $out;
}
/*
* Remove first gallery shortcode found in content. For use in gallery.php and default-gallery.php
* original codes from http://wordpress.stackexchange.com/questions/121489/split-content-and-gallery#answer-121508
*/
function tt_strip_shortcode_gallery( $content ) {
    preg_match_all( '/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER );
    if ( ! empty( $matches ) ) {
        foreach ( $matches as $shortcode ) {
            if ( 'gallery' === $shortcode[2] ) {
                $pos = strpos( $content, $shortcode[0] );
                if ($pos !== false)
                    return substr_replace( $content, '', $pos, strlen($shortcode[0]) );
            }
        }
    }
    return $content;
}
/*-------------------------------------------------------------- 
Hide Visual Composer & Rev Slider
--------------------------------------------------------------*/
// ----- revslider
if( function_exists( 'set_revslider_as_theme' ) ){
	set_revslider_as_theme();
}
// ----- visual composer
if ( ! function_exists( 'karma_vcSetAsTheme' ) ) :
	function karma_vcSetAsTheme() {
	    vc_set_as_theme();
	}
endif;
add_action( 'vc_before_init', 'karma_vcSetAsTheme' );
remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
remove_action( 'init', 'vc_page_welcome_redirect' );
remove_action( 'admin_init', 'vc_page_welcome_redirect' );
/*-------------------------------------------------------------- 
Add Karma Welcome Page (Appearance > Karma Welcome)
--------------------------------------------------------------*/
add_action('admin_menu', 'add_karma_welcome_page');
function add_karma_welcome_page() {
	add_theme_page('Welcome', 'Karma Welcome', 'edit_theme_options', 'karma-welcome', 'karma_render_about_page');
}
function karma_render_about_page(){
	require_once(TRUETHEMES_GLOBAL   . '/karma-welcome.php');
}
//add redirection
function karma_page_welcome_redirect() {
$theme_name = get_current_theme();
if($theme_name == 'Karma'){
	wp_redirect( admin_url( 'admin.php?page=karma-welcome' ) );
	}
}
//enable redirect on activation.
add_action( 'after_switch_theme', 'karma_page_welcome_redirect' );
/*-------------------------------------------------------------- 
Automatic Updates (https://kernl.us/documentation)
--------------------------------------------------------------*/
if( is_admin() ){ //do this only in admin
	require 'theme_update_check.php';
	$licence_key = get_option('ka_item_purchase_code');
	//uncomment to see license key
	//print_r($licence_key);
	if(!empty($licence_key)){
		$MyUpdateChecker = new ThemeUpdateChecker(
		    'karma',
		    'https://kernl.us/api/v1/theme-updates/56c48fee84ae93972f38adcb/'
		);
		$MyUpdateChecker->purchaseCode = $licence_key;
	}
}
/*-------------------------------------------------------------- 
Karma Mega Menu
--------------------------------------------------------------*/
//@since 4.8
if(!function_exists('karma_ajax_switch_menu_walker')) {
	function karma_ajax_switch_menu_walker() {	
		if ( ! current_user_can( 'edit_theme_options' ) )
		die('-1');
		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
	
		require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
	
		$item_ids = wp_save_nav_menu_items( 0, $_POST['menu-item'] );
		if ( is_wp_error( $item_ids ) )
			die('-1');
	
		foreach ( (array) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj        = wp_setup_nav_menu_item( $menu_obj );
				$menu_obj->label = $menu_obj->title;
				$menu_items[]    = $menu_obj;
			}
		}
	
		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after'       => '',
				'before'      => '',
				'link_after'  => '',
				'link_before' => '',
				'walker'      => new karma_admin_mega_walker,
			);
			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}
			
		die('end');
	}
	add_action('wp_ajax_karma_ajax_switch_menu_walker', 'karma_ajax_switch_menu_walker');
}
/*-------------------------------------------------------------- 
Functions for generating Facebook og tags
--------------------------------------------------------------*/
//if ( !function_exists( 'aioseop_ajax_scan_header' ) ) { // All-in-One SEO
if ( !defined('WPSEO_VERSION') && !defined( 'AIOSEOP_VERSION' ) ) { //Yoast SEO or ALL in One SEO
	
	function tt_facebook_get_gallery_image() {
	 	global $post;
		$content = "";
	 	// Only do this on singular items
	 	if( ! is_singular() )
	 		return $content;
	 	// Make sure the post has a gallery in it
	 	if( ! has_shortcode( $post->post_content, 'gallery' ) )
	 		return $content;
	 	// Retrieve the first gallery in the post
	 	$gallery = get_post_gallery_images( $post );
	 	return $gallery[0];
	}
	function tt_add_karma_og_tags(){
	global $post;
	if( is_singular() ):
		$thumb_url = $post_content = '';
		//use featured image src
		$thumb_id = get_post_thumbnail_id();
		$thumb_url = wp_get_attachment_image_src($thumb_id,'large', false);
		$thumb_url = $thumb_url[0];
		//if no featured image, we use featured image external src
		if(empty($thumb_url)){
			global $post;
			$thumb_url = get_post_meta($post->ID,'truethemes_external_image_url',true);
		}
		//if still no image, we try to get gallery image.
			if(empty($thumb_url)){
			$thumb_url = tt_facebook_get_gallery_image();
		}
	endif;
	if( !is_404() ):
			$post_content =  explode('<!--nextpage-->',$post->post_content);
			$post_content =  (string)$post_content[0];
			$post_content =  strip_shortcodes( $post_content );
			$post_content =  apply_filters( 'the_content', $post_content );
			$post_content =  str_replace(']]>', ']]&gt;', $post_content);
			$post_content =  wp_strip_all_tags( $post_content );		
			$post_content =  substr(strip_tags($post_content),0,150);
			$post_content =  rtrim($post_content); //remove space from end of string
			$post_content =  str_replace("<br>","",$post_content);
	endif;		
	?>
	<meta property="og:title" content="<?php the_title(); ?>"/>
	<meta property="og:image" content="<?php echo $thumb_url; ?>"/>
	<meta property="og:url" content="<?php the_permalink(); ?>"/>
	<meta property="og:description" content="<?php echo $post_content; ?>"/>
	<meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>
	<?php
	}
	add_action('wp_head','tt_add_karma_og_tags');
}
/*-----------------------------------------------------------------------------------*/
/* WP_DEBUG + PHP Error Reporting
/*-----------------------------------------------------------------------------------*/
//Some plugins such as wpcu3er will disable PHP error reporting, 
//therefore we must make sure it is turn on if WP_DEBUG is set to true.
if(defined('WP_DEBUG') == 1 || WP_DEBUG == true){
$error_setting = ini_get("display_errors");
	if($error_setting == '0'){
		ini_set('display_errors', '1');
	}
}
//if PHP error reporting is enabled we will only ALLOW PHP fatal error, syntax error, parse errors etc to show only.
$php_error_setting = ini_get("display_errors");
	if($php_error_setting == '1'){
	    //reference to http://www.php.net/manual/en/errorfunc.constants.php
		error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~ E_DEPRECATED & ~ E_USER_NOTICE);
}
?>