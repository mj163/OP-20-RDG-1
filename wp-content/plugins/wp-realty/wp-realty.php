<?php 
/*
Plugin Name: WP Realty - Real Estate Plugin for Wordpress
Plugin URI : http://www.mobilewebs.net/mojoomla/extend/wordpress/realestate/
Description: WP Realty - Real Estate Plugin for Wordpress is ideal way to manage your real estate agency and manage property agents. The plugin has front end for property search, listing and backend for complete Real estate operation management.
Version: 3.0
Author: Mojoomla
Author URI: http://codecanyon.net/user/dasinfomedia
Text Domain: estate-emgt
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Copyright 2016 Mojoomla  (email : sales@mojoomla.com)
*/

define('_PLGEXEC',1);
define('REMS_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('REMS_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
define('REMS_PLUGIN_URL',untrailingslashit(plugins_url('',__FILE__)));
define('REMS_CONTENT_URL',content_url());
define('REMS_THEME_URL',get_template_directory_uri());
define('REMS_INCLUDE_DIRECTORY',includes_url());
define('WP_DATEFORMAT',get_option('date_format'));
define('WP_TIMEFORMAT',get_option('time_format'));
global $post_type;
if( 'emgt_add_listing' == $post_type ) 
{ 
	define('EMPTY_TRASH_DAYS', 0); //It will remove trash link from posts and it will remove permanently. second parameter is days.
}
if(!defined("ABSPATH"))
{
	define("ABSPATH",dirname(__FILE__) . '/');
}

function emgt_add_current_role()
{ 
	if(is_user_logged_in())
	{
		$user=get_userdata(get_current_user_id());
		$current_role = $user->roles[0];		
		define('REMS_CURRENT_ROLE',$current_role);		
	}
}
add_action("init","emgt_add_current_role",0);

function emgt_plugin()
{
	require_once REMS_PLUGIN_DIR.'/add_listing_post.php';
	require_once REMS_PLUGIN_DIR.'/settings.php';
}
add_action( 'init', 'emgt_plugin',1);

function emgt_get_custom_post_template($single_template)
{	
	global $post;
	if($post->post_type == "emgt_add_listing")
	{
		if(@file_exists(get_stylesheet_directory().'/wprealty_template/single-property.php')) $single_template = get_stylesheet_directory().'/wprealty_template/single-property.php';
		else
			$single_template = REMS_PLUGIN_DIR.'/template/single-property.php';			
	}
	return $single_template;
}

add_filter("single_template","emgt_get_custom_post_template");
add_filter( 'page_template', 'emgt_page_template' );

function emgt_page_template( $page_template )
{
    $pl_page = get_option("emgt_property_list_page");	
	if (is_page($pl_page)) { /* 'property-list-page' */
		if(@file_exists(get_stylesheet_directory().'/wprealty_template/emgt_property_list.php')) require get_stylesheet_directory().'/wprealty_template/emgt_property_list.php';
			else
        $page_template = REMS_PLUGIN_DIR.'/template/emgt_property_list.php';
    }	
	
	if (is_page("register")) { /* 'property-list-page' */
		if(@file_exists(get_stylesheet_directory().'/wprealty_template/registration_page.php'))
		{ require get_stylesheet_directory().'/wprealty_template/registration_page.php'; exit;}
		// else {$page_template = REMS_PLUGIN_DIR.'/template/registration_page.php';}
    }
		
    return $page_template;
}

add_action("emgt_before_single_property","emgt_before_single_property");

function emgt_before_single_property(){
	if(@file_exists(get_stylesheet_directory().'/emgt_template/includes/before_single_property.php'))
		{			
			require get_stylesheet_directory().'/emgt_template/includes/before_single_property.php';	
		}
	else
	{ require(REMS_PLUGIN_DIR."/template/includes/before_single_property.php");	 }
}

if(is_admin())
{
	require_once REMS_PLUGIN_DIR.'/admin/active_plugin.php';
	register_activation_hook( REMS_PLUGIN_BASENAME, 'wp_realty_activate' );
}

function emgt_domain_load(){
	load_plugin_textdomain( 'estate-emgt', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );	
}
add_action( 'plugins_loaded', 'emgt_domain_load' );

/* add_action('activated_plugin','my_save_error_emgt');
function my_save_error_emgt()
{
    file_put_contents(dirname(__file__).'/error_activation.txt', ob_get_contents());
}
 */
