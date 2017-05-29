<?php
/*
Plugin Name: Karma Builder
Plugin URI: http://www.truethemes.net
Description: A Visual Composer Add-on for Karma Theme.
Version: 2.2.6
Author: TrueThemes
Author URI: http://www.truethemes.net
License: GPLv2 or later
*/

// Don't load directly
if (!defined('ABSPATH')){die('-1');}


// Automatic Updates (https://kernl.us)
if(is_admin()):
	require( dirname( __FILE__ ) . '/plugin_update_check.php');
	$MyUpdateChecker = new PluginUpdateChecker_2_0 (
	    'https://kernl.us/api/v1/updates/56c6656b129e3975061d40a9/',
	    __FILE__,
	    'karma-builder',
	    1
	);
endif;

// Make plugin available for translation
function karma_builder_load_textdomain() {
  load_plugin_textdomain( 'tt_karma_builder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
add_action( 'plugins_loaded', 'karma_builder_load_textdomain' );


// Add parameter karma_builder_note
if(function_exists('vc_add_shortcode_param')): //added by denzel to prevent fatal error if jscomposer is not activated!.
	function karma_builder_add_param_settings_field($settings, $value) {
	   $dependency = vc_generate_dependencies_attributes($settings);
	   return '<div class="true-notification  tip"><p style="font-size:12px;"><strong>Tip!</strong> '.$value.'</p></div>';
	}
	vc_add_shortcode_param('karma_builder_note', 'karma_builder_add_param_settings_field');
endif;


// Create Thumbnails for Testimonial Sliders
add_theme_support('post-thumbnails' );
add_image_size( 'testimonial-user', 71, 71, true );
add_image_size( 'testimonial-user-2', 36, 36, true );


// Function to generate random ID for usage in tabs shortcode
function karma_builder_truethemes_random() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = mt_rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}


// Load CSS into backend for "live preview" in WP Editor
function karma_builder_wp_admin_style() {
	//wp_enqueue_style( 'font-awesome',  plugins_url('css/font-awesome.min.css', __FILE__) );
	wp_enqueue_style( 'karma-builder-backend', plugins_url('css/karma-builder-backend-editor.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'karma_builder_wp_admin_style' );

/*
* function to exclude shortcode from karma theme formatter
* @since 2.2.1
* Do this only when necessary
*/
/* function karma_builder_exclude_shortcode_from_karma_formatter(){
global $post;
if(stripos($post->post_content,"tt_vector_box") !== false){
    //this will fix the icon box breaking into two parts issue.
    remove_filter('the_content', 'truethemes_formatter', 99);
}
}
add_action('wp_head','karma_builder_exclude_shortcode_from_karma_formatter'); */

/*
* function to be use in frame shortcode
* from shortcode.php in karma theme
*/
if(!function_exists('truethemes_image_frame_constructor')):
	function truethemes_image_frame_constructor($style,$frame_class,$image_path,$width,$height,$framesize,$lightbox,$link_to_page,$image_zoom_number,$target,$description,$lightbox_group,$float){
	
	//Allow plugins/themes to override this layout.
	//refer to http://codex.wordpress.org/Function_Reference/add_filter for usage
	$output = apply_filters('truethemes_image_frame_filter','',$style,$frame_class,$image_path,$width,$height,$framesize,$lightbox,$link_to_page,$image_zoom_number,$target,$description,$lightbox_group,$float);
	if ( $output != '' ){
			return $output;
	}
	$image_src = truethemes_crop_image($thumb=null,$image_path,$width,$height); //see above
	
	$output .= '<div class="'.$style.'_img_frame '.$framesize.''; //@since 4.0 - check for $float
	if ('' == $float) {
		$output .= '">';
	} else {
		$output .= ' true-img-'.$float.'">';
	}
	
	//if $lightbox or $link_to_page we add .lightbox-img class for hover-effect
	if(('' != $lightbox) || ('' != $link_to_page)){
		$output .='<div class="img-preload lightbox-img">';
	} else {
		$output .= '<div class="img-preload">'; //all "preload classes" replaced by .img-preload
	}
	
	//if $link_to_page we format for link
	if(!empty($link_to_page)){ 
		$output.='<a href="'.$link_to_page.'" class="attachment-fadeIn" title="'.$description.'" target="'.$target.'">';
		$output.='<span class="lightbox-zoom zoom-'.$image_zoom_number.' zoom-link" style="position:absolute; display: none;">&nbsp;</span>';
	}
	
	//if $lightbox we format for lightbox
	if(!empty($lightbox)){
		$output.='<a href="'.$lightbox.'" class="attachment-fadeIn" data-gal="prettyPhoto['.$lightbox_group.']" title="'.$description.'">';
		$output.='<span class="lightbox-zoom zoom-'.$image_zoom_number.'" style="position:absolute; display: none;">&nbsp;</span>';
	}
	
	//if $lightbox and $link_to_page are empty we format normal image
	if(('' == $lightbox) || ('' == $link_to_page)){
	$output .= "<img src='".$image_src."' alt='".$description."' class=\"attachment-fadeIn\" />";
	}
	
	
	//if $lightbox or $link_to_page we close anchor tag </a>
	if(('' != $lightbox) || ('' != $link_to_page)){
		$output.='</a></div></div>';
	} else {
		$output .='</div></div>'; //close normal image
	}
	
	return $output;
	
	}
endif;


/* ----- RELATED POSTS ----- 
* Do not move this function into VC -- class KarmaBuilderVCExtendAddonClass
* This function is used by Karma Theme to generate related posts in single.php
* This function is also used by related_posts shortcode
*/
if(!function_exists('related_posts_shortcode')):
function related_posts_shortcode( $atts ) {
	extract(shortcode_atts(array(
		'title' 	=> '',
		'limit' 	=> '5',
		'post_id' 	=> '',
		'style' 	=> 'one', //default style one, style two is same as that found in single.php
		'icon' 		=> '<i style="font-size:13px" class="fa fa-file-text-o"></i>', //for style two
		'target'	=> '_blank',
	), $atts));
	
	//prepare html codes that wrap list items output
	$style_one_html_before = "<div class='related_posts'><h4>{$title}</h4><ul class='list list1'>";
	$style_one_html_after = "</ul></div>";
	$style_two_html_before = "<h6 class='heading-horizontal true-blog-related-post'><span>$icon&nbsp; {$title}</span></h6><ul class='list list1 true-blog-related-post-list'>";
	$style_two_html_after = "</ul>"; 

	/*
	* if user did not enter post_id in shortcode, 
	* we assign current $post->ID to post_id
	*/
	if(empty($post_id)){
	global $post;
	$post_id = $post->ID;
	}
	
	/*
	* Start grabbing lists of post tag term_id from this post 
	*/
	
	//declare container for tag ids
	$tag_ids = array();
	//grab the tags
	$tags = wp_get_post_tags($post_id);
	//if there are tags found, we push their id into tag_ids container 
	if ($tags) { 
			foreach($tags as $individual_tag) {
			$tag_ids[] = $individual_tag->term_id;
			}
	   }
	   
	   
	//declare output container
	$output = '';
	
	
    /*
    * Start doing database query only if there is tags found.
    */
    
    
    if($tags){
    /*
    * if there are tags found, we grab all posts that has the same tags and print as lists items
    * if not we skip the block of codes enclosed and straight to ## end of function
    */
    
    		//print out html before list item according to style
			if($style=='one'){
			    	$output.= $style_one_html_before;
			    }else{
			    	$output.= $style_two_html_before;
			}
		    
		    //prepare query arguments
		    $args = array(
						'tag__in' => $tag_ids,
						'post__not_in' => array($post_id), //don't include current post.
						'showposts' => $limit,  // number of related posts that will be shown.
						'ignore_sticky_posts' => 1 //do not show sticky posts at the top.
					);
					
			//run the query		
			$related_query = new WP_Query($args);
			
			//the loop 
			if ( $related_query->have_posts() ) {

			    //print out list items
			    while ( $related_query->have_posts() ) {
			    	$related_query->the_post();
			    	$output.= "<li><a href='".get_permalink()."' rel='bookmark' title='".get_the_title()."' target='{$target}'>".get_the_title()."</a></li>";
			    }

			} else {
			    
			    //there are tags in current post but no related posts found
				$output.= "<li>No related posts found.</li>";

			}
			
			
			//print out html after list items according to style
			if($style=='one'){
			    	$output.= $style_one_html_after;
			    }else{
			    	$output.= $style_two_html_after;
			}
			
			//restore original post data
			wp_reset_postdata();

			//everything done, we display on screen.
    		return $output;

    }else{
    
        //## end of function	
     
        //There are no tags found in current posts, so we skip WP_Query and display no related post found.
    	if($style=='one'):
   	    		$output.= $style_one_html_before."<li>No related posts found.</li>".$style_one_html_after;
   	    	else:
	   	    	$output.= $style_two_html_before."<li>No related posts found.</li>".$style_two_html_after;
   	    endif;
   	    
   	    return $output;
    
    }
  
}
endif;



/**
 *
 * Do not move or remove this variable,
 * it is a global variable and needs to be outside any function or class
 * for use with Dynamic CSS Function.
 *
 * @since Karma Builder 1.0
 */
if( !isset( $karma_builder_css_array ) ) {
$karma_builder_css_array = array();
}


// Extend Visual Composer
class KarmaBuilderVCExtendAddonClass {
    function __construct() {
        // Safely integrate with VC using this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );

        // Add the new shortcodes
 		add_shortcode( 'karma_builder_accordion'             , array( $this, 'render_karma_builder_accordion' ) );
 		add_shortcode( 'karma_builder_accordion_panel'       , array( $this, 'render_karma_builder_accordion_panel' ) );
		// alert box is using notify_box
 		add_shortcode( 'notify_box'                 		 , array( $this, 'render_karma_builder_alert' ) );
 		add_shortcode( 'karma_builder_button'				 , array( $this, 'render_karma_builder_button' ) );
 		add_shortcode( 'karma_builder_content_box'           , array( $this, 'render_karma_builder_content_box' ) );
 		add_shortcode( 'karma_builder_circle_loader'         , array( $this, 'render_karma_builder_circle_loader' ) );
 		add_shortcode( 'karma_builder_circle_loader_icon'    , array( $this, 'render_karma_builder_circle_loader_icon' ) );
 		add_shortcode( 'karma_builder_dropcap'               , array( $this, 'render_karma_builder_dropcap' ) );
 		add_shortcode( 'karma_builder_features'              , array( $this, 'render_karma_builder_features' ) );
 		add_shortcode( 'karma_builder_heading'               , array( $this, 'render_karma_builder_heading' ) );
		// iconbox is using tt_vector_box shortcode
 		add_shortcode( 'tt_vector_box'              		 , array( $this, 'render_karma_builder_icon_box' ) ); 		
 		add_shortcode( 'karma_builder_icon_content'          , array( $this, 'render_karma_builder_icon_content' ) );
 		add_shortcode( 'karma_builder_icon_png'              , array( $this, 'render_karma_builder_icon_png' ) );
 		add_shortcode( 'karma_builder_imagebox_1'            , array( $this, 'render_karma_builder_imagebox_1' ) );
 		add_shortcode( 'karma_builder_imagebox_2'            , array( $this, 'render_karma_builder_imagebox_2' ) );
 		add_shortcode( 'karma_builder_number_counter'        , array( $this, 'render_karma_builder_number_counter' ) );
 		add_shortcode( 'karma_builder_pricing_box'           , array( $this, 'render_karma_builder_pricing_box_1' ) );
 		add_shortcode( 'karma_builder_progress_bar'          , array( $this, 'render_karma_builder_progress_bar' ) );
 		add_shortcode( 'karma_builder_progress_bar_vertical' , array( $this, 'render_karma_builder_progress_bar_vertical' ) );
 		add_shortcode( 'karma_builder_services'              , array( $this, 'render_karma_builder_services' ) );
 		add_shortcode( 'karma_builder_tab_1'                 , array( $this, 'render_karma_builder_tab_1' ) );
 		add_shortcode( 'karma_builder_tab_1_content'         , array( $this, 'render_karma_builder_tab_1_content' ) );
 		add_shortcode( 'karma_builder_tab_2'                 , array( $this, 'render_karma_builder_tab_2' ) );
 		add_shortcode( 'karma_builder_tab_2_content'         , array( $this, 'render_karma_builder_tab_2_content' ) );
 		add_shortcode( 'karma_builder_tab_3'                 , array( $this, 'render_karma_builder_tab_3' ) );
 		add_shortcode( 'karma_builder_tab_3_content'         , array( $this, 'render_karma_builder_tab_3_content' ) );
 		add_shortcode( 'karma_builder_testimonial_1'         , array( $this, 'render_karma_builder_testimonial_1' ) );
 		add_shortcode( 'karma_builder_testimonial_1_slide'   , array( $this, 'render_karma_builder_testimonial_1_slide' ) );
 		add_shortcode( 'karma_builder_testimonial_2'         , array( $this, 'render_karma_builder_testimonial_2' ) );
 		add_shortcode( 'karma_builder_testimonial_2_slide'   , array( $this, 'render_karma_builder_testimonial_2_slide' ) );
 		//Original Karma theme Shortcodes
 		add_shortcode( 'button'	, array( $this, 'render_karma_builder_button' ) );// do not remove.
 		add_shortcode( 'business_contact', array( $this, 'render_karma_builder_business_contact' ) );	
 		add_shortcode( 'callout', array( $this, 'render_karma_builder_callout' ) );
 		add_shortcode( 'one_sixth', array( $this, 'render_karma_builder_one_sixth' ) );
 		add_shortcode( 'one_sixth_last', array( $this, 'render_karma_builder_one_sixth_last' ) );
 		add_shortcode( 'one_fifth', array( $this, 'render_karma_builder_one_fifth' ) );
		add_shortcode( 'one_fifth_last', array( $this, 'render_karma_builder_one_fifth_last' ) );
		 add_shortcode( 'one_fourth', array( $this, 'render_karma_builder_one_fourth' ) );
		 add_shortcode( 'one_fourth_last', array( $this, 'render_karma_builder_one_fourth_last' ) );
		add_shortcode( 'one_third', array( $this, 'render_karma_builder_one_third' ) );
		add_shortcode( 'one_third_last', array( $this, 'render_karma_builder_one_third_last' ) );
		add_shortcode( 'one_half', array( $this, 'render_karma_builder_one_half' ) );
		add_shortcode( 'one_half_last', array( $this, 'render_karma_builder_one_half_last' ) );
		add_shortcode( 'two_thirds', array( $this, 'render_karma_builder_two_thirds' ) );
		add_shortcode( 'two_thirds_last', array( $this, 'render_karma_builder_two_thirds_last' ) );
		add_shortcode( 'three_fourth', array( $this, 'render_karma_builder_three_fourth' ) );
		add_shortcode( 'three_fourth_last', array( $this, 'render_karma_builder_three_fourth_last' ) );
		add_shortcode( 'flash_wrap', array( $this, 'render_karma_builder_flash_wrap' ) );
		add_shortcode( 'frame', array( $this, 'render_karma_builder_image_frame' ) );		
		add_shortcode( 'hr_shadow', array( $this, 'render_karma_builder_hr_shadow' ) );//do not remove or comment out, leave for backward compatibility
		add_shortcode( 'hr', array( $this, 'render_karma_builder_hr' ) );//do not remove or comment out, leave for backward compatibility
		add_shortcode( 'top_link',  array( $this, 'render_karma_builder_top_link' ) );
		add_shortcode( 'tt_vector',  array( $this, 'render_karma_builder_font_awesome' ));
		add_shortcode( 'gap',  array( $this, 'render_karma_builder_sc_gap' ));
		add_shortcode( 'arrow_list', array( $this, 'render_arrow_list' ));			
		add_shortcode( 'list_item', array( $this, 'render_list_item' ));	
		add_shortcode( 'star_list', array( $this, 'render_star_list' ));
		add_shortcode( 'circle_list', array( $this, 'render_circle_list' ));	
		add_shortcode( 'check_list', array( $this, 'render_check_list' ));	
		add_shortcode( 'caret_list', array( $this, 'render_caret_list' ));	
		add_shortcode( 'plus_list', array( $this, 'render_plus_list' ));	;
		add_shortcode( 'double_angle_list', array( $this, 'render_double_angle_list' ));	
		add_shortcode( 'full_arrow_list', array( $this, 'render_full_arrow_list' ));
		add_shortcode( 'social',  array( $this, 'render_karma_social_shortcode' ));
		add_shortcode( 'accordion', array( $this, 'render_accordion' ));
		add_shortcode( 'slide', array( $this, 'render_slide' ));
		add_shortcode('tabset',array( $this, 'render_tabset' ));	
		add_shortcode('tab',array( $this, 'render_tab' ));
		add_shortcode('testimonial_wrap', array( $this, 'render_testimonial_wrap' ));
		add_shortcode('testimonial', array( $this, 'render_testimonial' ));
		add_shortcode('client_name', array( $this, 'render_client_name' ));
		add_shortcode('team_member', array( $this, 'render_team_member' ));				
		add_shortcode('h1', array( $this, 'render_h1' ));
		add_shortcode('h2', array( $this, 'render_h2' ));
		add_shortcode('h3', array( $this, 'render_h3' ));
		add_shortcode('h4', array( $this, 'render_h4' ));
		add_shortcode('h5', array( $this, 'render_h5' ));;
		add_shortcode('h6', array( $this, 'render_h6' ));
		add_shortcode('callout1', array( $this, 'render_callout1' ));//do not remove or comment out, leave for backward compatibility
		add_shortcode('callout2', array( $this, 'render_callout2' ));//do not remove or comment out, leave for backward compatibility
		add_shortcode('heading_horizontal', array( $this, 'render_heading_horizontal' ));		 		
		add_shortcode('video_left', array( $this, 'render_video_left' ));
		add_shortcode('video_right', array( $this, 'render_video_right' ));
		add_shortcode('video_frame', array( $this, 'render_video_frame' ));
		add_shortcode('video_text', array( $this, 'render_video_text' ));
		add_shortcode('iframe', array( $this, 'render_iframe' ));
		add_shortcode('blog_posts',array( $this, 'render_blog_posts' ));
		add_shortcode('related_posts',array( $this, 'render_related_posts' ));
		/*
		* This shortcode has only a title attribute.
		* I don't think this is in use, so I did not add user interface. 
		* Keep in case for backward compatibility
		*/
		add_shortcode('post_categories',array( $this, 'render_categorie_display' ));
		
		
		//new shortcode
		add_shortcode('karma_list',array( $this, 'render_karma_list' ));
		add_shortcode('karma_list_item',array( $this, 'render_karma_list_item' ));
		add_shortcode('karma_separator_line',array( $this,'render_karma_separator_line'));
		add_shortcode('karma_callout_text',array( $this,'render_karma_callout_text'));		
		
            
        // Register CSS and JS (these are Enqueued near bottom of this file)
        add_action( 'wp_enqueue_scripts', array( $this, 'karma_builder_enqueue_script' ) );

        // Print dynamic CSS code in footer
        add_action( 'wp_footer', array( $this, 'karma_builder_dynamic_hook_embed_css' ) );
    }

/**
 * Dynamic CSS Function
 *
 * Prints dynamic css so that styles are not nested inline
 * and site remains HTML 5 compatible.
 *
 * @since Karma Builder 1.0
 */

// Prepares CSS into global array for printing in footer
// Removes duplicated set of style codes
public function karma_builder_dynamic_embed_css($style_code){
global $karma_builder_css_array;
    if(!in_array($style_code,$karma_builder_css_array)){
		array_push($karma_builder_css_array,$style_code);
    }
}
// Generate the CSS
public function karma_builder_dynamic_hook_embed_css(){
global $karma_builder_css_array;
    if(!empty($karma_builder_css_array)){
	    $code ="\n<!--dynamic styles generated by orbit plugin-->";
	    ///scoped attribute is needed to be html 5 valid, I do not know what it means..
	    $code .= "<style type='text/css' scoped>";
       foreach($karma_builder_css_array as $style_code){
        $code .= $style_code."\n";
       }
	    $code .="</style>\n";
	    echo $code;
    }
} // END Dynamic CSS Function


public function integrateWithVC() {
	// Check for VC
	if ( ! defined( 'WPB_VC_VERSION' ) ) {
	    // Alert VC is required
	    add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
	    return;
	}


/*
* The vc_icon.php and vc_empty_space.php in existing js_composer has unnecessary carriage return, causing truethemes_formatter's wpautop of karma theme to break it!
* so we had to copy the original and place it into karma_builder plugin for overwriting, removing the unnecessary empty spacing! 
* we fix only the shortcode output template and nothing else...
*/
$karma_vc_icon_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_icon.php'
);
vc_map_update('vc_icon', $karma_vc_icon_map);

$karma_vc_empty_space_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_empty_space.php'
);
vc_map_update('vc_empty_space', $karma_vc_empty_space_map);

$karma_vc_text_separator_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_text_separator.php'
);
vc_map_update('vc_text_separator', $karma_vc_text_separator_map);

$karma_vc_toggle_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_toggle.php'
);
vc_map_update('vc_toggle', $karma_vc_toggle_map);

$karma_vc_image_carousel_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_image_carousel.php'
);
vc_map_update('vc_images_carousel', $karma_vc_image_carousel_map);

$karma_vc_pie_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_pie.php'
);
vc_map_update('vc_pie',$karma_vc_pie_map);

$karma_vc_cta_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_cta.php'
);
vc_map_update('vc_cta',$karma_vc_cta_map);

$karma_vc_basic_grid_map = array (
	'html_template' => dirname(__FILE__) . '/vc_templates/vc_basic_grid.php'
);
vc_map_update('vc_basic_grid',$karma_vc_basic_grid_map);//uses the same template
vc_map_update('vc_media_grid',$karma_vc_basic_grid_map);//uses the same template
vc_map_update('vc_masonry_grid',$karma_vc_basic_grid_map);//uses the same template
vc_map_update('vc_masonry_media_grid',$karma_vc_basic_grid_map);//uses the same template

/**
 * Map the Shortcodes
 *
 * Lets call vc_map() to register the custom
 * shortcodes into Visual Composer interface.
 *
 * Note: some of the shortcodes are pulled from our Orbit Plugin
 * which you'll find reflected in the names
 *
 * @since Karma Builder 1.0
 */

/*--------------------------------------------------------------
Orbit - Accordion
--------------------------------------------------------------*/
/* * For working with animations
 * array(
 *        'type' => 'animation_style',
 *        'heading' => __( 'Animation', 'js_composer' ),
 *        'param_name' => 'animation',
 * ),
CSS Class: animated fadeInLeft
 */
vc_map( array(
	'category'                => __('Karma Builder', 'tt_karma_builder'),
	'name'                    => __("Accordion", 'tt_karma_builder'),
	'description'             => __("Collapsible content panels with color customization", 'tt_karma_builder'),
	'base'                    => "karma_builder_accordion",
	'controls'                => 'full',
	'class'                   => 'true-accordion',
	'show_settings_on_create' => true,
	'content_element'         => true,
	'js_view'                 => 'VcColumnView',
    'icon'        => plugins_url('images/backend-editor/truethemes-menu-accordion.png', __FILE__),
    "as_parent"   => array('only' => 'karma_builder_accordion_panel'),         
    "params"      => array(
    					array(
					      		'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),
				        array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Padding", 'tt_karma_builder'),
			                  'type'          => 'textfield',
			                  'holder'        => 'div',
			                  'param_name'    => "panel_padding",
			                  'value'         => "20px",
			                  'description'   => __('The vertical padding within each section title', 'tt_karma_builder')
			              ),			              
				        array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Gradient Color (top)", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "gradient_top",
			                  'value'         => "#fff",
			                  'description'   => __('The top gradient color of each section title', 'tt_karma_builder')
			              ),
				        array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Gradient Color (bottom)", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "gradient_bottom",
			                  'value'         => "#efefef",
			                  'description'   => __('The bottom gradient color of each section title', 'tt_karma_builder')
			              ),
				        array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Border Color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "panel_border",
			                  'value'         => "#e1e1e1",
			                  'description'   => __('The 1px border around each section title', 'tt_karma_builder')
			              ),
				        array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Section Title Color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "title_color",
			                  'value'         => "#666"
			              ),
				        array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Section Title Color (active state)", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "title_color_active",
			                  'value'         => "",
			                  'description' => __('If color is left empty it will adopt the color of the active theme color scheme.  ', 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
				    ),
		) 
);// END vc_map

// Map the accordion content
   vc_map( array(
   		'category'                => __('Karma Builder', 'tt_karma_builder'),
   		'name'                    => __("Accordion Panel", 'tt_karma_builder'),
   		'description'             => __('Add an accordion panel', 'tt_karma_builder'),
   		'base'                    => "karma_builder_accordion_panel",
   		'controls'                => 'full',
   		'content_element'         => true,
   		'show_settings_on_create' => true,
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-accordion.png', __FILE__),
        "as_child"    => array('only' => 'karma_builder_accordion'),          
        "params" => array(
			              array(
			              		'heading'       => __("Title", 'tt_karma_builder'),
					            'type'          => "textfield",
					            'holder'        => 'div',
					            'param_name'    => "title",
					            'description'   => __('This title is shown before the user clicks the sliding panel', 'tt_karma_builder')
			              ),	  
			              array(
								'heading'    => __('Content', 'tt_karma_builder'),
								'type'       => 'textarea_html',
								'param_name' => 'content',
								'value'      => __("<h3>Heading</h3><p>Lorem ipsum dolor ante venenatis dapibus posuere.</p>", 'tt_karma_builder')
							),
							array(
        				  		'heading'       => __("Open by Default?", 'tt_karma_builder'),
					            'type'          => 'dropdown',
					            'param_name'    => "panel_active",
					            'value'         => array(
											'False'  => 'false',
											'True' => 'true',
											),
					            'description' => __("If True this panel will be open by default", 'tt_karma_builder'),
					            'std' => array('False'  => 'false'),
					      ),					            				              						              						           
        		)
			) 
	);// END vc_map

/*--------------------------------------------------------------
Karma - Accordion 2
--------------------------------------------------------------*/
//accordion from karma theme
   vc_map( array(
        'name'            => __("Accordion 2", 'tt_karma_builder'),
        'description'     => __("Collapsible content panels", 'tt_karma_builder'),
        'base'            => "accordion",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-accordion.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only' => 'slide'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
            			  array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),
			               array(
							  'heading'     => __('Unique ID', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'class',
			                  'description'     => __('Give this accordion set a unique id name, no spacing between words. This option is preserved for backward compatibility.', 'tt_karma_builder'),
			                  'value'     => __('accordion1', 'tt_karma_builder'),
			              ),
			               array(
							  'heading'     => __('Active Panel', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'active',
			                  'value'     => __('1', 'tt_karma_builder'),
			                  'description'     => __('Indicate which accordion panel should be open by default, or just leave empty to close all.', 'tt_karma_builder'),
			                  'std' => 1,
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),		              
					    ),
     	   		) 
);// END vc_map

// Map the accordion content
vc_map( array(
        'name'            => __("Accordion Panel", 'tt_karma_builder'),
        'description'     => __("Add an accordion panel", 'tt_karma_builder'),
        'base'            => "slide",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-accordion.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'accordion'),	        
        "params"      => array(
 				            array(
			              		'heading'       => __("Title", 'tt_karma_builder'),
					            'type'          => "textfield",
					            'holder'        => 'div',
					            'param_name'    => "name",
					            'description'   => __('This title is shown before the user clicks the sliding panel', 'tt_karma_builder')
			              ),	  
			              array(
								'heading'    => __('Content', 'tt_karma_builder'),
								'type'       => 'textarea_html',
								'param_name' => 'content',
								'value'      => __("<h3>Heading</h3><p>Lorem ipsum dolor ante venenatis dapibus posuere.</p>", 'tt_karma_builder')
							),
					    ),
     	   		)
);// END vc_map

/*--------------------------------------------------------------
Orbit - Alert Box
--------------------------------------------------------------*/
vc_map( array(
		/**
		 * important:
		 *
		 * 'admin_enqueue_css/js' added to this vc_map to load
		 * custom CSS file for backend-editor styling.
		 * only needs to be loaded this one time.
		 *
		 * @since Karma Builder 1.0
		 */
		'admin_enqueue_css' => plugins_url('css/karma-builder.css', __FILE__), //main stylesheet
		'admin_enqueue_js'  => plugins_url('js/karma-builder-backend-editor.js', __FILE__), //custom backend JS
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __("Alert Box", 'tt_karma_builder'),
        'description' => __("Stylish notification message", 'tt_karma_builder'),
        'base'        => "notify_box",
        'controls'    => 'full',
        'class'       => 'true-alert-box',
        'js_view'     => 'OrbitAlertBox',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-alert-box.png', __FILE__),
        "params"      => array(
        				  array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			               array(
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'heading'    => __("Design style", 'tt_karma_builder'),
			                  'param_name' => "style",
			                  'value'      => array(
											'Success' => 'success',
											'Error'   => 'error',
											'Warning' => 'warning',
											'Tip'     => 'tip',
											'Neutral' => 'neutral',
											),
							  'save_always' => true,
			              ),
			              array(
			                  'type'       => "textfield",
			                  'holder'     => 'div',
			                  'heading'    => __("Font size", 'tt_karma_builder'),
			                  'param_name' => "font_size",
			                  'value'      => "12px",
			              ),
			              array(
			                  'type'          => "textarea_html",
			                  'holder'        => 'div',
			                  'heading'       => __("Alert text", 'tt_karma_builder'),
			                  'param_name'    => 'content',
			                  'value'         => "Edit this text with a custom message.",
			              ),
			               array(
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'heading'    => __("Closeable?", 'tt_karma_builder'),
			                  'param_name' => "closeable",
			                  'value'      => array(
											'True'  => 'true',
											'False' => 'false',
											),
			                  'description' => __('Select True to make this box closeable by the user.', 'tt_karma_builder'),
			                  'save_always' => true,
			              ),
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			          )
			) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Blog Posts
--------------------------------------------------------------*/
   vc_map( array(
        'name'            => __("Blog Posts", 'tt_karma_builder'),
        'description'     => __("Add blog posts", 'tt_karma_builder'),
        'base'            => "blog_posts",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-blog-posts.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
        				   array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
					           'group'       => __("General", 'tt_karma_builder'),
					           'type'          => "textfield",
					           'heading'       => __("Title (optional)", 'tt_karma_builder'),
					           'param_name'    => "title",
					           'description' => __("This title will be displayed above the list of posts", 'tt_karma_builder'),
					       ), 
        					array(
					          'group'       => __("General", 'tt_karma_builder'),			               
			               	  'heading'    => __("Post Layout", 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'param_name' => "layout",
					            'value'      => array(
					                        //Please note - you can change the label on the left, but not the value on the right..
					                        'Default Layout' => 'right_sidebar', //this was previously named "blog layout"
											'Small Thumbnails'  => 'default',
											'-----',
											'Column Layout - Full width page -  2 Columns' => 'two_col_large',	
											'Column Layout - Full width page -  3 Columns' => 'three_col_large',
											'Column Layout - Full width page -  4 Columns' => 'four_col_large',
											'-----',	
											'Column Layout - Sidebar page -  2 Columns' => 'two_col_small',
											'Column Layout - Sidebar page -  3 Columns' => 'three_col_small',
											'Column Layout - Sidebar page -  4 Columns' => 'four_col_small',
											),
											/*'description' => __('Select the shortcode layout', 'tt_karma_builder')*/
			                  'save_always' => true,
			              ),
			               array(
					           'group'       => __("General", 'tt_karma_builder'),			               
			               	  'heading'    => __("Image Frame Style", 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'param_name' => "style",
					            'value'      => array(
											'Modern'  => 'modern',
											'Shadow' => 'shadow',  																																	),
			                  'description' => __('Select the featured image frame style', 'tt_karma_builder'),
			                  'save_always' => true,
			              ),
			               array(
					           'group'       => __("General", 'tt_karma_builder'),					       
					           'type'          => "textfield",
					           'heading'       => __("Post Count", 'tt_karma_builder'),
					           'param_name'    => "count",
					           'description' => __("How many posts should be displayed?", 'tt_karma_builder'),
					           'value' => __('3'),
					           'save_always' => true,
					       ),       
					       array(
					           'group'       => __("General", 'tt_karma_builder'),
					           'type'          => "textfield",
					           'heading'       => __("Character Count", 'tt_karma_builder'),
					           'param_name'    => "character_count",
					           'description' => __("How many characters should be displayed in each post excerpt?", 'tt_karma_builder'),
					           'value' => __('115'),
					           'save_always' => true,
					       ),
					       array(
					           'group'       => __("General", 'tt_karma_builder'),					       
					           'type'          => "textfield",
					           'heading'       => __('"Read More" label', 'tt_karma_builder'),
					           'param_name'    => "link_text",
					           'description' => __('Enter a label to be used for "read more" text', 'tt_karma_builder'),
					           'value' => __('Read More', 'tt_karma_builder'),
					           'save_always' => true,
					       ),					       	
					       array(
					           'group'       => __("Category Options", 'tt_karma_builder'),							       
					           'type'          => "textfield",
					           'heading'       => __("Post Category (Optional)", 'tt_karma_builder'),
					           'param_name'    => "post_category",
					           'description' => __("Enter post category slug to show posts from a specified category. Leave this empty if showing all posts.", 'tt_karma_builder'),
					       ),
					       array(
					           'group'       => __("Category Options", 'tt_karma_builder'),							       
					           'type'          => "textfield",
					           'heading'       => __("Exclude Post Category (Optional)", 'tt_karma_builder'),
					           'param_name'    => "excluded_cat",
					           'description' => __("Exclude posts from a specified category. Enter the post category ID with a negative prefix. For example: -10. For multiple excludes, separate category ID with comma. For example: -10,-14,-16 Leave this empty if showing all posts or using the above Post Category setting.", 'tt_karma_builder'),
					       ),					       					       		              
					       						       			       				       
					    ),
     	   		)
);// END vc_map

/*--------------------------------------------------------------
Karma - Business Contact
--------------------------------------------------------------*/
//business_contact from karma theme
   vc_map( array(
        'name'            => __("Business Contact", 'tt_karma_builder'),
        'description'     => __("Phone, Fax, Skype, Email, Driving Directions", 'tt_karma_builder'),
        'base'            => "business_contact",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-business-contact.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            			  array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Phone Number", 'tt_karma_builder'),
			                  'param_name'  => "phone_number",
			                  'description' => __('Enter your phone number.', 'tt_karma_builder')
			              ),
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Fax Number", 'tt_karma_builder'),
			                  'param_name'  => "fax_number",
			                  'description' => __('Enter your fax number.', 'tt_karma_builder')
			              ),
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Skype Username", 'tt_karma_builder'),
			                  'param_name'  => "skype_username",
			                  'description' => __('Enter your Skype Username. This will be used to build the link.', 'tt_karma_builder')
			              ),	
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Skype Label", 'tt_karma_builder'),
			                  'param_name'  => "skype_label",
			                  'value' => __("Skype", 'tt_karma_builder'),
			                  'description' => __('Enter a label for your Skype Username.', 'tt_karma_builder')
			              ),
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Email Address", 'tt_karma_builder'),
			                  'param_name'  => "email_address",
			                  'description' => __('Enter an email address.', 'tt_karma_builder')
			              ),	
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Directions URL", 'tt_karma_builder'),
			                  'param_name'  => "directions_url",
			                  'description' => __('Enter the Google Maps URL here. Need help? Full details here: <a href="http://bit.ly/karma-google-map" target="_blank">http://bit.ly/karma-google-map</a>', 'tt_karma_builder')
			              ),
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Directions Label", 'tt_karma_builder'),
			                  'param_name'  => "directions_label",
			                  'value' => __("get driving directions", 'tt_karma_builder'),
			                  'description' => __('Enter the direction label', 'tt_karma_builder')
			              ),			              			              		   	              			              
     	   		)
     	 )
);// END vc_map

/*--------------------------------------------------------------
Karma - Button
--------------------------------------------------------------*/
//button shortcode from karma theme
   vc_map( array(
        'name'            => __("Button", 'tt_karma_builder'),
        'description'     => __("Stylish Button", 'tt_karma_builder'),
        'base'            => "karma_builder_button",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-button.png', __FILE__),
        'js_view'     		=> 'OrbitButtonView',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            			   array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
			                  'type'       => "textfield",
			                  'holder'     => 'a',
			                  'heading'    => __("Text on the button", 'tt_karma_builder'),
			                  'param_name' => 'content',
			                  'value'      => "Text on the button",
			                  'description' => __("Text on the button", 'tt_karma_builder')
			              ),
        					array(
					            'type'       => 'dropdown',
					            'heading'    => __("Button Size", 'tt_karma_builder'),
					            'param_name' => "size",
					            'value'      => array(
											'Small'  => 'small',
											'Medium' => 'medium',
											'Large' => 'large',
											),
					            'description' => __("Select the button size", 'tt_karma_builder'),
					            'save_always' => true,
					        ),
        					array(
					            'type'       => 'dropdown',
					            'heading'    => __("Button Color", 'tt_karma_builder'),
					            'param_name' => "style",
					            'value'      => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',																	
											),
					            'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-buttons.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder'),
					            'save_always' => true,
					        ),
						array(
					            'type'       => 'dropdown',
					            'heading'    => __("Alignment", 'tt_karma_builder'),
					            'param_name' => "alignment",
					            'value'      => array(
											'Left'  => 'left',
											'Center' => 'center',
											'Right' => 'right',
											),
					            'save_always' => true,
					        ),
					    array(
								'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'checkbox',
								'heading'     => __( 'Icon', 'tt_karma_builder' ),
								'description' => __( 'Check the box to add an icon to this Button', 'tt_karma_builder' ),
								'param_name'  => 'add_icon',
								'value'       => array( __( '', 'tt_karma_builder' ) => 'yes' )
								),			        
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons  						        
							array(
			                  'group'       => __('URL (link)', 'tt_karma_builder'),							
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'link',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),			        			              
			              array(
			                  'type'        => 'textfield',
			                  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "popup",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              array(
			                  'type'        => 'textfield',
			                  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'holder'      => 'div',
			                  'heading'     => __("Lightbox text", 'tt_karma_builder'),
			                  'param_name'  => "title",
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
				        					        					        
					        
     	   		)
     	 )
);// END vc_map

/*--------------------------------------------------------------
Karma - Callout Box
--------------------------------------------------------------*/
//callout shortcode from karma theme.
   vc_map( array(
        'name'            => __("Callout Box", 'tt_karma_builder'),
        'description'     => __("A colorful callout box", 'tt_karma_builder'),
        'base'            => "callout",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-callout-box.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"          => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
					            'type'       => 'dropdown',
					            'heading'    => __("Color scheme", 'tt_karma_builder'),
					            'param_name' => "style",
					            'value'      => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',																	
											),
					            'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-callout-box.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder'),
					            	'save_always' => true,
					        ),
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Font Size", 'tt_karma_builder'),
			                  'param_name'  => "font_size",
			                  'value'     => __("13px", 'tt_karma_builder'),
			                  'description' => __('Enter font size in px', 'tt_karma_builder')
			              ),
				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
			                 'description' => __('Enter the callout text.', 'tt_karma_builder')	,
			                 'value'     => __("<h6>H6 Heading</h6><p>Lorem ipsum dolor sit amet.</p>", 'tt_karma_builder')			    		 
			              ),						        

     	   		)
     	 )
);// END vc_map

/*--------------------------------------------------------------
Orbit - Content Box
--------------------------------------------------------------*/
vc_map( array(
        'name'        => __("Content Box", 'tt_karma_builder'),
        'description' => __("Stylish text box", 'tt_karma_builder'),
        'base'        => "karma_builder_content_box",
        'controls'    => 'full',
        'class'       => 'true-content-box',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-content-box.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitContentBox',
        "params"      => array(
            			  array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			               array(
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'heading'    => __("Color", 'tt_karma_builder'),
			                  'param_name' => "style",
			                  'value'      => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',
											),
			                  'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-content-box.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder'),
			                  'save_always' => true,
			              ),
			            array(
			            		'group'       => __('Icon', 'tt_karma_builder'),
     							'type'       => 'checkbox',
								'heading'    => __( 'Icon', 'tt_karma_builder' ),
								'description' => __( 'Check the box to add an icon to this Content Box', 'tt_karma_builder' ),
								'param_name' => 'add_icon',
								'value'      => array( __( '', 'tt_karma_builder' ) => 'yes' )
								),
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
 							array(
 								'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),
			               array(
			               	  'group'       => __('Icon', 'tt_karma_builder'),
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),													
			              array(
			                  'type'          => "textfield",
			                  'holder'        => 'div',
			                  'heading'       => __("Title", 'tt_karma_builder'),
			                  'param_name'    => "title",
			                  'value'         => "Content Box",
			                  'description' => __('This title is displayed in the top color section.', 'tt_karma_builder')
			              ),
			              array(
			                  'type'          => "textarea_html",
			                  'holder'        => 'div',
			                  'heading'       => __('Content', 'tt_karma_builder'),
			                  'param_name'    => 'content',
			                  'value'         => __("Edit this text with custom content.", 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),					              
        				)
			) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Circle Loader
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Circle Loader", 'tt_karma_builder'),
        'description' => __("Animated circle loader", 'tt_karma_builder'),
        'base'        => "karma_builder_circle_loader",
        'controls'    => 'full',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-circle-loader.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            			   array(
					      	    'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
        						'group'      => 'Content',
								'value'      => '50',
								'type'       => 'textfield',
								'heading'    => __('Number', 'tt_karma_builder'),
								'param_name' => 'number',
							),
							array(
								'group'      => 'Content',
								'value'      => '%',
								'type'       => 'textfield',
								'heading'    => __('Symbol', 'tt_karma_builder'),
								'param_name' => 'symbol',
							),
							array(
								'group'      => 'Content',
								'type'       => 'textarea_html',
								'heading'    => __('Content', 'tt_karma_builder'),
								'param_name' => 'content',
								'value'      => __("<h3>Heading</h3><p>This content is displayed below the circle loader.</p>", 'tt_karma_builder')
							),
							array(
								'group'      => __('Design', 'tt_karma_builder'),
								'value'      => '#000',
								'type'       => 'colorpicker',
								'heading'    => __('Number Color', 'tt_karma_builder'),
								'param_name' => 'number_color',
							),
							array(
								'group'      => __('Design', 'tt_karma_builder'),
								'value'      => '#E8E8E8',
								'type'       => 'colorpicker',
								'heading'      => __('Track Color', 'tt_karma_builder'),
								'param_name' => 'track_color',
							),
							array(
								'group'      => __('Design', 'tt_karma_builder'),
								'value'      => '#a0dbe1',
								'type'       => 'colorpicker',
								'heading'    => __('Bar Color', 'tt_karma_builder'),
								'param_name' => 'bar_color',
							),
							array(
								'group'      => __('Design', 'tt_karma_builder'),
								'type'       => 'dropdown',
								'heading'    => __('Bar Style', 'tt_karma_builder'),
								'value'      => array(
										'Square'     => 'square',
										'Round'      => 'round'
								),
								'param_name' => 'style',
								'save_always' => true,
							),
							array(
								'group'      => __('Design', 'tt_karma_builder'),
								'value'      => '10',
								'type'       => 'textfield',
								'heading'    => __('Bar Width', 'tt_karma_builder'),
								'param_name' => 'bar_width',
							),
							array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),		
					    ),
     	   		) 
	);// END vc_map

/*--------------------------------------------------------------
Orbit - Circle Loader (icon)
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Circle Loader (icon)", 'tt_karma_builder'),
        'description' => __("Animated circle loader and icon", 'tt_karma_builder'),
        'base'        => "karma_builder_circle_loader_icon",
        'controls'    => 'full',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-circle-loader-icon.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'show_settings_on_create' => true,
        "params"      => array(
            			  array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

					        array(
        						'group'       => 'Content',
        						'heading'     => __('Number', 'tt_karma_builder'),
								'value'       => '50',
								'type'        => 'textfield',
								'description' => __('Percentage of the loader bar.', 'tt_karma_builder'),
								'param_name'  => 'number',
							),
							array(
								'group'      => 'Content',
								'heading'    => __('Content', 'tt_karma_builder'),
								'type'       => 'textarea_html',
								'param_name' => 'content',
								'value'      => __("<h3>Heading</h3><p>This content is displayed below the circle icon loader.</p>", 'tt_karma_builder')
							),
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
 							array(
								'group'      => 'Icon',
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),	
			               array(
			                  'group'      => 'Icon',
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			               ),														
							array(
								'value'       => '#d3565a',
								'group'      => __('Design', 'tt_karma_builder'),
								'type'        => 'colorpicker',
								'heading'     => __('Icon Color', 'tt_karma_builder'),
								'param_name'  => 'icon_color',
							),
							array(
								'value'      => '#E8E8E8',
								'group'      => __('Design', 'tt_karma_builder'),
								'type'       => 'colorpicker',
								'heading'      => __('Track Color', 'tt_karma_builder'),
								'param_name' => 'track_color',
							),
							array(
								'value'      => '#a0dbe1',
								'group'      => __('Design', 'tt_karma_builder'),
								'type'       => 'colorpicker',
								'heading'    => __('Bar Color', 'tt_karma_builder'),
								'param_name' => 'bar_color',
							),
							array(
								'type'       => 'dropdown',
								'group'      => __('Design', 'tt_karma_builder'),
								'heading'    => __('Bar Style', 'tt_karma_builder'),
								'value'      => array(
								'Square'     => 'square',
								'Round'      => 'round'
								),
								'param_name' => 'style',
								'save_always' => true,
							),
							array(
								'value'      => '10',
								'group'      => __('Design', 'tt_karma_builder'),
								'type'       => 'textfield',
								'heading'    => __('Bar Width', 'tt_karma_builder'),
								'param_name' => 'bar_width',
							),
							array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Dropcap
--------------------------------------------------------------*/
vc_map(      
	array(
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __('Dropcap', 'tt_karma_builder'),
        'description' => __("Stylish dropcap element", 'tt_karma_builder'),
        'base'        => 'karma_builder_dropcap',
        'controls'    => 'full',
        'class'       => 'true-dropcap',
        'js_view'     => 'OrbitDropcap',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-dropcap.png', __FILE__),
        'params'      => array(
            			   array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			               array(
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'heading'    => __("Color scheme", 'tt_karma_builder'),
			                  'param_name' => 'color',
			                  'value' => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',
									),
			                  'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-dropcap.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder'),
			                  'save_always' => true,
			              ),
							array(
				                  'type'       => 'dropdown',
				                  'holder'     => 'div',
				                  'heading'    => __("Style", 'tt_karma_builder'),
				                  'param_name' => 'style',
				                  'value' => array('Round'=> 'round','Square'=> 'square','Text'=> 'text'),
				                  'save_always' => true,
				              ),
							array(
								  'heading'     => __("Dropcap", 'tt_karma_builder'),
				                  'type'        => "textfield",
				                  'holder'      => 'div',
				                  'value'       => 'O',
				                  'param_name'  => 'dropcap',
				                  'description' => __('The single character to be "drop-capped".', 'tt_karma_builder')
				              ),
							array(
							 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'param_name'    => 'content',
				    		 'value'         => __("<p>This text displayed next to the dropcap.</p>", 'tt_karma_builder')
			              ),
							array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			           )
	) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Feature List Item
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Feature List Item", 'tt_karma_builder'),
        'description' => __("Animated features list", 'tt_karma_builder'),
        'base'        => "karma_builder_features",
        'controls'    => 'full',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-feature-list.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'class'       => 'true-feature-list',
        'js_view'     => 'OrbitFeatureListItem',
        'show_settings_on_create' => true,
        "params"      => array(
					      array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									'no animation'       => 'animate_none',
									
								),
								'save_always' => true,
					        ),
					      array(
			              	'group'         => __('General', 'tt_karma_builder'),
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'      => __("<h3>Feature #1</h3><p>Lorem ipsum dolor sit amet.</p>", 'tt_karma_builder')
			              ),
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
					      array(
					      		'group'  => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),
			               array(
					      	  'group'  => __('Icon', 'tt_karma_builder'),			               
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),							
					        array(
					        	'group'      => __('Design', 'tt_karma_builder'),
								'value'       => '#d3565a',
								'type'        => 'colorpicker',
								'heading'     => __('Icon Color', 'tt_karma_builder'),
								'param_name'  => 'icon_color',
							),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __('Icon Color (hover)', 'tt_karma_builder'),
								'param_name'    => "icon_color_hover",
								'value'         => "#ffffff",
								'description'   => __("The color of the icon when hovered.", 'tt_karma_builder')
					        ),	
				            array(
				            	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => "textfield",
								'holder'        => 'div',
								'heading'       => __("Border Width", 'tt_karma_builder'),
								'param_name'    => "border_width",
								'value'         => "2px",
								'description'   => __("The width of the circle border.", 'tt_karma_builder')
				              ),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __("Border Color", 'tt_karma_builder'),
								'param_name'    => "border_color",
								'value'         => "#a2dce2",
								'description'   => __("The color of the circle border.", 'tt_karma_builder')
					        ),	
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __("Background Color", 'tt_karma_builder'),
								'param_name'    => "bg_color",
								'value'         => "#fff",
								'description'   => __("The color of the circle.", 'tt_karma_builder')
					        ),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __("Background Color (hover)", 'tt_karma_builder'),
								'param_name'    => "bg_color_hover",
								'value'         => "#a2dce2",
								'description'   => __("The color of the circle when hovered.", 'tt_karma_builder')
					        ),								        
							array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox_content",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Lightbox text", 'tt_karma_builder'),
			                  'param_name'  => "lightbox_description",
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),							
					),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Gap
--------------------------------------------------------------*/
//gap shortcode from karma theme
   vc_map( array(
        'name'            => __("Gap", 'tt_karma_builder'),
        'description'     => __("Blank space with custom height", 'tt_karma_builder'),
        'base'            => "gap",
        'controls'        => array(),
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-gap.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
 				            array(
				    		 'type'          => "textfield",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Size', 'tt_karma_builder'),
				    		 'param_name'    => 'size',
			                 'description' => __('Enter the height.', 'tt_karma_builder'),
			                 'value'       => __('100px', 'tt_karma_builder'),				    		 
			              ),						        

     	   		)
     	 )
);// END vc_map

/*--------------------------------------------------------------
Orbit - Heading
--------------------------------------------------------------*/
vc_map( array(
        'name'        => __("Heading", 'tt_karma_builder'),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'description' => __("A stylish heading with subheading (H1-H6)", 'tt_karma_builder'),
        'base'        => "karma_builder_heading",
        'controls'    => 'full',
        'class'       => 'true-heading',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-heading.png', __FILE__),
        "params"      => array(
            				array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
        						'group'       => __('Content', 'tt_karma_builder'),
        						'heading'     => __('Heading Text', 'tt_karma_builder'),
								'value'       => 'Hello',
								'type'        => 'textfield',
								'param_name'  => 'heading_text',
							),
							array(
			               	  'group'      => __('Content', 'tt_karma_builder'),
			               	  'heading'    => __('Sub-Heading Text', 'tt_karma_builder'),
			                  'type'       => 'textarea',
			                  'holder'     => 'div',
			                  'param_name' => 'sub_heading_text',
			                  'value'      => __("", 'tt_karma_builder')
			              ),
							array(
			               	  'group'       => __('Content', 'tt_karma_builder'),
			               	  'heading'     => __('Sub-Heading Text (Advanced)', 'tt_karma_builder'),
			                  'type'        => 'textarea_html',
			                  'holder'      => 'div',
			                  'param_name'  => 'content',
			                  'value'       => __("", 'tt_karma_builder'),
			                  'description' => __('Use this field instead for greater sub-heading options.', 'tt_karma_builder')
			              ),
							array(
			               	  'group'      => __('Content', 'tt_karma_builder'),
			               	  'heading'    => __('Heading Color', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "heading_color",
			                  'value'         => '#363636'
			              ),
							array(
			               	  'group'      => __('Content', 'tt_karma_builder'),
			               	  'heading'    => __('Sub-Heading Color', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "sub_heading_color",
			                  'value'         => '#555'
			              ),
							array(
			               	  'group'      	=> __('Content', 'tt_karma_builder'),
			               	  'heading'    	=> __('Link Color', 'tt_karma_builder'),
			                  'type'        => 'colorpicker',
			                  'holder'      => 'div',
			                  'param_name'  => "sub_heading_link_color",
			                  'value'       => '',
			                  'description' => __('Custom link color for sub-heading text advanced', 'tt_karma_builder')
			              ),
							array(
							  'group'      => __('Content', 'tt_karma_builder'),
			              	  'heading'    => __('Top Margin', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'margin_top',
			                  'value'     => __('20px', 'tt_karma_builder'),
			              ),
        				 array(
        				 	  'group'      => __('Content', 'tt_karma_builder'),
			              	  'heading'    => __('Bottom Margin', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'margin_bottom',
			                  'value'     => __('30px', 'tt_karma_builder'),
			              ),
							array(
        						'group'       => __('Heading', 'tt_karma_builder'),
        						'heading'     => __('Heading Size', 'tt_karma_builder'),
								'value'       => '30px',
								'type'        => 'textfield',
								'param_name'  => 'heading_size',
							),
							array(
			               	  'group'      => __('Heading', 'tt_karma_builder'),
			               	  'heading'    => __('Heading Type', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'param_name' => 'heading_type',
			                  'value' => array(
									'H3'  => 'h3',
									'H1'  => 'h1',
									'H2'  => 'h2',
									'H4'  => 'h4',
									'H5'  => 'h5',
									'H6'  => 'h6'
								),
								'save_always' => true,
			              ),
							array(
			               	  'group'      => __('Heading', 'tt_karma_builder'),
			               	  'heading'    => __('Heading Style', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'holder'     => 'div',
			                  'param_name' => 'heading_style',
			                  'value' => array(
									'Normal'    => 'none',
									'UPPERCASE' => 'uppercase'
								),
								'save_always' => true,
			              ), 
			               array(
        						'group'       => __('Sub-Heading', 'tt_karma_builder'),
        						'heading'     => __('Sub-Heading Size', 'tt_karma_builder'),
								'value'       => '16px',
								'type'        => 'textfield',
								'param_name'  => 'sub_heading_size',
							),
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			               	  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
        				)
			) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Icon Box
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Icon Box", 'tt_karma_builder'),
        'description' => __("Stylish vector icon callout box", 'tt_karma_builder'),
        'base'        => "tt_vector_box",
        'controls'    => 'full',
        'class'       => 'true-icon-box',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-icon-box.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitIconBox',
        "params"      => array(
            			array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        				//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons			
					      array(
					      		'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),	
			               array(
					      	  'group'       => __('Icon', 'tt_karma_builder'),		               
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),													
							array(
									'group'       => __('Icon', 'tt_karma_builder'),
									'heading'     => __('Icon Size', 'tt_karma_builder'),
									'type'        => 'dropdown',
									'save_always' => true,
									'holder'      => 'div',
									'param_name'  => 'icon_size',
									'description' => __('Small: fa-3x , Medium: fa-4x , Large: fa-5x', 'tt_karma_builder'),
									'value'       => array(
									'Small'       => 'fa-3x',
									'Medium'      => 'fa-4x',
									'Large'       => 'fa-5x'
								)
			              ),
			               array(
				    		 'type'          => "textarea_html",
				    		 'group'         => __('Content', 'tt_karma_builder'),
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'      => __("<h3>Heading</h3><p>Lorem ipsum dolor sit amet.</p>", 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			               	  'heading'       => __("Box BG color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => 'box_bg_color',
			                  'value'         => '#fff',
			                  'description' => __('The main background color of the box', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'heading'       => __("Icon color", 'tt_karma_builder'),
			                  'param_name'    => 'icon_color',
			                  'value'         => '#fff'
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'heading'       => __("Icon BG color", 'tt_karma_builder'),
			                  'param_name'    => 'icon_bg_color',
			                  'value'         => '',
			                  'description' => __('This is the colored circle behind the icon. If left empty it will adopt the color of the active theme color scheme.  ', 'tt_karma_builder')
			              ),
			               array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox_content",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Lightbox text", 'tt_karma_builder'),
			                  'param_name'  => "lightbox_description",
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			              						              						              						           
        				)
			) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Icon + Text
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Icon + Text", 'tt_karma_builder'),
        'description' => __("Round vector icon with content", 'tt_karma_builder'),
        'base'        => "karma_builder_icon_content",
        'controls'    => 'full',
        'class'       => 'true-icon-text',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-icon-text.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitIconText',
        "params"      => array(
            			array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons			
					      array(
					      		'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),	
			               array(
					      	  'group'       => __('Icon', 'tt_karma_builder'),		               
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			               ),							
													
							array(
			                  'type'          => 'colorpicker',
			                  'group'       => __('Icon', 'tt_karma_builder'),
			                  'holder'        => 'div',
			                  'heading'       => __("Icon color", 'tt_karma_builder'),
			                  'param_name'    => 'icon_color',
			                  'value'         => '#fff'
			              ),
			               array(
			                  'type'          => 'colorpicker',
			                  'group'       => __('Icon', 'tt_karma_builder'),
			                  'holder'        => 'div',
			                  'heading'       => __("Icon BG color", 'tt_karma_builder'),
			                  'param_name'    => "icon_bg_color",
			                  'value'         => '#3b86c4',
			                  'description' => __('The colored circle behind the icon.', 'tt_karma_builder')
			              ),
			               array(
				    		 'type'          => "textarea_html",
				    		 'group'         => __('Content', 'tt_karma_builder'),
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'      => __("<p><strong>Heading</strong></p><p>Lorem ipsum dolor sit amet.</p>", 'tt_karma_builder')
			              ),
			               array(
				    		 'type'          => "textfield",
				    		 'group'         => __('Content', 'tt_karma_builder'),
				    		 'holder'        => 'div',
				    		 'heading'       => __('Paragraph Font Size', 'tt_karma_builder'),
				    		 'param_name'    => 'paragraph_font_size',
				    		 'description'   => __("Customize the paragraph font size. example: 12px", 'tt_karma_builder'),
				    		 'value' 		 => '12px',
			              ),			              
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			              						              						              						           
        				)
			) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Icon PNG
--------------------------------------------------------------*/
vc_map(      
	array(
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __("Icon PNG", 'tt_karma_builder'),
        'description' => __("65 Stylish PNG icons", 'tt_karma_builder'),
        'base'        => 'karma_builder_icon_png',
        'controls'    => 'full',
        'class'       => 'true-icon-png',
        'js_view'     => 'OrbitIconImage',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-icon-png.png', __FILE__),
        "params"      => array(
            				array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			               array(
			               	  'group'      => __('General', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'heading'    => __("Select an Icon", 'tt_karma_builder'),
			                  'param_name' => "icon",
			                  'value' => array(
									'Alarm'                  => 'icon-alarm',
									'Arrow Down'             => 'icon-arrow-down-a',
									'Arrow Down 2'           => 'icon-arrow-down-b',
									'Arrow Up'               => 'icon-arrow-up-a',
									'Arrow Up 2'             => 'icon-arrow-up-b',
									'Calculator'             => 'icon-calculator',
									'Calendar - Day'         => 'icon-calendar-day',
									'Calendar - Month'       => 'icon-calendar-month',
									'Camera'                 => 'icon-camera',
									'Cart - Ecommerce'       => 'icon-cart-add',
									'Caution'                => 'icon-caution',
									'Cell Phone'             => 'icon-cellphone',
									'Chart'                  => 'icon-chart',
									'Chat (speech bubble)'   => 'icon-chat',
									'Chat 2 (speech bubble)' => 'icon-chat-2',
									'Checklist'              => 'icon-checklist',
									'Checkmark'              => 'icon-checkmark',
									'Clipboard'              => 'icon-clipboard',
									'Clock'                  => 'icon-clock',
									'Cog (sprocket)'         => 'icon-gear',
									'Contacts'               => 'icon-contacts',
									'Crate (wooden box)'     => 'icon-crate',
									'Database'               => 'icon-database',
									'Document edit'          => 'icon-document-edit',
									'DVD'                    => 'icon-dvd',
									'Email'                  => 'icon-email-send',
									'Flag'                   => 'icon-flag',
									'Games'                  => 'icon-games',
									'Globe'                  => 'icon-globe',
									'Globe - download'       => 'icon-globe-download',
									'Globe - upload'         => 'icon-globe-upload',
									'Hard Drive (HDD)'       => 'icon-drive',
									'HDTV'                   => 'icon-hdtv',
									'Heart'                  => 'icon-heart',
									'History'                => 'icon-history',
									'Home'                   => 'icon-home',
									'Info'                   => 'icon-info',
									'Laptop'                 => 'icon-laptop',
									'Lightbulb'              => 'icon-light-on',
									'Lock'                   => 'icon-lock-closed',
									'Magnifying Glass'       => 'icon-magnify',
									'Megaphone'              => 'icon-megaphone',
									'Money'                  => 'icon-money',
									'Movie'                  => 'icon-movie',
									'MP3 Player'             => 'icon-mp3',
									'MS Word Document'       => 'icon-ms-word',
									'Music'                  => 'icon-music',
									'Network'                => 'icon-network',
									'News'                   => 'icon-news',
									'Notebook'               => 'icon-notebook',
									'PDF Document'           => 'icon-pdf',
									'Photos'                 => 'icon-photos',
									'Notebook'               => 'icon-notebook',
									'Refresh'                => 'icon-refresh',
									'RSS'                    => 'icon-rss',
									'Shield (blue)'          => 'icon-shield-blue',
									'Shield (green)'         => 'icon-shield-green',
									'Smartphone'             => 'icon-smart-phone',
									'Star'                   => 'icon-star',
									'Support'                => 'icon-support',
									'Tools'                  => 'icon-tools',
									'Users'                  => 'icon-user-group',
									'vCard'                  => 'icon-vcard',
									'Video Camera'           => 'icon-video-camera',
									'X'                      => 'icon-x'
								)
			              ),
							array(
									 'group'      => __('General', 'tt_karma_builder'),
									 'heading'       => __('Content', 'tt_karma_builder'),
						    		 'type'          => "textarea_html",
						    		 'holder'        => 'div',
						    		 'param_name'    => 'content',
						    		 'value'      => __("<p>This text is displayed next to the icon.</p>", 'tt_karma_builder')
			              	),
							array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox_content",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox text', 'tt_karma_builder'),
			                  'param_name'  => 'lightbox_description',
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			           )
	) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Image Box 1
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Image Box - square", 'tt_karma_builder'),
        'description' => __("A callout box with image and text", 'tt_karma_builder'),
        'base'        => "karma_builder_imagebox_1",
        'controls'    => 'full',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-image-box-1.png', __FILE__),
        'js_view'     => 'OrbitImage_1',
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            			   array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			               array(
			               	  'group'         => __('General', 'tt_karma_builder'),
			                  'type'          => "attach_image",
			                  'heading'       => __("Image", 'tt_karma_builder'),
			                  'param_name'    => "attachment_id",
			              ),
			               array(
			               	  'group'         => __('General', 'tt_karma_builder'),
			                  'type'          => "textfield",
			                  'heading'       => __("Main Title", 'tt_karma_builder'),
			                  'value'         => __("Main Title", 'tt_karma_builder'),
			                  'param_name'    => "main_title",
			                  'description' => __('This is the larger more prominent title.', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('General', 'tt_karma_builder'),
			                  'type'          => "textfield",
			                  'heading'       => __("Sub Title", 'tt_karma_builder'),
			                  'value'         => __("Sub Title", 'tt_karma_builder'),
			                  'param_name'    => "sub_title"
			              ),
			              array(
			              	 'group'         => __('General', 'tt_karma_builder'),
				    		 'type'          => "textarea_html",
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'      => __("<p>Edit this text with custom content.</p>", 'tt_karma_builder')
			              ),
			              array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			               	  'heading'       => __("Box BG color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'param_name'    => 'box_bg_color',
			                  'value'         => '#fff',
			                  'description' => __('The main background color of the box', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Image Border Width", 'tt_karma_builder'),
			                  'type'          => 'textfield',
			                  'param_name'    => "img_border_width",
			                  'value'         => "8px",
			                  'description' => __('The colored border below the image', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Image Border Color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'param_name'    => "img_border_color",
			                  'value'         => "#cf6e6e"
			              ),
			              array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'       => __("Main Title Color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'param_name'    => "main_title_color",
			                  'value'         => "#cf6e6e"
			              ),
			              array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox_content",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'heading'     => __('Lightbox text', 'tt_karma_builder'),
			                  'param_name'  => 'lightbox_description',
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),					              
			              						              						              						           
        				)
			) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Image Box 2
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Image Box - round", 'tt_karma_builder'),
        'description' => __("A callout box with image, text and icon", 'tt_karma_builder'),
        'base'        => "karma_builder_imagebox_2",
        'controls'    => 'full',
        'js_view'     => 'OrbitImage_2',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-image-box-2.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            				array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
			               	  'group'         => __('General', 'tt_karma_builder'),
			                  'type'          => "attach_image",
			                  'heading'       => __("Image", 'tt_karma_builder'),
			                  'param_name'    => "attachment_id"
			              ),
			               array(
			               	 'group'         => __('General', 'tt_karma_builder'),
				    		 'type'          => "textarea_html",
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'         => __("<h2>Heading</h2><p>Edit this text with custom content.</p>", 'tt_karma_builder')
			              ),
							//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
					      array(
					      		'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),							
			               array(
			               	  'group'       => __('Icon', 'tt_karma_builder'),		               
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),															 
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			               	  'heading'       => __("Box BG color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'param_name'    => 'box_bg_color',
			                  'value'         => '#fff',
			                  'description' => __('The main background color of the box', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'heading'       => __("Icon BG color", 'tt_karma_builder'),
			                  'param_name'    => 'icon_bg_color',
			                  'value'         => '#87C442',
			                  'description' => __('The circle behind the vector icon', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'heading'       => __("Icon color", 'tt_karma_builder'),
			                  'param_name'    => 'icon_color',
			                  'value'         => '#fff',
			                  'description' => __('The vector icon', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'heading'       => __("Link color", 'tt_karma_builder'),
			                  'param_name'    => 'link_color',
			                  'value'         => '#3b86c4',
			                  'description' => __('The link color (optional)', 'tt_karma_builder')
			              ),
			              array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('URL (link)', 'tt_karma_builder'),
			               	  'heading'     => __('Link text', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'param_name'  => 'link_text',
			                  'description' => __('This text is displayed near the bottom of the box. (optional)', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox_content",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'heading'     => __('Lightbox text', 'tt_karma_builder'),
			                  'param_name'  => 'lightbox_description',
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
			              array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),				              
			              						              						              						           
        				)
			) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Image Frames
--------------------------------------------------------------*/
//image frame shortcode from karma theme
   vc_map( array(
        'name'            => __("Image Frames", 'tt_karma_builder'),
        'description'     => __("Stylish image frames", 'tt_karma_builder'),
        'base'            => "frame",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-image-frames.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Frame Style", 'tt_karma_builder'),
					            'param_name' => "style",
					            'value'      => array(
											'Modern'  => 'modern',
											'Shadow' => 'shadow',															
											),
					            'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/style-img-frames.png" target="_blank">View available styles &rarr;</a>', 'tt_karma_builder'),
					        ),	
        					array(
					            'type'       => 'dropdown',
					            'heading'    => __("Size", 'tt_karma_builder'),
					            'param_name' => "size",
					            'value'      => array(
											'Full Width Page -  BANNER - 922px x 201px'  => 'banner_full',
											'Full Width Page -  ONE_HALF (2 Column) - 437px x 234px' => 'two_col_large',	
											'Full Width Page -  ONE_THIRD (3 Column) - 275px x 145px' => 'three_col_large',		
											'Full Width Page -  ONE_THIRD (3 Column - Square) - 275px x 275px' => 'three_col_square',
											'Full Width Page -  ONE_FOURTH (4 Column) - 190px x 111px' => 'four_col_large',
											'Full Width Page - PORTRAIT BIG - 612px x 792px' => 'portrait_full',
											'Full Width Page - PORTRAIT SMALL - 275px x 355px' => 'portrait_thumb',
											'-----',
											'Sidebar Page -  BANNER - 703px x 201px' => 'banner_regular',
											'Sidebar Page -  ONE_HALF (2 Column) - 324px x 180px' => 'two_col_small',
											'Sidebar Page -  ONE_THIRD (3 Column) - 202px x 113px' => 'three_col_small',
											'Sidebar Page -  ONE_FOURTH (4 Column) - 135px x 76px' => 'four_col_small',
											'-----',
											'Sidebar + Sidenav Page -  BANNER - 493px x 201px' => 'banner_small',
											'-----',
											'SQUARE IMAGE FRAME - 190px x 180px' => 'square',											
											),
					            'description' => __("Select the size of image frame", 'tt_karma_builder'),
					            'save_always' => true,
					        ),
        					array(
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Float?", 'tt_karma_builder'),
					            'param_name' => "float",
					            'value'      => array(
											'Do not float'  => 'none',
											'Left' => 'left',
											'Right' => 'right',															
											)
					        ),						        					        
			               array(
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Image", 'tt_karma_builder'),
			                  'param_name'    => "local_uploaded_image_id",
			                  'description'   => __('Select or Upload Image', 'tt_karma_builder')
			              ),
			              array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("External Image (URL)", 'tt_karma_builder'),
			                  'param_name'  => "external_image_url",
			                  'description' => __('If your image is not uploaded to this site, you can enter the image url here. This will overwrite the above Image option.', 'tt_karma_builder')
			              ),
							array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'link',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Lightbox text", 'tt_karma_builder'),
			                  'param_name'  => "description",
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),		              			              			              					        	
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Lightbox Group", 'tt_karma_builder'),
			                  'param_name'  => "lightbox_group",
			                  'value' => __('1', 'tt_karma_builder'),
			                  'description' => __('Enter the same number for lightboxes that you want to group into slides. (optional)', 'tt_karma_builder')
			              ),

     	   		)
     	 )
);// END vc_map

/*--------------------------------------------------------------
Karma - List
--------------------------------------------------------------*/
//karma list
   vc_map( array(
        'name'            => __("List", 'tt_karma_builder'),
        'description'     => __("List with vector icons for bullet", 'tt_karma_builder'),
        'base'            => "karma_list",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-list.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only' => 'karma_list_item'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

//karma_list_items
   vc_map( array(
		        'name'            => __("List Item", 'tt_karma_builder'),
		        'description'     => __("Add a list item", 'tt_karma_builder'),
		        'base'            => "karma_list_item",
		        'controls'        => 'full',
		        'content_element' => true,
		        'icon'            => plugins_url('images/backend-editor/truethemes-menu-list.png', __FILE__),
		        'category'        => __('Karma Builder', 'tt_karma_builder'),
		        "as_child"        => array('only' => 'karma_list'),	        
		        "params"      => array(
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons	
				        array(
				        	  'group'   => __('Icon', 'tt_karma_builder'),
			              	  'heading'       => __("Icon Color", 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'param_name'    => "custom_color",
			                  'value'         => "#000000",
			                  'description'   => __('Set the icon color', 'tt_karma_builder')
			              ),							
		 		        array(
		 		        	 'group'   => __('Icon', 'tt_karma_builder'),
			        		 'type'          => "textarea_html",
			        		 'holder'        => 'div',
			        		 'heading'       => __('Content', 'tt_karma_builder'),
			        		 'param_name'    => 'content',
			                 'description' => __('Enter the link label', 'tt_karma_builder'),			    		 
			            ),
			    ),
     	 )
);// END vc_map

/*--------------------------------------------------------------
Orbit - Number Counter
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Number Counter", 'tt_karma_builder'),
        'description' => __("Animated number counter", 'tt_karma_builder'),
        'base'        => "karma_builder_number_counter",  
        'controls'    => 'full',
        'class'       => 'true-number-counter',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-number-counter.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitNumberCounter',
        'show_settings_on_create' => true,
							"params"     => array(
					    array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

					        // add params same as with any other content element
						array(
							'group'      => __('General', 'tt_karma_builder'),
							'heading'    => __('Number', 'tt_karma_builder'),
							'description'=> __('Commas are automatically added to larger numbers such as 125,000. Please avoid inputting commas. (example input: 125000)', 'tt_karma_builder'),
							'value'      => '125',
							'type'       => 'textfield',
							'param_name' => 'number',
						),
						array(
							'group'      => __('General', 'tt_karma_builder'),
							'heading'    => __('Number Font Size', 'tt_karma_builder'),
							'value'      => '50px',
							'type'       => 'textfield',
							'param_name' => 'number_size',
						),
						array(
							'group'       => __('General', 'tt_karma_builder'),
							'heading'     => __('Number Font Weight', 'tt_karma_builder'),
							'type'        => 'dropdown',
							'param_name'  => 'number_weight',
							'save_always' => true,
							'value'       => array(
											    '100' => '100',
												'400' => '400',
												'600' => '600',
											),
						),
						array(
							'group'      => __('General', 'tt_karma_builder'),
							'heading'    => __('Divider Height', 'tt_karma_builder'),
							'value'      => '4px',
							'type'       => 'textfield',
							'param_name' => 'divider_height',
							'description' => __("This divider is displayed between the number and title.", 'tt_karma_builder')
						),
						array(
							'value'      => 'Lorem Ipsum',
							'group'      => __('General', 'tt_karma_builder'),
							'type'       => 'textfield',
							'heading'    => __('Title', 'tt_karma_builder'),
							'param_name' => 'title',
							'description' => __("This text is displayed below the divider.", 'tt_karma_builder')
						),
						array(
							'group'      => __('General', 'tt_karma_builder'),
							'heading'    => __('Title Font Size', 'tt_karma_builder'),
							'value'      => '18px',
							'type'       => 'textfield',
							'param_name' => 'title_size',
						),
						array(
							'group'       => __('General', 'tt_karma_builder'),
							'heading'     => __('Title Font Weight', 'tt_karma_builder'),
							'type'        => 'dropdown',
							'param_name'  => 'title_weight',
							'save_always' => true,
							'value'       => array(
											    '100' => '100',
												'400' => '400',
												'600' => '600',
											),
						),
						array(
							'value'      => '#000',
							'group'      => __('Design', 'tt_karma_builder'),
							'type'       => 'colorpicker',
							'heading'    => __('Number Color', 'tt_karma_builder'),
							'param_name' => 'number_color',
						),
						array(
							'value'      => '#e1e1e1',
							'group'      => __('Design', 'tt_karma_builder'),
							'type'       => 'colorpicker',
							'heading'    => __('Divider Color', 'tt_karma_builder'),
							'param_name' => 'divider_color',
							'description' => __("This divider is displayed between the number and title.", 'tt_karma_builder')
						),
						array(
							'value'      => '#000',
							'group'      => __('Design', 'tt_karma_builder'),
							'type'       => 'colorpicker',
							'heading'    => __('Title Color','tt_karma_builder'),
							'param_name' => 'title_color',
						),
						array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),								

					),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Pricing Box
--------------------------------------------------------------*/
vc_map(      
	array(
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __("Pricing Box", 'tt_karma_builder'),
        'description' => __("Stylish pricing box", 'tt_karma_builder'),
        'base'        => "karma_builder_pricing_box",
        'controls'    => 'full',
        'class'       => 'true-pricing-box',
        'js_view'     => 'OrbitPricingBox',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-pricing-box.png', __FILE__),
        "params"      => array(
            			array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              array(
			              	  'group'      => __('Design', 'tt_karma_builder'),
			              	  'heading'    => __('Layout style', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => 'style',
			                  'value' => array('Style 1'=> 'style-1','Style-2'=> 'style-2'),
			                  'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/style-pricing.png" target="_blank">View available layout styles &rarr;</a>', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'      => __('Design', 'tt_karma_builder'),
			               	  'heading'    => __("Color scheme", 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => "color",
			                  'value' => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',
									),
			                  'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-pricing.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder')
			              ),
							array(
			               	  'group'      => __('Design', 'tt_karma_builder'),
			               	  'heading'    => __("Button color", 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => "button_color",
			                  'value' => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',
									),
			                  'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-buttons.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder')
			              ),
							array(
							  'group'       => 'Content',
							  'heading'     => __('Price', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'price',
			                  'value'       => '39',
			              ),
							array(
							  'group'       => 'Content',
							  'heading'     => __('Currency symbol', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'currency',
			                  'value'       => '$',
			                  'description' => __('ie. $, &euro;', 'tt_karma_builder')
			              ),
							array(
							  'group'       => 'Content',
							  'heading'     => __('Plan name', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'plan',
			                  'value'       => 'Pro',
			                  'description' => __('ie. Basic, Pro, Premium', 'tt_karma_builder')
			              ),
							array(
							  'group'       => 'Content',
							  'heading'     => __('Term', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'term',
			                  'value'       => 'per month',
			                  'description' => __('ie. per month, per year', 'tt_karma_builder')
			              ),
							array(
							  'group'       => 'Content',
							  'heading'     => __('Details', 'tt_karma_builder'),
			                  'type'        => 'textarea_html',
			                  'holder'      => 'div',
			                  'param_name'  => 'content',
			                  'value'       => __("<ul><li><strong>Full</strong> Email Support</li><li><strong>25GB</strong> of Storage</li><li><strong>5</strong> Domains</li><li><strong>10</strong> Email Addresses</li></ul>", 'tt_karma_builder')
			              ),
							array(
							  'group'      => 'Button (URL)',
			                  'type'       => "textfield",
			                  'holder'     => 'div',
			                  'heading'    => __("Button text", 'tt_karma_builder'),
			                  'param_name' => 'button_label',
			                  'value'      => 'Sign Up',
			                  'description' => __('ie. Sign up, Purchase, Register', 'tt_karma_builder')
			              ),
							array(
							  'group'      => 'Button (URL)',
							  'heading'    => __('Button Size', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => "button_size",
			                  'value' => array('Small'=> 'small','Medium'=> 'medium','Large'=> 'large')
			              ),
							array(
							  'group'       => 'Button (URL)',
							  //'heading'   => ** this is empty for cleaner user-interface
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this button.', 'tt_karma_builder')
			              ),
							array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
		)
	) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Progress Bar
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Progress Bar", 'tt_karma_builder'),
        'description' => __("Animated progress bar", 'tt_karma_builder'),
        'base'        => "karma_builder_progress_bar",
        'controls'    => 'full',
        'class'       => 'true-progress-bar',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-progress-bar.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitProgressBar',
        'show_settings_on_create' => true,
        "params"      => array(
            				array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

								array(
									'group'      => __('General', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Title', 'tt_karma_builder'),
									'value'      => 'Lorem Ipsum',
									'param_name' => 'title',
								),
								array(
									'group'      => __('General', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Number', 'tt_karma_builder'),
									'value'      => '50',
									'param_name' => 'number',
								),
								array(
									'group'      => __('General', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Symbol', 'tt_karma_builder'),
									'param_name' => 'symbol',
									'value'      => '%',
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Bar Height', 'tt_karma_builder'),
									'value'      => '20px',
									'param_name' => 'progress_height',
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'type'       => 'checkbox',
									'heading'    => __( 'Round Style', 'tt_karma_builder' ),
									'description' => __( 'Check the box for rounded style.', 'tt_karma_builder' ),
									'param_name' => 'rounded_progress',
									'value'      => array( __( '', 'tt_karma_builder' ) => 'true-progress-rounded' )
								),		
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'      => __('Title Color', 'tt_karma_builder'),
									'value'      => '#000',
									'type'       => 'colorpicker',
									'param_name' => 'title_color'
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'      => __('Number Color', 'tt_karma_builder'),
									'value'      => '#000',
									'type'       => 'colorpicker',
									'param_name' => 'number_color'
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'      => __('Bar Color', 'tt_karma_builder'),
									'value'      => '#a2dce2',
									'type'       => 'colorpicker',
									'param_name' => 'bar_color',
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'    => __('Track Color', 'tt_karma_builder'),
									'value'      => '#e1e1e1',
									'type'       => 'colorpicker',
									'param_name' => 'track_color',
								),
								array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),									
					    ),
     	   		) 
	);// END vc_map

/*--------------------------------------------------------------
Orbit - Progress Bar (vertical)
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Progress Bar 2", 'tt_karma_builder'),
        'description' => __("Animated progress bar (vertical)", 'tt_karma_builder'),
        'base'        => "karma_builder_progress_bar_vertical",
        'controls'    => 'full',
        'class'       => 'true-progress-bar',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-progress-bar-vertical.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitProgressBar2',
        'show_settings_on_create' => true,
        "params"      => array(
            				array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

					        array(
									'group'      => __('General', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Title', 'tt_karma_builder'),
									'value'      => 'Lorem Ipsum',
									'param_name' => 'title',
								),
								array(
									'group'      => __('General', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Number', 'tt_karma_builder'),
									'value'      => '50',
									'param_name' => 'number',
								),
								array(
									'group'      => __('General', 'tt_karma_builder'),
									'type'       => 'textfield',
									'heading'    => __('Symbol', 'tt_karma_builder'),
									'param_name' => 'symbol',
									'value'      => '%',
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'      => __('Title Color', 'tt_karma_builder'),
									'value'      => '#000',
									'type'       => 'colorpicker',
									'param_name' => 'title_color'
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'      => __('Number Color', 'tt_karma_builder'),
									'value'      => '#000',
									'type'       => 'colorpicker',
									'param_name' => 'number_color'
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'      => __('Bar Color', 'tt_karma_builder'),
									'value'      => '#a2dce2',
									'type'       => 'colorpicker',
									'param_name' => 'bar_color',
								),
								array(
									'group'      => __('Design', 'tt_karma_builder'),
									'heading'    => __('Track Color', 'tt_karma_builder'),
									'value'      => '#e1e1e1',
									'type'       => 'colorpicker',
									'param_name' => 'track_color',
								),
								array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Separator (shadow & divider line)
--------------------------------------------------------------*/
vc_map( array(
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __("Separator", 'tt_karma_builder'),
        'description' => __("Horizontal separator", 'tt_karma_builder'),
        'base'        => "karma_separator_line",
        'controls'    => 'full',
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-separator.png', __FILE__),
        "params"      => array(
			               array(
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'heading'    => __("Style", 'tt_karma_builder'),
			                  'param_name' => "style",
			                  'value'      => array(
											'Shadow' => 'separator_shadow',
											'Clean Line'   => 'separator_clean_line',
											)
			              ),
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			          )
			) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Separator + Text (formerly "Horizontal Heading")
--------------------------------------------------------------*/
//heading_horizontal shortcode from karma theme
   vc_map( array(
        'name'            => __("Separator + Text", 'tt_karma_builder'),
        'description'     => __("Horizontal separator line with heading", 'tt_karma_builder'),
        'base'            => "heading_horizontal",
        'controls'        => array(),
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-separator-text.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        'show_settings_on_create' => true,
        "params"          =>  array(
        				 array(
			              	  'heading'    => __('Heading Size', 'tt_karma_builder'),
			                  'type'        => 'dropdown',
			                  'save_always' => true,
			                  'holder'      => 'div',
			                  'param_name'  => 'type',
			                  'description'     => __('Select the heading size', 'tt_karma_builder'),
			                  'value'      => array(
											'h1' => 'h1',
											'h2' => 'h2',
											'h3' => 'h3',
											'h4' => 'h4',
											'h5' => 'h5',
											'h6' => 'h6'
											)
			              ),
        				 array(
							  'heading'     => __('Heading', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'content',
			                  'value'       => __('Our Services', 'tt_karma_builder'),
			                  'description'     => __('Enter the heading text', 'tt_karma_builder'),
			              ),
        				 array(
			              	  'heading'    => __('Top Margin', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'margin_top',
			                  'value'     => __('20px', 'tt_karma_builder'),
			              ),
        				 array(
			              	  'heading'    => __('Bottom Margin', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'margin_bottom',
			                  'value'     => __('20px', 'tt_karma_builder'),
			              ),			              
					    ),
     	 )
     	 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Service List Item
--------------------------------------------------------------*/
   vc_map( array(
        'name'        => __("Service List Item", 'tt_karma_builder'),
        'description' => __("Animated services list", 'tt_karma_builder'),
        'base'        => "karma_builder_services",  
        'controls'    => 'full',
        'class'       => 'true-service-list',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-feature-list.png', __FILE__),
        'category'    => __('Karma Builder', 'tt_karma_builder'),
        'js_view'     => 'OrbitServiceListItem',
        'show_settings_on_create' => true,
        "params"      => array(
					        array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'save_always' => true,
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									'no animation'       => 'animate_none',
								)
					        ),
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
					      array(
					      		'group'  => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),
			               array(
					      	  'group'  => __('Icon', 'tt_karma_builder'),			               
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),													
			              array(
			              	'group'         => __('General', 'tt_karma_builder'),
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'      => __("<h4>Heading</h4><p>Lorem ipsum dolor sit amet.</p>", 'tt_karma_builder')
			              ),	
					        array(
					        	'group'      => __('Design', 'tt_karma_builder'),
								'value'       => '#d3565a',
								'type'        => 'colorpicker',
								'heading'     => __('Icon Color', 'tt_karma_builder'),
								'param_name'  => 'icon_color',
							),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __('Icon Color (hover)', 'tt_karma_builder'),
								'param_name'    => "icon_color_hover",
								'value'         => "#FFFFFF",
								'description'   => __("The color of the icon when hovered.", 'tt_karma_builder'),
					        ),	
				            array(
				            	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => "textfield",
								'holder'        => 'div',
								'heading'       => __("Border Width", 'tt_karma_builder'),
								'param_name'    => "border_width",
								'value'         => "2px",
								'description'   => __("The width of the circle border.", 'tt_karma_builder')
				              ),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __("Border Color", 'tt_karma_builder'),
								'param_name'    => "border_color",
								'value'         => "#a2dce2",
								'description'   => __("The color of the circle border.", 'tt_karma_builder')
					        ),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __("Background Color", 'tt_karma_builder'),
								'param_name'    => "bg_color",
								'value'         => "#fff",
								'description'   => __("The color of the circle.", 'tt_karma_builder')
					        ),
					        array(
					        	'group'         => __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'       => __("Background Color (hover)", 'tt_karma_builder'),
								'param_name'    => "bg_color_hover",
								'value'         => "#a2dce2",
								'description'   => __("The color of the circle when hovered.", 'tt_karma_builder')
					        ),								        
							array(
							  'group'       => __('URL (link)', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link this element. (optional)', 'tt_karma_builder')
			              ),
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Lightbox', 'tt_karma_builder'),
			                  'param_name'  => "lightbox_content",
			                  'description' => __('Display content inside a lightbox by entering the URL here. This will override any URL (link) settings on the previous tab. <a href="https://s3.amazonaws.com/Plugin-Vision/lightbox-samples.html" target="_blank">Lightbox content samples &rarr;</a>', 'tt_karma_builder')
			              ),  
			              	array(
			              	  'group'       => __('Lightbox', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Lightbox text", 'tt_karma_builder'),
			                  'param_name'  => "lightbox_description",
			                  'description' => __('This text is displayed within the lightbox (optional)', 'tt_karma_builder')
			              ),
			              	array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Social Icons
--------------------------------------------------------------*/
vc_map(      
	array(
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __("Social Icons", 'tt_karma_builder'),
        'description' => __("Karma social icons", 'tt_karma_builder'),
        'base'        => "social",
        'controls'    => 'full',
        'class'       => 'true-social-icons',
        'js_view'     => 'OrbitSocialIcons',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-social-icons.png', __FILE__),
        "params"      => array(
            			   array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              array(
			              	  'group'      => __('General', 'tt_karma_builder'),
			              	  'heading'    => __('Design Style', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => 'style',
			                  'value'      => array(
			                  					'Images'      => 'image',
												'Vector Icons (will take on theme colors)'       => 'vector',
												'Vector Icons Color (pre-designed with social colors)' => 'vector_color',
			                  	),
			                  'description' => __('', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'      => __('General', 'tt_karma_builder'),
			              	  'heading'    => __('Show Title?', 'tt_karma_builder'),
			              	  'description' => __('(ie "Twitter", "Facebook", "RSS")', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => 'show_title',
			                  'value'      => array(
			                  					'No' => 'false',
			                  					'Yes'  => 'true',
			                  	),
			              ),
			              array(
			              	  'group'      => __('General', 'tt_karma_builder'),
			              	  'heading'    => __('Target', 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => 'target',
			                  'value'      => array(
			                  					'_self' => '_self',
			                  					'_blank'  => '_blank',
			                  	),
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('RSS Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'rss',
			                  'value'     => get_bloginfo_rss('rss_url'),
			                  'save_always' => true,
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('RSS Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'rss_title',
			                  'value'     => __('RSS', 'tt_karma_builder'),			                  
			              ),			              			              
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Twitter Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'twitter'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Twitter Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'twitter_title',
			                  'value'     => __('Twitter', 'tt_karma_builder'),			                  
			              ),			              
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Facebook Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'facebook'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Facebook Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'facebook_title',
			                  'value'     => __('Facebook', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Email Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'email'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Email Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'email_title',
			                  'value'     => __('Email', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Flickr Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'flickr'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Flickr Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'flickr_title',
			                  'value'     => __('Flickr', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Youtube Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'youtube'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Youtube Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'youtube_title',
			                  'value'     => __('Youtube', 'tt_karma_builder'),			                  
			              ),	
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Linkedin Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'linkedin'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Linkedin Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'linkedin_title',
			                  'value'     => __('Linkedin', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Pinterest Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'pinterest'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Pinterest Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'pinterest_title',
			                  'value'     => __('Pinterest', 'tt_karma_builder'),			                  
			              ),	
			              
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Instagram Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'instagram'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Instagram Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'instagram_title',
			                  'value'     => __('Instagram', 'tt_karma_builder'),			                  
			              ),	
			              
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('FourSquare Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'foursquare'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('FourSquare Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'foursquare_title',
			                  'value'     => __('FourSquare', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Delicious Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'delicious'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Delicious Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'delicious_title',
			                  'value'     => __('Delicious', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Digg Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'digg'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Digg Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'digg_title',
			                  'value'     => __('Digg', 'tt_karma_builder'),			                  
			              ),				              			              				              		              
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Google + Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'google'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Google + Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'google_title',
			                  'value'     => __('Google +', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Dribbble Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'dribbble'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Dribbble Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'dribbble_title',
			                  'value'     => __('Dribbble', 'tt_karma_builder'),			                  
			              ),	
			              
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Skype Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'skype'
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Skype Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'skype_title',
			                  'value'     => __('Skype', 'tt_karma_builder'),			                  
			              ),	
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Vkontakte Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'vkontakte'
			              ),			              
			              array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Vkontakte Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'vkontakte_title',
			                  'value'     => __('Vkontakte', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Vimeo Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'vimeo'
			              ),			              
			              array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Vimeo Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'vimeo_title',
			                  'value'     => __('Vimeo', 'tt_karma_builder'),			                  
			              ),
			               array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Soundcloud Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'soundcloud'
			              ),			              
			              array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Soundcloud Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'soundcloude_title',
			                  'value'     => __('Soundcloud', 'tt_karma_builder'),			                  
			              ),
			              array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Odnoklassniki Url', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'odnoklassniki'
			              ),			              
			              array(
							  'group'       => __('Social Accounts', 'tt_karma_builder'),
							  'heading'     => __('Odnoklassniki Title', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'param_name'  => 'odnoklassniki_title',
			                  'value'     => __('Odnoklassniki', 'tt_karma_builder'),			                  
			              ),				              				              
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
		)
	) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Tab 1
--------------------------------------------------------------*/
   vc_map( array(
        'name'                    => __("Tabs 1", 'tt_karma_builder'),
        'description'             => __("Tabbed content", 'tt_karma_builder'),
        'base'                    => "karma_builder_tab_1",
        'controls'                => 'full',
        'content_element'         => true,
        'show_settings_on_create' => true,
		'js_view'                 => 'VcColumnView',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-tabs-1.png', __FILE__),
        'category'                => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"               => array('only' => 'karma_builder_tab_1_content'),
        "params"      => array(
        				  array(
								'type'       => 'karma_builder_note',
								'param_name' => 'karma_builder_note',
								'value'      => 'Customize the design of this Tab Section using the options below. Upon completion simply click "Save changes" to begin adding Tabs.'
								),
					      array(
			               	  'heading'    => __("Color scheme", 'tt_karma_builder'),
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'param_name' => "color_scheme",
			                  'value' => array(
											'Alpha Green'    => 'alphagreen',
											'Autumn'         => 'autumn',
											'Black'          => 'black',
											'Blue Grey'      => 'bluegrey',
											'Buoy Red'       => 'buoyred',
											'Cherry'         => 'cherry',											
											'Coffee'         => 'coffee',											
											'Cool Blue'      => 'coolblue',											
											'Fire'           => 'fire',											
											'Forest Green'   => 'forestgreen',
											'French Green'   => 'frenchgreen',										
											'Golden'         => 'golden',											
											'Grey'           => 'grey',	
											'Lime Green'     => 'limegreen',
											'Orange'         => 'orange',											
											'Periwinkle'     => 'periwinkle',											
											'Pink'           => 'pink',
											'Political Blue' => 'politicalblue',										
											'Purple'         => 'purple',											
											'Royal Blue'     => 'royalblue',
											'Saffron Blue'   => 'saffronblue',					
											'Silver'         => 'silver',											
											'Sky Blue'       => 'skyblue',
											'Steel Green'    => 'steelgreen',	
											'Teal'           => 'teal',											
											'Teal Grey'      => 'tealgrey',
											'Tuf Green'      => 'tufgreen',										
											'Violet'         => 'violet',										
											'Vista Blue'     => 'vistablue',											
											'Yogi Green'     => 'yogigreen',
									),
			                  'description' => __('<a href="http://s3.truethemes.net/plugin-assets/karma-builder/color-tabs.png" target="_blank">View available color schemes &rarr;</a>', 'tt_karma_builder')
			              ),
			              	array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

// Map the tab content
   vc_map( array(
   		'category'                => __('Karma Builder', 'tt_karma_builder'),
        'name'                    => __("Tab Section", 'tt_karma_builder'),
        'description'             => __("Add a tab section", 'tt_karma_builder'),
        'base'                    => "karma_builder_tab_1_content",
        'content_element'         => true,
        'show_settings_on_create' => true,
        'controls'                => 'full',
        'icon'        			  => plugins_url('images/backend-editor/truethemes-menu-tabs-1.png', __FILE__),
        "as_child"                => array('only' => 'karma_builder_tab_1'), 
        "params"                  => array(
        					array(
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Active Tab?", 'tt_karma_builder'),
					            'param_name' => "tab_active",
					            'value'      => array(
											'No'  => 'no',
											'Yes' => 'yes',
											),
					            'description' => __("Should this tab be opened by default? (only one active tab per section)", 'tt_karma_builder')
					        ),						
					        array(
					            'type'        => 'textfield',
					            'heading'     => __("Tab Title", 'tt_karma_builder'),
					            'param_name'  => "nav_tab_title",
					            'value'       => 'New Tab'
					        ),
				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Tab Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'         => __("<h2>Heading</h2><p>Edit this text with custom content.</p>", 'tt_karma_builder')
			              ),
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
 							array(
 								'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),
			               array(
			               	  'group'       => __('Icon', 'tt_karma_builder'),
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),														              								        								        
					    ),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Tab 2
--------------------------------------------------------------*/
   vc_map( array(
   		'category'                => __('Karma Builder', 'tt_karma_builder'),
        'name'                    => __("Tabs 2", 'tt_karma_builder'),
        'description'             => __("Tabbed content", 'tt_karma_builder'),
        'base'                    => "karma_builder_tab_2",
        'controls'                => 'full',
        'content_element'         => true,
        'show_settings_on_create' => true,
		'js_view'                 => 'VcColumnView',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-tabs-2.png', __FILE__),
        "as_parent"               => array('only' => 'karma_builder_tab_2_content'),
        "params"      => array(
        
        					array(
								'type'       => 'karma_builder_note',
								'param_name' => 'karma_builder_note',
								'value'      => 'Customize the design of this Tab Section using the options below. Upon completion simply click "Save changes" to begin adding Tabs.'
								), 
 
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),


							array(
								'type'          => 'colorpicker',
								'heading'    => __('Active Tab Link Color', 'tt_karma_builder'),
								'param_name'    => "color_scheme",
								'value'         => "#3b86c4",
								'description'   => __('The font color of the active Tab in this set.', 'tt_karma_builder')
					        ),
			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

// Map the tab content
   vc_map( array(
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        'name'            => __("Tab Section", 'tt_karma_builder'),
        'description'     => __("Add a tab section", 'tt_karma_builder'),
        'base'            => "karma_builder_tab_2_content",
        'controls'        => 'full',
        'content_element' => true,
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-tabs-2.png', __FILE__),
        "as_child"        => array('only' => 'karma_builder_tab_2'),
        'show_settings_on_create' => true,
        "params"      => array(
        					array(
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Active Tab?", 'tt_karma_builder'),
					            'param_name' => "tab_active",
					            'value'      => array(
											'No'  => 'no',
											'Yes' => 'yes',
											),
					            'description' => __("Should this tab be opened by default? (only one active tab per section)", 'tt_karma_builder')
					        ),
					        array(
					            'type'        => 'textfield',
					            'heading'     => __("Tab Title", 'tt_karma_builder'),
					            'param_name'  => "nav_tab_title",
					            'value'       => 'New Tab'
					        ),
				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Tab Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'         => __("<h2>Heading</h2><p>Edit this text with custom content.</p>", 'tt_karma_builder')
			              ),								        								        
					    ),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Orbit - Tab 3
--------------------------------------------------------------*/
   vc_map( array(
        'name'                    => __("Tabs 3", 'tt_karma_builder'),
        'description'             => __("Tabbed content (vertical)", 'tt_karma_builder'),
        'base'                    => "karma_builder_tab_3",
        'controls'                => 'full',
        'content_element'         => true,
        'show_settings_on_create' => true,
		'js_view'                 => 'VcColumnView',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-tabs-3.png', __FILE__),
        'category'                => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"               => array('only' => 'karma_builder_tab_3_content'),
        "params"      => array(
            				array(
		
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
								'group'      => __('Design', 'tt_karma_builder'),
								'type'       => 'karma_builder_note',
								'param_name' => 'karma_builder_note',
								'value'      => 'Customize the design of this Tab Section using the options below. Upon completion simply click "Save changes" to begin adding Tabs.'
								),
        					array(
								'group'      => __('Design', 'tt_karma_builder'),
								'type'       => 'checkbox',
								'heading'    => __( 'Disable Icons', 'tt_karma_builder' ),
								'param_name' => 'disable_icon',
								'value'      => array( __( 'Check this box to disable icons.', 'tt_karma_builder' ) => 'yes' )
								),
        					array(
					        	'group'       	=> __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'    	=> __('Menu Background Color', 'tt_karma_builder'),
								'param_name'    => "menu_bg_color",
								'value'         => "#f6f6f6",
								'description'   => __('The background color of the menu.', 'tt_karma_builder')
					        ),
        					array(
        						'group'       	=> __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'    	=> __('Link Color - Normal', 'tt_karma_builder'),
								'param_name'    => "link_color",
								'value'         => "#666",
								'description'   => __('The link color when a tab is not active.', 'tt_karma_builder')
					        ),
					        array(
					    		'group'       	=> __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'    	=> __('Link Color - Hover', 'tt_karma_builder'),
								'param_name'    => "link_color_hover",
								'value'         => "#333",
								'description'   => __('The link color on hover.', 'tt_karma_builder')
					        ),
					        array(
					    		'group'       	=> __('Design', 'tt_karma_builder'),
								'type'       	=> 'colorpicker',
								'heading'    	=> __('Link Color - Active', 'tt_karma_builder'),
								'param_name'    => "link_color_active",
								'value'         => "#099",
								'description'   => __('The link color when a tab is active.', 'tt_karma_builder')
					        ),
					    	array(
					    		'group'       	=> __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'    	=> __('Tab Background Color - Hover', 'tt_karma_builder'),
								'param_name'    => "tab_bgcolor_hover",
								'value'         => "#E8E8E8",
								'description'   => __('The background color of a tab on hover.', 'tt_karma_builder')
					        ),
					        array(
					    		'group'       	=> __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'    	=> __('Tab Background Color - Active', 'tt_karma_builder'),
								'param_name'    => "tab_bgcolor_active",
								'value'         => "#fff",
								'description'   => __('The background color of a tab when active.', 'tt_karma_builder')
					        ),
					        array(
					    		'group'       	=> __('Design', 'tt_karma_builder'),
								'type'          => 'colorpicker',
								'heading'    	=> __('Tab - Bottom border', 'tt_karma_builder'),
								'param_name'    => "tab_border_color",
								'value'         => "#e1e1e1",
								'description'   => __('The 1px line that separates each tab.', 'tt_karma_builder')
					        ),
					        array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),				        					        								
					    ),
     	   		) 
);// END vc_map

// Map the tab content
   vc_map( array(
   		'category'                => __('Karma Builder', 'tt_karma_builder'),
        'name'                    => __("Tab Section", 'tt_karma_builder'),
        'description'             => __("Add a tab section", 'tt_karma_builder'),
        'base'                    => "karma_builder_tab_3_content",
        'content_element'         => true,
        'show_settings_on_create' => true,
        'controls'                => 'full',
        'icon'                    => plugins_url('images/backend-editor/truethemes-menu-tabs-3.png', __FILE__),
        "as_child"                => array('only' => 'karma_builder_tab_3'), 
        "params"                  => array(
        					array(
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Active Tab?", 'tt_karma_builder'),
					            'param_name' => "tab_active",
					            'value'      => array(
											'No'  => 'no',
											'Yes' => 'yes',
											),
					            'description' => __("Should this tab be opened by default? (only one active tab per section)", 'tt_karma_builder')
					        ),							
					        array(
					            'type'        => 'textfield',
					            'heading'     => __("Tab Title", 'tt_karma_builder'),
					            'param_name'  => "nav_tab_title",
					            'value'       => 'New Tab'
					        ),
				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Tab Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
				    		 'value'         => __("<h2>Heading</h2><p>Edit this text with custom content.</p>", 'tt_karma_builder')
			              ),
		        		//START icons
		        		array(
							'group'   => __('Icon', 'tt_karma_builder'),		        		
							'type'    => 'dropdown',
							'heading' => __( 'Icon library', 'tt_karma_builder' ),
							'value'   => array(
									__( 'Font Awesome', 'tt_karma_builder' )        => 'fontawesome',
									__( 'Open Iconic', 'tt_karma_builder' )         => 'openiconic',
									__( 'Typicons', 'tt_karma_builder' )            => 'typicons',
									__( 'Entypo', 'tt_karma_builder' )              => 'entypo',
									__( 'Linecons', 'tt_karma_builder' )            => 'linecons',
									__( 'Do not display icon', 'tt_karma_builder' ) => '',									
								),
							'admin_label' => true,
							'param_name'  => 'type',
							'description' => __( 'Select icon library.', 'tt_karma_builder' ),
							'std'         => array(__( 'Font Awesome', 'tt_karma_builder' ) => 'fontawesome'),
						),
						//fontawesome
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_fontawesome',
							'value'        => 'fa fa-adjust',
							'settings'     => array(
							'emptyIcon'    => false,
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'fontawesome',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//openiconic
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_openiconic',
							'value'        => 'vc-oi vc-oi-dial',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'openiconic',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'openiconic',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//typicons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_typicons',
							'value'        => 'typcn typcn-adjust-brightness',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'typicons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'typicons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//entypo
						array(
							'group'        => __('Icon', 'tt_karma_builder'),					
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_entypo',
							'value'        => 'entypo-icon entypo-icon-note',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'entypo',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'entypo',
							),
						),
						//linecons
						array(
							'group'        => __('Icon', 'tt_karma_builder'),
							'type'         => 'iconpicker',
							'heading'      => __( 'Icon', 'tt_karma_builder' ),
							'param_name'   => 'icon_linecons',
							'value'        => 'vc_li vc_li-heart',
							'settings'     => array(
							'emptyIcon'    => false,
							'type'         => 'linecons',
							'iconsPerPage' => 4000,
							),
							'dependency'   => array(
							'element'      => 'type',
							'value'        => 'linecons',
							),
							'description'  => __( 'Select icon from library.', 'tt_karma_builder' ),
						),
						//END icons
 							array(
 								'group'       => __('Icon', 'tt_karma_builder'),
								'type'        => 'textarea_raw_html',
								'heading'     => __('Custom Icon', 'tt_karma_builder'),
								'description' => __('Display your own custom icon by entering it\'s HTML code here. Give this HTML element an additional CSS class name of "karma-custom-icon" for proper positioning.', 'tt_karma_builder'),
								'param_name'  => 'custom_icon',
								'value'       => '',
							),
			               array(
			               	  'group'       => __('Icon', 'tt_karma_builder'),
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Custom Icon Upload", 'tt_karma_builder'),
			                  'param_name'    => "custom_icon_upload",
			                  'description'   => __('Upload a custom icon, this overwrites the Custom Icon and Icon settings.', 'tt_karma_builder')
			              ),														              								        								        
					    ),
     	   		) 
);// END vc_map

/*--------------------------------------------------------------
Karma - Tabs 4
--------------------------------------------------------------*/
//tabs from karma theme
   vc_map( array(
        'name'            => __("Tabs 4", 'tt_karma_builder'),
        'description'     => __("Karma Theme Tabs", 'tt_karma_builder'),
        'base'            => "tabset",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-tabs-2.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only' => 'tab'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Tab Section Titles', 'tt_karma_builder'),
			                  'param_name'  => 'tab_labels',
			                  'description' => __('Enter the name of the Tab Sections separated by comma<br>For example: Tab 1, Tab 2, Tab 3, Tab 4', 'tt_karma_builder'),
			              ),
					    ),
     	   		) 
);// END vc_map

//tab content from karma theme
   vc_map( array(
        'name'            => __("Tab Content", 'tt_karma_builder'),
        'description'     => __("Add a tab", 'tt_karma_builder'),
        'base'            => "tab",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-tabs-2.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'tabset'),
        "params"      => array(
 				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',	    		 
			              ),
					    ),
     	   		)
);// END vc_map

/*--------------------------------------------------------------
Karma - Team Member
--------------------------------------------------------------*/
   vc_map( array(
        'name'            => __("Team Member", 'tt_karma_builder'),
        'description'     => __("Team member with photo", 'tt_karma_builder'),
        'base'            => "team_member",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-team-member.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
            			   array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

 				            array(
				    		 'type'          => "textfield",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Name', 'tt_karma_builder'),
				    		 'param_name'    => 'members_name',
			                 'description' => __('Enter the name of the team member', 'tt_karma_builder')				    		 
			              ),	
 				            array(
				    		 'type'          => "textfield",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Title', 'tt_karma_builder'),
				    		 'param_name'    => 'members_title',
			                 'description' => __('Enter the title of the team member', 'tt_karma_builder')				    		 
			              ),
 				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Description', 'tt_karma_builder'),
				    		 'param_name'    => 'content',
			                 'description' => __('Enter the description for this team member', 'tt_karma_builder')				    		 
			              ),
 				            array(
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Last Team Member?", 'tt_karma_builder'),
					            'param_name' => "last_item",
					            'value'      => array(
											'No'  => 'false',
											'Yes' => 'true',															
											),
					            'description' => __("Is this the last team member on the page?", 'tt_karma_builder')
					        ),
        					array(
        						'group'       => __('Photo', 'tt_karma_builder'),
					            'type'       => 'dropdown',
					            'save_always' => true,
					            'heading'    => __("Style", 'tt_karma_builder'),
					            'param_name' => "style",
					            'value'      => array(
											'Modern'  => 'modern',
											'Shadow' => 'shadow',															
											),
					            'description' => __("Select the style of image frame", 'tt_karma_builder')
					        ),
			               array(
			               	  'group'       => __('Photo', 'tt_karma_builder'),
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Image", 'tt_karma_builder'),
			                  'param_name'    => "local_uploaded_image_id",
			                  'description'   => __('Select or Upload Image', 'tt_karma_builder')
			              ),
			              array(
			              	  'group'       => __('Photo', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("Image ALT", 'tt_karma_builder'),
			                  'param_name'  => "image_alt",
			                  'description' => __('Enter your image alt text.', 'tt_karma_builder')
			              ),			              
			              array(
			              	  'group'       => __('Photo', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __("External Image (URL)", 'tt_karma_builder'),
			                  'param_name'  => "external_image_url",
			                  'description' => __('If your image is not uploaded to this site, you can enter the image url here. This will overwrite the above Image option.', 'tt_karma_builder')
			              ),		
							array(
							  'group'       => __('Photo', 'tt_karma_builder'),
			                  'type'        => 'vc_link',
			                  'holder'      => 'div',
			                  //'heading'   => ** this is empty for cleaner user-interface
			                  'param_name'  => 'url',
			                  'description' => __('Click "Select URL" to link the photo. (optional)', 'tt_karma_builder')
			              ),				              		              			        	              			
     	   		)
     	 )
);// END vc_map

/*--------------------------------------------------------------
Orbit - Testimonial 1
--------------------------------------------------------------*/
   vc_map( array(
   		'category'        => __('Karma Builder', 'tt_karma_builder'),
   		'name'            => __("Testimonial 1", 'tt_karma_builder'),
   		'description'     => __("Stylish testimonial slider", 'tt_karma_builder'),
   		'base'            => "karma_builder_testimonial_1",
   		'controls'        => 'full',
   		'content_element' => true,
   		'show_settings_on_create' => true,
   		'js_view'         => 'VcColumnView',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-testimonial-1.png', __FILE__),
        "as_parent"   => array('only' => 'karma_builder_testimonial_1_slide'), 
        "params"      => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

// Heres the testimonial content slide
   vc_map( array(
        'name'            => __("Testimonial Slide", 'tt_karma_builder'),
        'description'     => __("Add a testimonial", 'tt_karma_builder'),
        'base'            => "karma_builder_testimonial_1_slide",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-testimonial-1.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'karma_builder_testimonial_1'),
        "params"      => array(
			               array(
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Banner Image", 'tt_karma_builder'),
			                  'param_name'    => "banner_image_attachment_id",
			                  'description'   => __('Upload a banner image.', 'tt_karma_builder')
			              ),
			               array(
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Customer Photo", 'tt_karma_builder'),
			                  'param_name'    => "client_headshot_image_attachment_id",
			                  'description'   => __('Upload a photo of the customer.', 'tt_karma_builder')
			              ),
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Client Name", 'tt_karma_builder'),
					           'param_name'    => "client_name"
					       ),
					       array(
					           'type'          => "textarea",
					           'heading'       => __("Testimonial Text", 'tt_karma_builder'),
					           'param_name'    => "testimonial_text"
					       ),							       
					       						              						              
					    ),
     	   		) 
	);// END vc_map

/*--------------------------------------------------------------
Orbit - Testimonial 2
--------------------------------------------------------------*/
   vc_map( array(
        'name'            => __("Testimonial 2", 'tt_karma_builder'),
        'description'     => __("Stylish testimonial slider", 'tt_karma_builder'),
        'base'            => "karma_builder_testimonial_2",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-testimonial-2.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only' => 'karma_builder_testimonial_2_slide'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
            				array(
					      		'group'         => __('General', 'tt_karma_builder'),
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

        					array(
        					  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'heading'       => __("Slide BG Color", 'tt_karma_builder'),
			                  'param_name'    => "testimonial_bg_color",
			                  'value'         => "#e7e9e6",
			                  'description' => __('Background color of the testimonial slides.', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'colorpicker',
			                  'holder'        => 'div',
			                  'heading'       => __("Text Color", 'tt_karma_builder'),
			                  'param_name'    => "testimonial_text_color",
			                  'value'         => "#444444",
			                  'description' => __('Color of the text.', 'tt_karma_builder')
			              ),
			               array(
			               	  'group'         => __('Design', 'tt_karma_builder'),
			                  'type'          => 'textfield',
			                  'holder'        => 'div',
			                  'heading'       => __("Text Size", 'tt_karma_builder'),
			                  'param_name'    => "testimonial_text_size",
			                  'value'         => "13px",
			                  'description' => __('Size of the text.', 'tt_karma_builder')
			              ),
			               array(
			              	  'group'         => __('Design', 'tt_karma_builder'),
			              	  'heading'    	  => __('Design skin', 'tt_karma_builder'),
			                  'type'          => 'dropdown',
			                  'save_always' => true,
			                  'holder'        => 'div',
			                  'param_name'    => 'controls_style',
			                  'value' 		  => array(
			                  						'Dark'  => 'true-controls-dark',
			                  						'Light' => 'true-controls-light'),
			                  'description' => __('Choose a skin for the next/previous arrows.', 'tt_karma_builder')
			              ),
			              	array(
			              	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

// Heres the testimonial content slide
   vc_map( array(
        'name'            => __("Testimonial Slide", 'tt_karma_builder'),
        'description'     => __("Add a testimonial", 'tt_karma_builder'),
        'base'            => "karma_builder_testimonial_2_slide",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-testimonial-2.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'karma_builder_testimonial_2'),
        "params"      => array(
			               array(
			                  'type'          => "attach_image",
			                  'holder'        => 'div',
			                  'heading'       => __("Customer Photo", 'tt_karma_builder'),
			                  'param_name'    => "client_headshot_image_attachment_id",
			                  'description'   => __('Upload a photo of the customer.', 'tt_karma_builder')
			              ),
					       array(
					           'type'          => "textarea",
					           'heading'       => __("Content", 'tt_karma_builder'),
					           'param_name'    => "testimonial_text"
					       ),
					    ),
     	   		)
);// END vc_map

/*--------------------------------------------------------------
Karma - Testimonial 3
--------------------------------------------------------------*/
//testimonial from karma theme
   vc_map( array(
        'name'            => __("Testimonial 3", 'tt_karma_builder'),
        'description'     => __("Karma Theme Testimonial Slider", 'tt_karma_builder'),
        'base'            => "testimonial_wrap",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-testimonial-2.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only' => 'testimonial'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

// Heres the testimonial content slide
vc_map( array(
        'name'            => __("Testimonial", 'tt_karma_builder'),
        'description'     => __("Add a testimonial", 'tt_karma_builder'),
        'base'            => "testimonial",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => plugins_url('images/backend-editor/truethemes-menu-testimonial-2.png', __FILE__),
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'testimonial_wrap'),
        "params"      => array(
 				            array(
				    		 'type'          => "textarea_html",
				    		 'holder'        => 'div',
				    		 'heading'       => __('Content', 'tt_karma_builder'),
				    		 'param_name'    => 'content',	    		 
			              ),
			             array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('Customer Name', 'tt_karma_builder'),
			                  'param_name'  => 'client_name',
			                  'description' => __('Enter the name of the customer who gave this testimonial (optional)', 'tt_karma_builder'),
			              ),			              
					    ),
     	   		)
);// END vc_map

/*--------------------------------------------------------------
Karma - Text Callout
--------------------------------------------------------------*/
//new shortcode to combine "Callout 1 + 2"
vc_map( array(
		'category'    => __('Karma Builder', 'tt_karma_builder'),
        'name'        => __("Text Callout", 'tt_karma_builder'),
        'description' => __("Large text great for callouts", 'tt_karma_builder'),
        'base'        => "karma_callout_text",
        'controls'    => 'full',
        'icon'        => plugins_url('images/backend-editor/truethemes-menu-text-callout.png', __FILE__),
        "params"      => array(
            				array(
								'type'          => 'dropdown',
								'heading'       => __("Animation", 'tt_karma_builder'),
								'param_name'    => "animate",
								'value'         => array(
								    'no animation'       => 'animate_none',
									'fly-in from center' => 'in_from_center',
									'fly-in from top'    => 'in_from_top',
									'fly-in from right'  => 'in_from_right',
									'fly-in from bottom' => 'in_from_bottom',
									'fly-in from left'   => 'in_from_left',
									
								),
								'save_always' => true,
					        ),

			               array(
			                  'type'       => 'dropdown',
			                  'save_always' => true,
			                  'holder'     => 'div',
			                  'heading'    => __("Style", 'tt_karma_builder'),
			                  'param_name' => "style",
			                  'value'      => array(
											'Style 1 - large text with separator lines' => 'karma_callout_1',
											'Style 2 - large text no separator lines'   => 'karma_callout_2',
											)
			              ),
			               array(
					           'type'          => "textarea_html",
					           'heading'       => __("Callout Text", 'tt_karma_builder'),
					           'param_name'    => "content"
					       ),
			               array(
			               	  'group'       => __('CSS Class', 'tt_karma_builder'),
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
			          )
			) 
);// END vc_map



/**
 * Disable vc_map()
 *
 * These items are left for backward compatibility
 * but will not be displayed as visual-composer elements
 *
 * @since Karma Builder 1.0
 
//horizontal shadow shortcode from karma theme
   vc_map( array(
        'name'            => __("Horizontal Shadow", 'tt_karma_builder'),
        'description'     => __("", 'tt_karma_builder'),
        'base'            => "hr_shadow",
        'controls'        => array(),
        'content_element' => true,
        'icon'            => '',//remember to set this later
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        'show_settings_on_create' => false,
     	 )
);// END vc_map

//horizontal line shortcode from karma theme
   vc_map( array(
        'name'            => __("Horizontal Line", 'tt_karma_builder'),
        'description'     => __("", 'tt_karma_builder'),
        'base'            => "hr",
        'controls'        => array(),
        'content_element' => true,
        'icon'            => '',//remember to set this later
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        'show_settings_on_create' => false,
     	 )
);// END vc_map



//callout1 shortcode from karma theme
   vc_map( array(
        'name'            => __("Callout 1", 'tt_karma_builder'),
        'description'     => __("Typography", 'tt_karma_builder'),
        'base'            => "callout1",
        'controls'        => array(),
        'content_element' => true,
        'icon'            => '',//remember to set this later
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        'show_settings_on_create' => true,
        "params"          => array(
			               array(
							  'heading'     => __('Text', 'tt_karma_builder'),
			                  'type'        => 'textarea_html',
			                  'holder'      => 'div',
			                  'param_name'  => 'content',
			                  'description'     => __('Enter the text to be display in callout', 'tt_karma_builder'),
			              ),			              
					    ),       
        
     	 )
     	 
);// END vc_map

//callout2 shortcode from karma theme
   vc_map( array(
        'name'            => __("Callout 2", 'tt_karma_builder'),
        'description'     => __("Typography", 'tt_karma_builder'),
        'base'            => "callout2",
        'controls'        => array(),
        'content_element' => true,
        'icon'            => '',//remember to set this later
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        'show_settings_on_create' => true,
        "params"          => array(
			               array(
							  'heading'     => __('Text', 'tt_karma_builder'),
			                  'type'        => 'textarea_html',
			                  'holder'      => 'div',
			                  'param_name'  => 'content',
			                  'description'     => __('Enter the text to be display in callout', 'tt_karma_builder'),
			              ),			              
					    ),       
        
     	 )
     	 
);// END vc_map
 

//video left
   vc_map( array(
        'name'            => __("Video Left", 'tt_karma_builder'),
        'description'     => __("", 'tt_karma_builder'),
        'base'            => "video_left",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => '',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only'=>'video_frame,video_text'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

vc_map( array(
        'name'            => __("Video Right", 'tt_karma_builder'),
        'description'     => __("", 'tt_karma_builder'),
        'base'            => "video_right",
        'controls'        => 'full',
        'content_element' => true,
        'show_settings_on_create' => true,
        'icon'            => '',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_parent"       => array('only'=>'video_frame,video_text'),
        'js_view'         => 'VcColumnView',
        "params"          => array(
			              	array(
			                  'type'        => 'textfield',
			                  'holder'      => 'div',
			                  'heading'     => __('CSS class name', 'tt_karma_builder'),
			                  'param_name'  => 'custom_css_class',
			                  'description' => __('Give this element an extra CSS class name if you wish to refer to it in a CSS file. (optional)', 'tt_karma_builder')
			              ),
					    ),
     	   		) 
);// END vc_map

vc_map( array(
        'name'            => __("Video Frame", 'tt_karma_builder'),
        'description'     => __("Add a Video", 'tt_karma_builder'),
        'base'            => "video_frame",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => '',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'video_left,video_right'),
        "params"      => array(
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Iframe url", 'tt_karma_builder'),
					           'param_name'    => "iframe_url",
					           'description' => __("Enter iframe url, for example. http://www.youtube.com/embed/rV6wRZRDci0 ", 'tt_karma_builder'),
					       ),
					    ),
     	   		)
);// END vc_map

vc_map( array(
        'name'            => __("Video Text", 'tt_karma_builder'),
        'description'     => __("Add a Video Text", 'tt_karma_builder'),
        'base'            => "video_text",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => '',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "as_child"        => array('only' => 'video_left,video_right'),
        "params"      => array(
					       array(
					           'type'          => "textarea_html",
					           'heading'       => __("Video Text", 'tt_karma_builder'),
					           'param_name'    => "content",
					           'description' => __("Enter some text description for the video", 'tt_karma_builder'),
					       ),
					    ),
     	   		)
);// END vc_map

//iframe
   vc_map( array(
        'name'            => __("iFrame", 'tt_karma_builder'),
        'description'     => __("Add a iFrame", 'tt_karma_builder'),
        'base'            => "iframe",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => '',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Url", 'tt_karma_builder'),
					           'param_name'    => "url",
					           'description' => __("Enter iframe url", 'tt_karma_builder'),
					       ),
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Width", 'tt_karma_builder'),
					           'param_name'    => "width",
					           'description' => __("Enter the width", 'tt_karma_builder'),
					       ),	
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Height", 'tt_karma_builder'),
					           'param_name'    => "height",
					           'description' => __("Enter the height", 'tt_karma_builder'),
					       ),					       				       
					    ),
     	   		)
);// END vc_map

//related posts
   vc_map( array(
        'name'            => __("Related Posts", 'tt_karma_builder'),
        'description'     => __("", 'tt_karma_builder'),
        'base'            => "related_posts",
        'controls'        => 'full',
        'content_element' => true,
        'icon'            => '',
        'category'        => __('Karma Builder', 'tt_karma_builder'),
        "params"      => array(
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Title", 'tt_karma_builder'),
					           'param_name'    => "title",
					           'description' => __("Enter a title for this shortcode", 'tt_karma_builder'),
					       ),        
					       array(
					           'type'          => "textfield",
					           'heading'       => __("Post ID", 'tt_karma_builder'),
					           'param_name'    => "post_id",
					           'description' => __("Enter ID of the post that you want to show posts related to it.", 'tt_karma_builder'),
					       ),
					       array(
					           'type'          => "textfield",
					           'heading'       => __("No of posts", 'tt_karma_builder'),
					           'param_name'    => "limit",
					           'description' => __("Enter the number of related posts to show", 'tt_karma_builder'),
					           'value' => __("3", 'tt_karma_builder'),
					       ),
					       array(
					           'type'          => "dropdown",
					           'heading'       => __("Target", 'tt_karma_builder'),
					           'param_name'    => "target",
					           'description' => __("Open link in new or current tab.", 'tt_karma_builder'),
					            'value'      => array(
											'_self'  => '_self',
											'_blank' => '_blank',	
  								),
					       ),
					       array(
					           'type'          => "dropdown",
					           'heading'       => __("Style", 'tt_karma_builder'),
					           'param_name'    => "style",
					           'description' => __("Alternative Style is similar to related posts found at end of single.php", 'tt_karma_builder'),
					            'value'      => array(
											'Default'  => 'one',
											'Alternative' => 'two',	
  								),
					       ),						       					       				       				       
					    ),
     	   		)
);// END vc_map
*/



}//END integrateWithVC()


/**
 * Build the Shortcodes
 *
 * Lets build out the custom shortcodes
 * This is the back-end stuff the controls how the shortcode
 * will be displayed on the front-end.
 *
 * Note: some of the shortcodes are pulled from our Vision Plugin and Orbit Plugin
 * which you'll find reflected in CSS .class and #ID names
 *
 * @since Karma Builder 1.0
 */

/*--------------------------------------------------------------
Orbit - Accordion
--------------------------------------------------------------*/
public function render_karma_builder_accordion( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'gradient_top'       => '#ffffff',
	'gradient_bottom'    => '#efefef',
	'panel_border'       => '#e1e1e1',
	'panel_padding'      => '20px',
	'title_color'        => '#666666',
	'title_color_active' => '',
	'custom_css_class'   => '',
	'unique_id'          => '',
	'animate'			=> '',
	), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Dynamic CSS Function
	$style_code ='.true-accordion.'.$unique_id.' dt {
	border: 1px solid '.$panel_border.';
	background-image: -webkit-linear-gradient(top, '.$gradient_top.' 0%, '.$gradient_bottom.' 100%);
	background-image: -moz-linear-gradient(top, '.$gradient_top.' 0%, '.$gradient_bottom.' 100%);
	background-image: -o-linear-gradient(top, '.$gradient_top.' 0%, '.$gradient_bottom.' 100%);
	background-image: linear-gradient(top, '.$gradient_top.' 0%, '.$gradient_bottom.' 100%);
	padding: '.$panel_padding.' 0;
	}
	.true-accordion.'.$unique_id.' dt,
	.true-accordion.'.$unique_id.' dt:before {
	    color: '.$title_color.';
	}
	.true-accordion.'.$unique_id.' dt.current,
	.true-accordion.'.$unique_id.' dt.current:before {
	    color: '.$title_color_active.';
	}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

   return '<dl class="true-accordion '.$custom_css_class.' '.$unique_id.' tt_'.$animate.'">'.$content.'</dl>';
}// END shortcode

/*--------------------------------------------------------------
Orbit - Accordion [slide]
--------------------------------------------------------------*/
public function render_karma_builder_accordion_panel($atts, $content = null) {
	extract(shortcode_atts(array(
	'title'        => '',
	'panel_active' => 'false'
	), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	if ($panel_active == 'true'){
	$output = '<dt class="current">'.$title.'</dt><dd class="current">'.$content.'</dd>';
	} else {
	$output = '<dt>'.$title.'</dt><dd>'.$content.'</dd>';
	}

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Alert
--------------------------------------------------------------*/ 
public function render_karma_builder_alert($atts, $content = null){
	extract(shortcode_atts(array(
	'style'     => '',
	'font_size' => '13px',
	'closeable' => '',
	'custom_css_class'   => '',
	'animate' => ''
	), $atts));
	
	//map existing karma theme notify box style to alert box style.
	switch($style){
		case 'green': $style = 'success';
	break;
		case 'red': $style = 'error';
	break;
		case 'blue': $style = 'tip';
	break;
		case 'yellow': $style = 'warning';
	break;
	}
	  
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	  
   if ($closeable == 'true'){
   	$output = '<div class="true-notification tt_'.$animate.' '.$custom_css_class.' '.$style.' closeable"><div class="closeable-x"><p style="font-size:'.$font_size.';">' .$content. '</p></div></div>';
   } else{
     $output = '<div class="true-notification tt_'.$animate.' '.$custom_css_class.' '.$style.'"><p style="font-size:'.$font_size.';">' .$content. '</p></div>';
   }

return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Content Box
--------------------------------------------------------------*/
public function render_karma_builder_content_box( $atts, $content = null ) {      
	extract(shortcode_atts(array(
		'style'            => '',
		'title'            => 'Content Box',
		'custom_icon'      => '',
		'custom_css_class' => '',
		'custom_icon_upload' => '',
	    'type' => 'fontawesome',
	    'icon_fontawesome' => 'fa fa-adjust',
	    'icon_openiconic' => 'vc-oi vc-oi-dial',
	    'icon_typicons' => 'typcn typcn-adjust-brightness',
	    'icon_entypoicons' => 'entypo-icon entypo-icon-note',
	    'icon_linecons' => 'vc_li vc_li-heart',
	    'icon_entypo' => '',
	    'add_icon'=>'',
	    'animate' => ''
		), $atts));
		
	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );
	
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
	//Build output for icon
	if(!empty($type)){
		$icon_output = '<i class="fa '.esc_attr( ${"icon_" . $type} ).'"></i>';
	}
	
    /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }
    
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }
    
    if($add_icon != 'yes'){
    $icon_output = '';
    }		

	    $output = '<div class="true-contentbox tt_'.$animate.' '.$custom_css_class.'"><div class="true-contentbox-title true-cb-title-'.$style.'">'.$icon_output.' <span>'.$title.'</span></div><div class="true-contentbox-content true-content-style-'.$style.'">'.$content.'</div></div>'; 	 

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Circle Loader
--------------------------------------------------------------*/
public function render_karma_builder_circle_loader($atts, $content = null) {
	extract(shortcode_atts(array(  
	'number'           => '50',
	'number_color'     => '#000',
	'symbol'           => '%',
	'bar_width'        => '10',
	'style'            => 'square',
	'track_color'      => '#E8E8E8',
	'bar_color'        => '#a0dbe1',
	'custom_css_class' => '',
	'unique_id'        => '',
	'animate' => ''

	), $atts));
		  
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	$output = '<div class="true-circle-loader '.$unique_id.' tt_'.$animate.' '.$custom_css_class.'">
      <div class="easyPieChart true-circle-number" data-percent="'.$number.'" data-trackcolor="'.$track_color.'" data-barcolor="'.$bar_color.'" data-linewidth="'.$bar_width.'" data-linecap="'.$style.'">
          <span class="true-circle-number-wrap"><span class="true-circle-number">'.$number.'</span>'.$symbol.'</span>
          <canvas></canvas>
      </div>
      <div class="loader-details">'.$content.'</div></div>';

    // Dynamic CSS Function
	$style_code ='.true-circle-loader.'.$unique_id.' .true-circle-number-wrap {color:'.$number_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
		
	return $output;
 }// END shortcode

/*--------------------------------------------------------------
Orbit - Circle Loader (icon)
--------------------------------------------------------------*/
public function render_karma_builder_circle_loader_icon($atts, $content = null) {
	extract(shortcode_atts(array(  
	'number'           => '50',
	'custom_icon' => '',
	'icon_color'       => '#d3565a',
	'bar_width'        => '10',
	'style'            => 'square',
	'track_color'      => '#E8E8E8',
	'bar_color'        => '#a0dbe1',
	'custom_css_class' => '',
	'unique_id'        => '',
	'custom_icon_upload' => '',
	'type' => 'fontawesome',
	'icon_fontawesome' => 'fa fa-adjust',
	'icon_openiconic' => 'vc-oi vc-oi-dial',
	'icon_typicons' => 'typcn typcn-adjust-brightness',
	'icon_entypoicons' => 'entypo-icon entypo-icon-note',
	'icon_linecons' => 'vc_li vc_li-heart',
	'icon_entypo' => '',
	'animate' => ''	
	), $atts));

	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }

    // Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	//Build output for icon
	if(!empty($type)){
		$icon_output = '<i class="fa '.esc_attr( ${"icon_" . $type} ).'"></i>';
	}	

	 /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }	
	
	$output = '<div class="true-circle-loader-icon '.$custom_css_class.' '.$unique_id.' tt_'.$animate.'"><div class="easyPieChart true-circle-icon" data-percent="'.$number.'" data-trackcolor="'.$track_color.'" data-barcolor="'.$bar_color.'" data-linewidth="'.$bar_width.'" data-linecap="'.$style.'">'.$icon_output.'<canvas></canvas></div> <div class="loader-details">'.$content.'</div></div>';

	// Dynamic CSS Function
	$style_code ='.true-circle-loader-icon.'.$unique_id.' .fa{color:'.$icon_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
	
	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Dropcap
--------------------------------------------------------------*/
public function render_karma_builder_dropcap( $atts, $content = null ) {
	extract(shortcode_atts(array(
	'style'    => '',
	'color'    => '',
	'dropcap'  => 'O',
	'custom_css_class'   => '',
	'animate' => ''
	), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	$output = '<div class="true-dropcap-wrap tt_'.$animate.' '.$custom_css_class.'"><span class="true-dropcap-'.$color.'"><span class="true-dropcap-'.$style.'">' .$dropcap. '</span></span>'.$content.'</div>';

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Feature List
--------------------------------------------------------------*/
public function render_karma_builder_features( $atts, $content = null ) {      
		  extract(shortcode_atts(array(  
		  'icon_color'           => '#d3565a',
		  'icon_color_hover'     => '#ffffff',
		  'bg_color_hover'       => '#a2dce2',
		  'bg_color'             => '#fff',
		  'border_color'         => '#a2dce2',
		  'border_width'         => '2px',
		  'animate'              => '',
		  'url'                  => '',
		  'lightbox_content'     => '',
		  'lightbox_description' => '',
		  'custom_css_class'     => '',
		  'unique_id'            => '',
		  'custom_icon'          => '',
		  'custom_icon_upload'   => '',
	      'type' => 'fontawesome',
		  'icon_fontawesome' => 'fa fa-adjust',
		  'icon_openiconic' => 'vc-oi vc-oi-dial',
		  'icon_typicons' => 'typcn typcn-adjust-brightness',
		  'icon_entypoicons' => 'entypo-icon entypo-icon-note',
		  'icon_linecons' => 'vc_li vc_li-heart',
	      'icon_entypo' => '',		  
		  ), $atts));

	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
	
    $output = '<div class="true-features '.$custom_css_class.' '.$unique_id.' tt_'.$animate.'">';
  
    if(!empty($lightbox_content)){
      $output .= '<a href="'.$lightbox_content.'" data-gal="prettyPhoto" title="'.$lightbox_description.'">';
    } elseif(!empty($a_href)){
      $output .= '<a href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'">';
    }

    /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.1
	 * added custom icon upload @since Orbit 1.2
	 */
	 
	//Build output for icon
	if(!empty($type)){
		$icon_output = '<i class="true-custom-icon fa '.esc_attr( ${"icon_" . $type} ).'"></i>';
	}	 

	 /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }	
		 
	//add icon back to output.
	$output.= $icon_output;
  
    if(!empty($lightbox_content)){
      $output .= '</a>';
    } elseif(!empty($a_href)){
      $output .= '</a>';
    }
  
    $output .= '<div class="true-description">'.$content.'</div></div>';

    // Dynamic CSS Function
	$style_code ='
	.true-features.'.$unique_id.' .true-custom-icon,
	.true-features.'.$unique_id.' .true-karma-custom-icon{color:'.$icon_color.';border: '.$border_width.' solid '.$border_color.';background:'.$bg_color.';}
	.true-features.'.$unique_id.' .true-custom-icon:hover,
	.true-features.'.$unique_id.' .true-karma-custom-icon:hover{color:'.$icon_color_hover.';background-color:'.$bg_color_hover.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
  
    return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Heading
--------------------------------------------------------------*/
public function render_karma_builder_heading($atts, $content = null) {
	extract(shortcode_atts(array(
	'heading_text'           => 'Hello',
	'heading_color'          => '#363636',
	'heading_size'           => '30px',
	'heading_type'           => '',
	'heading_style'          => '',
	'sub_heading_text'       => '',
	'sub_heading_color'      => '#555',
	'sub_heading_size'       => '16px',
	'sub_heading_link_color' => '',
	'custom_css_class'       => '',
	'unique_id'              => '',
	'margin_top'             => '20px',
	'margin_bottom'          => '30px',
	'animate' => ''
	), $atts));

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Ensure HTML tags get closed
	$sub_heading_text = force_balance_tags($sub_heading_text);

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	$output = '<div class="true-heading-wrap '.$unique_id.' tt_'.$animate.' '.$custom_css_class.'"><'.$heading_type.'>'.$heading_text.'</'.$heading_type.'>';

	if(!empty($sub_heading_text)) {
		$output .= '<p>'.$sub_heading_text.'</p>';
	} else {
		$output .= $content;
	}

	$output .= '</div>';

	// Dynamic CSS Function
	$style_code ='.true-heading-wrap.'.$unique_id.' '.$heading_type.' {
		color: '.$heading_color.';font-size: '.$heading_size.';text-transform: '.$heading_style.';}
		.true-heading-wrap.'.$unique_id.' p {
			color: '.$sub_heading_color.';
			font-size: '.$sub_heading_size.';
		}
		.true-heading-wrap.'.$unique_id.' {
			margin: '.$margin_top.' 0 '.$margin_bottom.' 0;
		}
		.true-heading-wrap.'.$unique_id.' a {
			color: '.$sub_heading_link_color.';
		}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Icon Box
--------------------------------------------------------------*/
public function render_karma_builder_icon_box($atts, $content = null) {
	/*
	* Add existing/old Karma Theme tt_vector_box shortcode attributes to $atts for backward compatibility
	* The VC popup will not be printing these old attributes, old attributes will be mapped to new attributes within this function..
	*/
	extract(shortcode_atts(array(
    'size'            => '', //old tt_vector_icon attribute, do not remove, keep for backward compatibility
    'color'           => '',  //old tt_vector_icon attribute, do not remove, keep for backward compatibility
    'link_to_page'    => '',  //old tt_vector_icon attribute, do not remove, keep for backward compatibility
    'target'          => '',  //old tt_vector_icon attribute, do not remove, keep for backward compatibility
    'description'     => '',  //old tt_vector_icon attribute, do not remove, keep for backward compatibility  
    'icon'     => '',  //old tt_vector_icon attribute, do not remove, keep for backward compatibility  

	'type' => 'fontawesome',
	'icon_fontawesome' => 'fa fa-adjust',
	'icon_openiconic' => '',
	'icon_typicons' => '',
	'icon_entypoicons' => '',
	'icon_linecons' => '',
	'icon_entypo' => '',
	'custom_icon' => '',
	'icon_size'            => 'fa-4x',
	'icon_color'           => '#ffffff',
	'icon_bg_color'        => '',
	'box_bg_color'         => '',
	'lightbox_content'     => '',
	'lightbox_description' => '',
	'url'                  => '',
	'custom_css_class'     => '',
	'unique_id'            => '',
	'custom_icon_upload' => '',
	'animate' => ''
	), $atts));
	

	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );
	
    //We start mapping existing/old tt_vector_icon attribute to new attributes.
    if(!empty($size)){
    $icon_size = $size;
    }
    if(!empty($color)){
    $icon_bg_color = $color;
    }
    if(!empty($link_to_page)){
    $link_to_page = urlencode($link_to_page);
    $url = "url:$link_to_page|title:$description|target:$target";
    }    	

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
	
	
	//Build output for icon
	if(!empty($type)){
		$icon_output = '<span class="fa-stack '.$icon_size.'"><i class="fa fa-circle fa-stack-2x"></i><i class="fa '.esc_attr( ${"icon_" . $type} ).' fa-stack-1x fa-inverse"></i></span>';
	}
	
	//backward compatible, do not remove.
	if(!empty($icon)){
		$icon_output = '<span class="fa-stack '.$icon_size.'"><i class="fa fa-circle fa-stack-2x"></i><i class="fa '.esc_attr( $icon ).' fa-stack-1x fa-inverse"></i></span>';
	}		

	 /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }

	if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }

	//build the shortcode
	/* if(!empty($lightbox_content)){
	$output = '<div class="true-icon-box tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'"><a href="'.$lightbox_content.'" data-gal="prettyPhoto" title="'.$lightbox_description.'" class="overlay-link">';
	} elseif(!empty($a_href)){
	$output = '<div class="true-icon-box tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'"><a href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'" class="overlay-link">';
	} else {
	$output = '<div class="true-icon-box tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'">';
	} */

	if(!empty($a_href)){
	$overlay_link = '<a class="overlay-link" href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'"></a>';
	}

	if(!empty($lightbox_content)){
	$overlay_link = '<a class="overlay-link" href="'.$lightbox_content.'" data-gal="prettyPhoto" title="'.$lightbox_description.'"></a>';
	}

	$output .= '<div class="true-icon-box tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'">';
	$output .= $icon_output.$content.$overlay_link;
	$output .= '</div>';

	/* if(!empty($lightbox_content)){
		$output .= '</a></div>';
	} elseif(!empty($a_href)){
		$output .= '</a></div>';
	} else {
		$output .= '</div>'; 
	} */

	// Dynamic CSS Function
	$style_code ='.true-icon-box.'.$unique_id.'{background:'.$box_bg_color.';}
	.true-icon-box.'.$unique_id.' .fa.fa-circle{color:'.$icon_bg_color.';}
	.true-icon-box.'.$unique_id.' .fa-inverse{color:'.$icon_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
//	print_r($output);

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Icon + Text
--------------------------------------------------------------*/
public function render_karma_builder_icon_content( $atts, $content = null ) {      
	extract(shortcode_atts(array(
		'type' => 'fontawesome',
		'icon_fontawesome' => 'fa fa-adjust',
		'icon_openiconic' => '',
		'icon_typicons' => '',
		'icon_entypoicons' => '',
		'icon_linecons' => '',
		'icon_entypo' => '',
		'custom_icon' => '',
		'icon_color'       => '',
		'icon_bg_color'    => '',
		'custom_css_class' => '',
		'unique_id'        => '',
		'custom_icon_upload' => '',
		'paragraph_font_size' => '',
		'animate' => ''
	), $atts));
	 
	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );	 
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();
	
	//Build output for icon
	if(!empty($type)){
		$icon_output = '<span class="fa '.esc_attr( ${"icon_" . $type} ).' true-icon"></span>';
	}	

	 /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }
    
    
	if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }	    
	

	$output  = '<div class="true-icon-wrap tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'">'.$icon_output.'<div class="true-icon-text">'.$content.'</div></div>';

	// Dynamic CSS Function
	$style_code = '.true-icon-wrap.'.$unique_id.' .true-icon {color:'.$icon_color.';background-color:'.$icon_bg_color.';}.true-icon-wrap.'.$unique_id.' .true-icon-text p{font-size:'.$paragraph_font_size.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit Icon (PNG)
--------------------------------------------------------------*/
public function render_karma_builder_icon_png($atts, $content = null) {
	extract(shortcode_atts(array(
	'url'                  => '',
	'icon'                 => '',
	'lightbox_content'     => '',
	'lightbox_description' => '',
	'custom_css_class'   => '',
	'animate' => ''
  ), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
  
	if(!empty($a_href)){
		$output = '<a href="'.$a_href.'" class="true-icon-link tt_'.$animate.' true-icon-png true-'.$icon.'" target="'.$a_target.'" title="'.$a_title.'">'.$content.'</a>';
	}

	if(empty($a_href)){
		$output = '<p class="true-icon-png tt_'.$animate.' true-'.$icon.'">'.$content.'</p>';
	}

	if(!empty($lightbox_content)){
		$output = '<a href="'.$lightbox_content.'" class="true-icon-link tt_'.$animate.' true-icon-png true-'.$icon.'" data-gal="prettyPhoto" title="'.$lightbox_description.'">'.$content.'</a>';
	}

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Image Box 1
--------------------------------------------------------------*/
public function render_karma_builder_imagebox_1( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'attachment_id'    => '',
	'img_border_color' => '#cf6e6e',
	'img_border_width' => '',
	'box_bg_color'     => '',
	'sub_title'        => 'Sub Title',
	'main_title'       => 'Main Title',
	'main_title_color' => '#cf6e6e',
	'url'              => '',
	'image_html'       => '',		 	
	'overlay_link'     => '',
	'lightbox_content'     => '',
	'lightbox_description' => '',
	'custom_css_class'     => '',
	'unique_id'            => '',
	'animate' => ''
	), $atts));
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
	
	// Let WordPress build the whole img tag so it includes ALT and other attributes.
	// Using large size here to prevent server crash from full-size images
	$image_html = wp_get_attachment_image( $attachment_id,'large' );
	
	if(!empty($a_href)){
	$overlay_link = '<a class="overlay-link" href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'"></a>';
	}

	if(!empty($lightbox_content)){
	$overlay_link = '<a class="overlay-link" href="'.$lightbox_content.'" data-gal="prettyPhoto" title="'.$lightbox_description.'"></a>';
	}
	
	$output = '<div class="true-image-box-1 tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'"><div class="true-img-wrap">'.$image_html.'</div><div class="true-text-wrap"><div class="callout-heading-wrap true-montserrat"><h4>'.$sub_title.'</h4><h3>'.$main_title.'</h3></div><div class="callout-details-wrap">'.$content.'</div></div>'.$overlay_link.'</div>';

	// Dynamic CSS Function
	$style_code = '.true-image-box-1.'.$unique_id.' .true-img-wrap {border-bottom: '.$img_border_width.' solid '.$img_border_color.';}
	.true-image-box-1.'.$unique_id.' {background: '.$box_bg_color.';}
	.true-image-box-1.'.$unique_id.' .true-text-wrap .callout-heading-wrap h3 {color: '.$main_title_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Image Box 2
--------------------------------------------------------------*/
public function render_karma_builder_imagebox_2( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'attachment_id'        => '',
	'icon'                 => '',
	'box_bg_color'         => '',
	'icon_bg_color'        => '',
	'icon_color'           => '',
	'link_color'           => '',
	'link_text'            => '',
	'url'                  => '',
	'image_html'           => '',		 	
	'overlay_link'         => '',
	'lightbox_content'     => '',
	'lightbox_description' => '',
	'box_link'             => '', //this is made up variable....no input from user
	'custom_css_class'     => '',
	'unique_id'            => '',
	'custom_icon_upload'   => '',
	'icon_fontawesome'     => '',
	'icon_openiconic'      => '',
	'icon_typicons'        => '',
	'icon_entypoicons'     => '',
	'icon_linecons'        => '',
	'icon_entypo'          => '',
	'type'                 => 'fontawesome',
	'animate' => ''
	), $atts));

	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
	
	// Let WordPress build the whole img tag so it includes ALT and other attributes.
	// Using large size here to prevent server crash from full-size images
	$image_html = wp_get_attachment_image( $attachment_id,'large' );

	if(!empty($link_text)){ //this is centered text link at bottom of image-box
		$box_link = '<p class="true-image-box-2-link"><a href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'">'.$link_text.'</a></p>';
	}

	if(!empty($a_href)){
	$overlay_link = '<a class="overlay-link" href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'"></a>';
	}

	if(!empty($lightbox_content)){
	$overlay_link = '<a class="overlay-link" href="'.$lightbox_content.'" data-gal="prettyPhoto" title="'.$lightbox_description.'"></a>';
	}	

	//Build output for icon
	if(!empty($icon)){
		$icon_output = '<i class="true-custom-icon fa '.$icon.'"></i>';  
	}
	
	//Build output for icon
	if(!empty($type)){
		$icon_output = '<i class="true-custom-icon fa '.esc_attr( ${"icon_" . $type} ).'"></i>';
	}

	 /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }	
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }	
	
	
	$output = '<div class="true-image-box-2 tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'"><div class="true-img-wrap">'.$image_html.'</div><div class="true-text-wrap"><span class="icon-circ-wrap">'.$icon_output.'</span><div class="callout-details-wrap">'.$content.$box_link.'</div></div>'.$overlay_link.'</div>';

	// Dynamic CSS Function
	$style_code = '.true-image-box-2.'.$unique_id.' {background: '.$box_bg_color.';}
	.true-image-box-2.'.$unique_id.' .icon-circ-wrap {background: '.$icon_bg_color.';}
	.true-image-box-2.'.$unique_id.' .true-text-wrap .icon-circ-wrap i {color: '.$icon_color.';}
	.true-image-box-2.'.$unique_id.' .true-image-box-2-link a {color: '.$link_color.';border-bottom: 1px dotted '.$link_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Number Counter
--------------------------------------------------------------*/	
public function render_karma_builder_number_counter($atts, $content = null) {
	extract(shortcode_atts(array(
	'number'           => '125',
	'number_color'     => '',
	'number_size'      => '50px',
	'number_weight'    => '400',
	'title'            => 'Lorem Ipsum',
	'title_color'      => '',
	'title_size'       => '18px',
	'title_weight'     => '400',
	'divider_height'   => '4px',
	'divider_color'    => '',
	'custom_css_class' => '',
	'unique_id'        => '',
	'animate'          => ''
	), $atts));
	  
	if(function_exists('wpb_js_remove_wpautop')){					  
    $title = wpb_js_remove_wpautop($title, false);
    }else{
    $title = do_shortcode(shortcode_unautop($title));
    }
	
	// Ensure HTML tags get closed
	$title = force_balance_tags($title);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Dynamic CSS Function
	$style_code = '.true-counter-wrap.'.$unique_id.' h3.true-counter {color: '.$number_color.';}
	.true-counter-wrap.'.$unique_id.' h3:after {background: '.$divider_color.';height: '.$divider_height.';}
	.true-counter-wrap.'.$unique_id.' h4 {color: '.$title_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
	
	return '<div class="true-counter-wrap tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'"><h3 class="true-counter true-timer" data-to="'.$number.'" data-speed="1500" data-refresh-interval="100" style="font-size:'.$number_size.';font-weight:'.$number_weight.';"></h3><h4 style="font-size:'.$title_size.';font-weight:'.$title_weight.';">'.$title.'</h4></div>';
}// END shortcode

/*--------------------------------------------------------------
Orbit - Pricing Box 1
--------------------------------------------------------------*/
//styles: true-pricing-style-1, true-pricing-style-2
function render_karma_builder_pricing_box_1($atts, $content = null) {
	extract(shortcode_atts(array(
	'style'         => '',
	'color'         => '',
	'plan'          => 'Pro',
	'currency'      => '$',
	'price'         => '39',
	'term'          => 'per month',
	'button_label'  => 'Sign Up',
	'button_size'   => 'small',
	'button_color'  => '',
	'url'           => '',
	'custom_css_class'   => '',
	'animate' => ''
	), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
	
	if ($style == 'style-1'){
	$output = '<div class="true-pricing-column tt_'.$animate.' true-pricing-'.$style.' '.$custom_css_class.'"><div class="true-pricing-top true-cb-title-'.$color.'">
	<p class="true-pricing-plan">'.$plan.'</p>
	<p class="true-pricing-price"><sup>'.$currency.'</sup>'.$price.'</p>
	<p class="true-pricing-term">'.$term.'</p>
	</div>'.$content.'<hr />
	<a href="'.$a_href.'" class="'.sanitize_html_class( $button_size ).'_button '.sanitize_html_class( $button_size ).'_'.sanitize_html_class( $button_color ).' ka_button" target="'.$a_target.'" title="'.$a_title.'">' .$button_label. '</a></div>';
	}
	
	if ($style == 'style-2'){
	$output = '<div class="true-pricing-column tt_'.$animate.' true-pricing-'.$style.' '.$custom_css_class.'"><div class="true-pricing-top true-cb-title-'.$color.'">
	<p class="true-pricing-plan">'.$plan.'</p>
	</div>'.$content.'<hr /><p class="true-pricing-price"><sup>'.$currency.'</sup>'.$price.'</p>
	<p class="true-pricing-term">'.$term.'</p>
	<a href="'.$a_href.'" class="'.sanitize_html_class( $button_size ).'_button '.sanitize_html_class( $button_size ).'_'.sanitize_html_class( $button_color ).' ka_button" target="'.$a_target.'" title="'.$a_title.'">' .$button_label. '</a></div>';
	}
	
  return $output;
} // END shortcode

/*--------------------------------------------------------------
Orbit - Progress Bar
--------------------------------------------------------------*/
public function render_karma_builder_progress_bar( $atts, $content = null ) {      
	  extract(shortcode_atts(array(  
	  'title'            => 'Lorem Ipsum',
	  'title_color'      => '',
	  'number'           => '50',
	  'number_color'     => '',
	  'track_color'      => '',
	  'bar_color'        => '',
	  'symbol'           => '%',
	  'custom_css_class' => '',
	  'unique_id'        => '',
	  'rounded_progress' => '',
	  'progress_height'  => '20px',
	  'animate' => ''
	  ), $atts));

	if(function_exists('wpb_js_remove_wpautop')){					  
    $title = wpb_js_remove_wpautop($title, false);
    }else{
    $title = do_shortcode(shortcode_unautop($title));
    }
	
	// Ensure HTML tags get closed
	$title = force_balance_tags($title);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Dynamic CSS Function
	$style_code = '.true-progress-section.'.$unique_id.' h4.pull-left {color: '.$title_color.';}
	.true-progress-section.'.$unique_id.' h4.pull-right {color: '.$number_color.';}
	.true-progress-section.'.$unique_id.' .progress {background: '.$track_color.';}
	.true-progress-section.'.$unique_id.' .progress-bar {background: '.$bar_color.';}
	.true-progress-section.'.$unique_id.' .progress, .true-progress-section.'.$unique_id.' .progress-bar {height: '.$progress_height.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
	
	 return '<div class="true-progress-section tt_'.$animate.' '.$custom_css_class.' '.$unique_id.' '.$rounded_progress.'">
	 			<div class="progress-title">
	 				<h4 class="pull-left">'.$title.'</h4>
	 				<h4 class="pull-right"><span class="true-progress-number"><span>'.$number.'</span></span>'.$symbol.'</h4>
	 			</div>
	 			<div class="progress">
	 				<div class="progress-bar" data-number="'.$number.'"></div>
	 			</div>
	 		</div>';

}// END shortcode

/*--------------------------------------------------------------
Orbit - Progress Bar (Vertical)
--------------------------------------------------------------*/
public function render_karma_builder_progress_bar_vertical($atts, $content = null) {
	extract(shortcode_atts(array(  
	'title'            => 'Lorem Ipsum',
    'title_color'      => '',
    'number'           => '50',
    'number_color'     => '',
    'track_color'      => '',
    'bar_color'        => '',
    'symbol'           => '%',
    'custom_css_class' => '',
	'unique_id'        => '',
	'animate' => ''
	), $atts));
	  
	if(function_exists('wpb_js_remove_wpautop')){					  
    $title = wpb_js_remove_wpautop($title, false);
    }else{
    $title = do_shortcode(shortcode_unautop($title));
    }
	
	// Ensure HTML tags get closed
	$title = force_balance_tags($title);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Dynamic CSS Function
	$style_code = '.true-progress-section-vertical.'.$unique_id.' h4.true-progress-title {color: '.$title_color.';}
	.true-progress-section-vertical.'.$unique_id.' h4.true-progress-text {color: '.$number_color.';}
	.true-progress-section-vertical.'.$unique_id.' .progress-wrapper {background: '.$track_color.';}
	.true-progress-section-vertical.'.$unique_id.' .progress-bar-vertical {background: '.$bar_color.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
	
	return '<div class="true-progress-section-vertical tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'"><div class="progress-wrapper"><div class="progress-bar-vertical" data-number="'.$number.'"></div></div><h4 class="true-progress-title">'.$title.'</h4><h4 class="true-progress-text"><span class="true-progress-number"><span>'.$number.'</span></span>'.$symbol.'</h4></div>';
}// END shortcode

/*--------------------------------------------------------------
Orbit - Service List
--------------------------------------------------------------*/
public function render_karma_builder_services( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'icon'                 => '',
	'icon_color'           => '#d3565a',
	'icon_color_hover'     => '',
	'bg_color'             => '',
	'bg_color_hover'       => '',
	'border_color'         => '#a2dce2',
	'border_width'         => '2px',
	'animate'              => '',
	'url'                  => '',
	'lightbox_content'     => '',
	'lightbox_description' => '',
	'custom_css_class'     => '',
	'unique_id'            => '',
	'custom_icon'          => '',
	'custom_icon_upload'   => '',
	'type' => '',
	'icon_fontawesome' => '',
	'icon_openiconic' => '',
	'icon_typicons' => '',
	'icon_entypoicons' => '',
	'icon_linecons' => '',
	'icon_entypo' => '',	
	), $atts));
	
	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );	
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Required by param => 'vc_link' to parse the link
	$url = vc_build_link( $url );
	// grab the attributes
	$a_href   = $url['url'];
	$a_title  = $url['title'];
	$a_target = $url['target'];
	
    $output = '<div class="true-services '.$custom_css_class.' '.$unique_id.' tt_'.$animate.'">';
  
    if(!empty($lightbox_content)){
      $output .= '<a href="'.$lightbox_content.'" data-gal="prettyPhoto" title="'.$lightbox_description.'">';
    } elseif(!empty($a_href)){
      $output .= '<a href="'.$a_href.'" target="'.$a_target.'" title="'.$a_title.'">';
    }
    
	//Build output for icon
	if(!empty($icon)){
		$icon_output = '<i class="true-custom-icon fa '.$icon.'"></i>';  
	}
	
	//Build output for icon
	if(!empty($type)){
		$icon_output = '<i class="true-custom-icon fa '.esc_attr( ${"icon_" . $type} ).'"></i>';
	}	

	 /**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
    }
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
    }	
		 
	//add icon back to output.
	$output.= $icon_output;    
    
  
    if(!empty($lightbox_content)){
      $output .= '</a>';
    } elseif(!empty($a_href)){
      $output .= '</a>';
    }
  
    $output .= '<div class="true-description true-montserrat">'.$content.'</div></div>';

    // Dynamic CSS Function
	$style_code ='
	.true-services.'.$unique_id.' .true-custom-icon {
		color:'.$icon_color.';
		border: '.$border_width.' solid '.$border_color.';
		background:'.$bg_color.';
	}
	
	.true-services.'.$unique_id.' .true-custom-icon:hover { 
		color:'.$icon_color_hover.' !important;
		background-color:'.$bg_color_hover.';
	}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);
  
    return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Tab 1
--------------------------------------------------------------*/ 
public function render_karma_builder_tab_1( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'tab_id'               => '',
	'color_scheme'         => '',
	'custom_css_class'     => ''
	), $atts));
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();
	
	// Generate Random ID
	$tab_id = karma_builder_truethemes_random();
	
	$output = '<div class="true-tabcolor-'.$color_scheme.' true-tabs-style-1 '.$custom_css_class.'" id="'.$tab_id.'">
	<ul class="'.$tab_id.' true-nav nav-pills nav-justified" role="tablist"></ul><div class="true-tab-content">'.$content.'</div></div>';

	/**
	* javascript for auto generating the tab navigation <li>, 
	* because cannot use PHP due to the way html is nested.
	* Javascript function karma_builder_built_tab_nav() is found in orbit.js
	*/
	$output.= '<script type="text/javascript">jQuery(document).ready(function(){truethemes_karma_builder_tabs("'.$tab_id.'");});</script>';

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Tab 1 [content]
--------------------------------------------------------------*/
public function render_karma_builder_tab_1_content( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'tab_content_id'     => '',
	'tab_active'         => '',
	'nav_tab_title'      => 'New Tab',
	'icon'               => '',
	'custom_icon'        => '',
	'custom_icon_upload' => '',
	'type'               => '',
	'icon_fontawesome'   => '',
	'icon_openiconic'    => '',
	'icon_typicons'      => '',
	'icon_entypoicons'   => '',
	'icon_linecons'      => '',
	'icon_entypo'        => '',	
	), $atts));
	 	 
	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );	 	 
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
	if($tab_active == 'yes'){ $active = "in active"; } else { $active = ''; }

	// Generate Random ID
	$tab_content_id = karma_builder_truethemes_random();
	
	//Build output for icon
	if(!empty($type)){
		$icon = ${"icon_" . $type};
	}

	/**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
        $icon = '';
    }
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
        $icon = '';
    }    
	
	$output = '<div class="tab-pane fade '.$active.'" id="'.$tab_content_id.'" data-title="'.$nav_tab_title.'" data-icon="'.$icon.'">'.$icon_output.$content.'</div>';

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Tab 2
--------------------------------------------------------------*/ 
public function render_karma_builder_tab_2( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'tab_id'           => '',
	'color_scheme'     => '',
	'custom_css_class' => '',
	'unique_id'        => '',
	'animate' => ''
	), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Generate Random ID
	$tab_id = karma_builder_truethemes_random();

	$output = '<div class="true-tabs-style-2 tt_'.$animate.' '.$custom_css_class.' '.$unique_id.'" id="tab2-'.$tab_id.'"><ul class="'.$tab_id.' true-nav nav-tabs" role="tablist"></ul><div class="true-tab-content">'.$content.'</div></div>';

	// javascript for auto generating the tab navigation <li>	
	$output.="<script type='text/javascript'>jQuery(document).ready(function(){truethemes_karma_builder_tabs_2('{$tab_id}');});</script>";

	// Dynamic CSS Function
	$style_code ='.true-tabs-style-2.'.$unique_id.' .nav-tabs > li.active > a:active,
	.true-tabs-style-2.'.$unique_id.' .nav-tabs > li.active > a:focus,
	.true-tabs-style-2.'.$unique_id.' .nav-tabs > li.active > a {color: '.$color_scheme.' !important;}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	return $output;
	}// END shortcode

/*--------------------------------------------------------------
Orbit - Tab 2 [content]
--------------------------------------------------------------*/
public function render_karma_builder_tab_2_content( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'tab_content_id'         => '',
	'tab_active'             => '',
	'nav_tab_title'          => 'New Tab'
	), $atts));
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
	if($tab_active == 'yes'){ $active = "in active"; } else { $active = ''; }
	
	// Generate Random ID
	$tab_content_id = karma_builder_truethemes_random();
	
	$output = '<div class="fade tab-pane '.$active.'" id="'.$tab_content_id.'" data-title="'.$nav_tab_title.'">'.$content.'</div>';

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Tab 3
--------------------------------------------------------------*/ 
public function render_karma_builder_tab_3( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'tab_id'             => '',
	'custom_css_class'   => '',
	'unique_id'          => '',
	'disable_icon'       => '',
	'menu_bg_color'      => '',
	'link_color'         => '',
	'link_color_hover'   => '',
	'link_color_active'  => '',
	'tab_bgcolor_hover'  => '',
	'tab_bgcolor_active' => '',
	'tab_border_color'   => '',
	'animate' => ''
	), $atts));

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	// Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();

	// Generate Random ID
	$tab_id = karma_builder_truethemes_random();
    $color_scheme         =(!isset($color_scheme))?null:$color_scheme;

	$output = '<div class="true-tabcolor-'.$color_scheme.' tt_'.$animate.' true-tabs-style-3 '.$custom_css_class.' '.$unique_id.'" id="'.$tab_id.'">
	<ul class="'.$tab_id.' true-nav nav-pills nav-stacked" role="tablist"></ul><div class="true-tab-content">'.$content.'</div></div>';

	/**
	* javascript for auto generating the tab navigation <li>, 
	* because cannot use PHP due to the way html is nested.
	* Javascript function karma_builder_built_tab_nav() is found in orbit.js
	*/
	$output.= '<script type="text/javascript">jQuery(document).ready(function(){truethemes_karma_builder_tabs("'.$tab_id.'");});</script>';

	// Dynamic CSS Function
	$style_code ='.true-tabs-style-3.'.$unique_id.' .nav-stacked > li.active > a,
	.true-tabs-style-3.'.$unique_id.' .nav-stacked > li.active > a:hover,
	.true-tabs-style-3.'.$unique_id.' .nav-stacked > li.active > a:focus,
	.true-tabs-style-3.'.$unique_id.' .nav-stacked > li.active > .fa {color: '.$link_color_active.';background-color: '.$tab_bgcolor_active.';}
	.true-tabs-style-3.'.$unique_id.' .nav-stacked > li > a:hover{ background:'.$tab_bgcolor_hover.'; color:'.$link_color_hover.'; }
	.true-tabs-style-3.'.$unique_id.' .nav-stacked li {border-color: '.$tab_border_color.';}
	.true-tabs-style-3.'.$unique_id.' .nav-stacked {background-color:'.$menu_bg_color.';}';

	/*
	* disable icon css
	* @since 1.2
	*/	
	if($disable_icon == 'yes'){
	$style_code.='.true-tabs-style-3 .fa{display:none !important;}.true-tabs-style-3 .nav-stacked > li > a {padding-left: 25px;}';
	}

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Tab 3 [content]
--------------------------------------------------------------*/
public function render_karma_builder_tab_3_content( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'tab_content_id'     => '',
	'tab_active'         => '',
	'nav_tab_title'      => 'New Tab',
	'icon'               => '',
	'custom_icon'        => '',
	'custom_icon_upload' => '',
	'type'               => '',
	'icon_fontawesome'   => '',
	'icon_openiconic'    => '',
	'icon_typicons'      => '',
	'icon_entypoicons'   => '',
	'icon_linecons'      => '',
	'icon_entypo'        => '',	
	), $atts));
	
	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );	
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
	if($tab_active == 'yes'){ $active = "in active"; } else { $active = ''; }
	
	// Generate Random ID
	$tab_content_id = karma_builder_truethemes_random();
	
	//Build output for icon
	if(!empty($type)){
		$icon = ${"icon_" . $type};
	}	

	/**
	 * Custom Icon
	 *
	 * $custom_icon is HTML code inputted from the user
	 * Visual composer uses base64_encode for a users HTML-input
	 * We're using base64_decode() to "un-encode" a users HTML input
	 * nothing malicious going on here :)
	 *
	 * @since Orbit 1.2
	 */
    if(!empty($custom_icon)){
    //custom icon will overwrite icon if there is any html entered by customer.
    	$icon_output = rawurldecode( base64_decode( strip_tags( $custom_icon) ) );
        $icon = '';
    }
    
    
    if(!empty($custom_icon_upload)){
    //custom icon upload will overwrite the above custom icon and icon.
        $uploaded_custom_icon_image = wp_get_attachment_image($custom_icon_upload,'full');
        $icon_output = '<span class="karma-custom-icon-img">'.$uploaded_custom_icon_image.'</span>';
        $icon = '';
    }    
    
	
	$output = '<div class="tab-pane fade '.$active.'" id="'.$tab_content_id.'" data-title="'.$nav_tab_title.'" data-icon="'.$icon.'">'.$icon_output.$content.'</div>';

	return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Testimonial 1
--------------------------------------------------------------*/ 
public function render_karma_builder_testimonial_1( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'custom_css_class' => '',
	'animate' => ''
	), $atts));
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	$output = '<div class="true-testimonial-1 tt_'.$animate.' '.$custom_css_class.'"><div class="loading true-testimonial-1-flexslider"><ul class="slides clearfix">'.$content.'</ul></div></div>';

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Testimonial 1 [slide]
--------------------------------------------------------------*/
public function render_karma_builder_testimonial_1_slide( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'banner_image_attachment_id'          => '',
	'client_headshot_image_attachment_id' => '',
	'client_name'                         => '',
	'testimonial_text'                    => '',
	'banner_image_html'                   => '',
	'client_headshot_image_html'          => '',
	), $atts));
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }
	
	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
	// Let WordPress build the whole img tag so it includes ALT and other attributes.
	// Using large size here to prevent server crash from full-size images
	$banner_image_html = wp_get_attachment_image( $banner_image_attachment_id,'large' );
		
	//see add_image_size 'testimonial-user' declared on the very top of this page.
	$client_headshot_image_html = wp_get_attachment_image( $client_headshot_image_attachment_id,'testimonial-user' );	
				
	
	$output = '<li>'.$banner_image_html.'<div class="true-slider-content"><div class="user-section">'.$client_headshot_image_html.'<p><strong>'.$client_name.'</strong></p></div><p>'.$testimonial_text.'</p></div></li>';

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Testimonial 2
--------------------------------------------------------------*/
public function render_karma_builder_testimonial_2( $atts, $content = null ) {      
 	 extract(shortcode_atts(array(
 	 'custom_css_class'           => '',
 	 'testimonial_bg_color'       => '',
	 'testimonial_text_color'     => '',
	 'testimonial_text_size'      => '',
	 'controls_style'             => '',
	 'unique_id'                  => '',
	 'animate' => ''
 	 ), $atts));
	 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
    } else { $content = do_shortcode(shortcode_unautop($content)); }

    // Generate Unique ID to be used for styling
	$unique_id = karma_builder_truethemes_random();
	
	$output = '<div class="true-testimonial-2 tt_'.$animate.' '.$custom_css_class.' '.$unique_id.' '.$controls_style.'"><div class="loading true-testimonial-2-flexslider"><ul class="slides clearfix">'.$content.'</ul></div></div>';

	// Dynamic CSS Function
	$style_code = '.true-testimonial-2.'.$unique_id.' .testimonial-text {background:'.$testimonial_bg_color.';}
	.true-testimonial-2.'.$unique_id.' .testimonial-text p {color:'.$testimonial_text_color.';font-size:'.$testimonial_text_size.';}';

	//must be called before return $output
	$this->karma_builder_dynamic_embed_css($style_code);

	 return $output;
}// END shortcode

/*--------------------------------------------------------------
Orbit - Testimonial 2 [slide]
--------------------------------------------------------------*/
public function render_karma_builder_testimonial_2_slide( $atts, $content = null ) {      
	extract(shortcode_atts(array(
	'client_headshot_image_attachment_id' => '',
	'testimonial_text'                    => '',
	'client_headshot_image_src'           => ''
	), $atts));
	
	//see add_image_size 'testimonial-user' declared on the very top of this page.
	$client_headshot_image_src = wp_get_attachment_image_src( $client_headshot_image_attachment_id,'testimonial-user-2' );
	
	
	if(empty($client_headshot_image_src)){
	$client_headshot_image_src[0] = "http://placehold.it/50x50";
	}	

	$output = '<li data-thumb="'.$client_headshot_image_src[0].'"><div class="testimonial-text"><p>'.$testimonial_text.'</p></div></li>';

	 return $output;
}// END shortcode


/**
 * Original Karma Theme Shortcodes
 *
 * Note: some are converted to Visual Composer Elements vc_map()
 * and others only remain for backward-compatible
 *
 * @since Karma Builder 1.0
 */

/*--------------------------------------------------------------
Karma - Button
--------------------------------------------------------------*/
public function render_karma_builder_button( $atts, $content = null ) {
  extract(shortcode_atts(array(
  'size'      => '',
  'style'     => '',
  'url'       => '',
  'target'    => '',
  'icon'      => '',//do not remove for backward compatibility
  'popup'     => '',
  'title'     => '',
  'link'      => '',
  'type'      => '',
  'alignment' => '',
  'icon_fontawesome' => 'fa fa-adjust',
  'icon_openiconic' => 'vc-oi vc-oi-dial',
  'icon_typicons' => 'typcn typcn-adjust-brightness',
  'icon_entypoicons' => 'entypo-icon entypo-icon-note',
  'icon_linecons' => 'vc_li vc_li-heart',
  'icon_entypo' => 'entypo-icon entypo-icon-note',
  'add_icon'=>'',  
  'animate' => ''
  ), $atts));
  
  //build link and for backward compatibility
  $link = vc_build_link( $link );
  if(!empty($link['url'])){
  $url = $link['url'];
  $target = $link['target'];
  }
  
  //format sizes so user doesn't need to input "_"
  $size   = ($size == 'small')  ? 'small_'  : $size;
  $size   = ($size == 'medium') ? 'medium_' : $size;
  $size   = ($size == 'large')  ? 'large_'  : $size;
  
  $output = '<a class="ka_button tt_'.$animate.' '.$size.'button '.$size.$style.'"';
  
  //link target
  if('' != $target){
	  $output .= ' target="'.$target.'"';
  }
  
  //link title
  if('' != $title){
	  $output .= ' title="'.$title.'"';
  }else{
      $output .= ' title="'.$link['title'].'"';
  }
  
  $output .= ' href="';
  
  //display popup in lightbox or normal url
  if('' != $popup){
	  $output .= $popup.'" data-gal="prettyPhoto">';
  } else {
	  $output .= $url.'">';
  }
  
  $r_content = '';
  
  if(!empty($icon)){
  	$r_content = '<i class="fa '.$icon.'"></i>' .do_shortcode($content). '</a>'; 
  }
  
  if(!empty($type)){
    //Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );	 
  	$r_content = '<i class="fa '.esc_attr( ${"icon_" . $type} ).'"></i>' .do_shortcode($content). '</a>'; 
  }
  
  if(empty($icon) && empty($type)){
	$r_content = do_shortcode($content). '</a>';
  }
  
  if($add_icon!='yes'){
  
  $output .= do_shortcode($content). '</a>';
  
  }else{
  
  $output .= $r_content;
  
  }

  return '<p style="text-align:'.$alignment.';">'.$output.'</p>';
}

/*--------------------------------------------------------------
Karma - Business Contact
--------------------------------------------------------------*/
public function render_karma_builder_business_contact( $atts, $content = null ) {
	extract(shortcode_atts(array(
	'phone_number'     => '',
  	'fax_number'       => '',
	'skype_username'   => '',
	'skype_label'      => 'Skype',
	'email_address'    => '',
	'directions_url'   => '',
	'directions_label' => 'get driving directions',
	'animate' => ''
	), $atts));
  
	$output = '<ul class="true-business-contact tt_'.$animate.'">';

	if(!empty($phone_number)):
		$output .= '<li><a href="tel://'.$phone_number.'" class="true-biz-phone">'.$phone_number.'</a></li>'; endif;
	if(!empty($fax_number)):
   		 $output .= '<li><a href="tel://'.$fax_number.'" class="true-biz-fax">'.$fax_number.'</a></li>'; endif;
	if(!empty($skype_username)):
		$output .= '<li><a href="skype:'.$skype_username.'?call" class="true-biz-skype">'.$skype_label.'</a></li>'; endif;
	if(!empty($email_address)):
		$output .= '<li><a href="mailto:'.$email_address.'" class="true-biz-email">'.$email_address.'</a></li>'; endif;
	if(!empty($directions_url)):
		$output .= '<li><a href="'.$directions_url.'" class="true-biz-directions" target="_blank">'.$directions_label.'</a></li>'; endif;

	$output .= '</ul>';

	return $output;
}

/*--------------------------------------------------------------
Karma - Callout Boxes
--------------------------------------------------------------*/
public function render_karma_builder_callout( $atts, $content = null ) {
  extract(shortcode_atts(array(
  'font_size' => '13px',
  'style' => '',
  'animate' => ''
  ), $atts));
  
  if('dark' == $style) {$style == 'black';}

$content =  '<div class="message_karma_'.$style.' colored_box tt_'.$animate.'" style="font-size:'.$font_size.';">' . do_shortcode($content) . '</div><br class="clear" />';
return $content;
}

/*--------------------------------------------------------------
Karma - Columns
--------------------------------------------------------------*/
public function render_karma_builder_one_sixth( $atts, $content = null ) {
   $content = '<div class="one_sixth tt-column">' . do_shortcode($content) . '</div>';
   return $content;
}
public function render_karma_builder_one_sixth_last( $atts, $content = null ) {
   $content = '<div class="one_sixth_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_one_fifth( $atts, $content = null ) {
   $content = '<div class="one_fifth tt-column">' . do_shortcode($content) . '</div>';
    return $content;
}
public function render_karma_builder_one_fifth_last( $atts, $content = null ) {
   $content = '<div class="one_fifth_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_one_fourth( $atts, $content = null ) {
   $content = '<div class="one_fourth tt-column">' . do_shortcode($content) . '</div>';
    return $content;
}
public function render_karma_builder_one_fourth_last( $atts, $content = null ) {
   $content = '<div class="one_fourth_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_one_third( $atts, $content = null ) {
   $content = '<div class="one_third tt-column">' . do_shortcode($content) . '</div>';
    return $content;
}
public function render_karma_builder_one_third_last( $atts, $content = null ) {
   $content = '<div class="one_third_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_one_half( $atts, $content = null ) {
   $content = '<div class="one_half tt-column">' . do_shortcode($content) . '</div>';
    return $content;
}
public function render_karma_builder_one_half_last( $atts, $content = null ) {
   $content = '<div class="one_half_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_two_thirds( $atts, $content = null ) {
   $content = '<div class="two_thirds tt-column">' . do_shortcode($content) . '</div>';
    return $content;
}
public function render_karma_builder_two_thirds_last( $atts, $content = null ) {
   $content = '<div class="two_thirds_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_three_fourth( $atts, $content = null ) {
   $content = '<div class="three_fourth tt-column">' . do_shortcode($content) . '</div>';
    return $content;
}
public function render_karma_builder_three_fourth_last( $atts, $content = null ) {
   $content = '<div class="three_fourth_last tt-column">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}
public function render_karma_builder_flash_wrap( $atts, $content = null ) {
   $content = '<div class="flash_wrap">' . do_shortcode($content) . '</div><br class="clear" />';
    return $content;
}

/*--------------------------------------------------------------
Karma - Image Frame + Lightbox
--------------------------------------------------------------*/
public function render_karma_builder_image_frame($atts, $content = null) {
  extract(shortcode_atts(array(  
  'style'           => 'modern',
  'local_uploaded_image_id' => '', //new attribute that will map to $image_path
  'external_image_url' => '', //new attribute that will map to $image_path
  'image_path'      => '', //Do not remove! leave it for backward compatibility
  'link_to_page'    => '', //Do not remove! leave it for backward compatibility
  'target'          => '', //Do not remove! leave it for backward compatibility
  'description'     => '',
  'link' 			=> '', //new attribute that will map to $link_to_page and $target
  'size'            => '',
  'lightbox'        => '',
  'lightbox_group'  => '1',
  'float'           => '',
  'animate' => ''
  ), $atts));
  
 //new - grab local uploaded image id to get image url
 $local_uploaded_image_attr = wp_get_attachment_image_src($local_uploaded_image_id,'full');
 $local_uploaded_image_url = $local_uploaded_image_attr[0];
 
 //new - if there is local upload image, we use it's url as $image_path.
 if(!empty($local_uploaded_image_url)){
 $image_path = $local_uploaded_image_url;
 }
 
 //new - if there is external image url, we use this as $image_path.
 if(!empty($external_image_url)){
 $image_path = $external_image_url;
 }

 //new - parse vc_link
 $url = vc_build_link( $link );

 //new - if there is url in vc_link, we map it to $link_to_page.
 if(!empty($url['url'])){
 //grab and map the attributes
 $link_to_page   = $url['url'];
 }
 
 //new - if there is target in vc_link, we map to $target.
 if(!empty($url['target'])){ 
 $target = $url['target'];
 }
 
 //new - overwrites link to page if lightbox is set, this will prevent error.
 if(!empty($lightbox)){
 $link_to_page = '';
 }

	   
 $framesize = $style.'_'.$size.' tt_'.$animate;
 $output = null;
 
 
/* --- FULL WIDTH -  BANNER --- */
if ($size == 'banner_full'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,922,201,$framesize,$lightbox,$link_to_page,'banner-full',$target,$description,$lightbox_group,$float);
}

/* --- FULL WIDTH -  ONE_HALF (2 Column) --- */
if ($size == 'two_col_large'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,437,234,$framesize,$lightbox,$link_to_page,'2',$target,$description,$lightbox_group,$float);
}

/* --- FULL WIDTH -  ONE_THIRD (3 Column) --- */
if ($size == 'three_col_large'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,275,145,$framesize,$lightbox,$link_to_page,'3',$target,$description,$lightbox_group,$float);
}

/* --- FULL WIDTH -  ONE_THIRD (3 Column - Square) --- */
if ($size == 'three_col_square'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,275,275,$framesize,$lightbox,$link_to_page,'3',$target,$description,$lightbox_group,$float);
}

/* --- FULL WIDTH -  ONE_FOURTH (4 Column) */
if ($size == 'four_col_large'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,190,111,$framesize,$lightbox,$link_to_page,'4',$target,$description,$lightbox_group,$float);
}

/* --- SIDE NAV -  BANNER --- */
if ($size == 'banner_regular'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,703,201,$framesize,$lightbox,$link_to_page,'banner-side-nav',$target,$description,$lightbox_group,$float);
}

/* --- SIDE NAV -  ONE_HALF (2 Column) --- */
if ($size == 'two_col_small'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,324,180,$framesize,$lightbox,$link_to_page,'2-small',$target,$description,$lightbox_group,$float);
}

/* --- SIDE NAV -  ONE_THIRD (3 Column) --- */
if ($size == 'three_col_small'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,202,113,$framesize,$lightbox,$link_to_page,'3-small',$target,$description,$lightbox_group,$float);
}

/* --- SIDE NAV -  ONE_FOURTH (4 Column) --- */
if ($size == 'four_col_small'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,135,76,$framesize,$lightbox,$link_to_page,'4-small',$target,$description,$lightbox_group,$float);
}

/* --- SIDE NAV + SIDEBAR -  BANNER --- */
if ($size == 'banner_small'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,493,201,$framesize,$lightbox,$link_to_page,'banner-side-nav-sidebar',$target,$description,$lightbox_group,$float);
}

/* --- PORTRAIT STYLE - FULL --- */
if ($size == 'portrait_full'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,612,792,$framesize,$lightbox,$link_to_page,'portrait-full',$target,$description,$lightbox_group,$float);
}

/* --- PORTRAIT STYLE - THUMBNAIL --- */
if ($size == 'portrait_thumb'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,275,355,$framesize,$lightbox,$link_to_page,'portrait-small',$target,$description,$lightbox_group,$float);
}

/* --- SQUARE IMAGE FRAME --- */
if ($size == 'square'){
$output .= truethemes_image_frame_constructor($style,'img-preload',$image_path,190,180,$framesize,$lightbox,$link_to_page,'square',$target,$description,$lightbox_group,$float);
}

return $output;
}

/*--------------------------------------------------------------
Karma - Dividers
--------------------------------------------------------------*/
//do not remove or comment out, leave for backward compatibility
public function render_karma_builder_hr_shadow($atts, $content = null) {
    $content = '<div class="hr_shadow {$custom_css_class}">&nbsp;</div>';  
   return $content;
}
//do not remove or comment out, leave for backward compatibility
public function render_karma_builder_hr($atts, $content = null) {
    $content = "<div class='hr {$custom_css_class}'>&nbsp;</div>"; 
    return $content; 
}
public function render_karma_separator_line($atts, $content = null){
  extract(shortcode_atts(array(  
  'style' => '',
  'custom_css_class' => '',
  ), $atts));
  
  $output = '';
  
  if($style == 'separator_shadow'){
    $output = '<div class="hr_shadow {$custom_css_class}">&nbsp;</div>';  
  }
  
  if($style == 'separator_clean_line'){
    $output = '<div class="hr {$custom_css_class}">&nbsp;</div>';  
  }
  
  return $output;  

}

public function render_karma_builder_top_link( $atts, $content = null ) {
   return '<div class="hr_top_link">&nbsp;</div><a href="#" class="link-top">' . do_shortcode($content) . '</a><br class="clear" />';
}

/*--------------------------------------------------------------
Karma - Vector Icons
--------------------------------------------------------------*/
public function render_karma_builder_font_awesome($atts, $content = null) {
  extract(shortcode_atts(array(  
  'icon'     => '',
  'size'     => '',
  'border'   => 'false',
  'pull'     => '',
  'color'    => ''
  
  ), $atts));
  
  $output = '<i class="fa '.$icon;
  
  if('' != $size):
  		$output .= ' '.$size;
  endif;
  
  if('true' == $border):
  		$output .= ' fa-border';
  endif;
  
  if('' != $pull):
  		$output .= ' '.$pull;
  endif; 
  
  if('' != $color):
  		$output .= '" style="color:'.$color.';';
  	endif;
  
  $output .= '"></i>';
  
  return $output;
}

/*--------------------------------------------------------------
Karma - Gap
--------------------------------------------------------------*/
public function render_karma_builder_sc_gap($atts, $content = null) {
  extract(shortcode_atts(array(  
  'size'            => '100px',
  ), $atts));
  
    $content = '<div class="hr_gap" style="height:'.$size.';"></div>';
    return $content;
}

/*--------------------------------------------------------------
Karma - Lists
--------------------------------------------------------------*/
public function render_arrow_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
		
   return '<ul class="list list1 '.$custom_css_class.'">' . $content . '</ul>';
}

public function render_list_item( $atts, $content = null ) {
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
   return '<li>' . $content. '</li>';
}

public function render_star_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

   return '<ul class="list list2 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}

public function render_circle_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
   return '<ul class="list list3 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}


public function render_check_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
	
   return '<ul class="list list4 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}


public function render_caret_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

   return '<ul class="list list5 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}


public function render_plus_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

   return '<ul class="list list6 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}


public function render_double_angle_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

   return '<ul class="list list7 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}


public function render_full_arrow_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

   return '<ul class="list list8 '.$custom_css_class.'">' . do_shortcode($content) . '</ul>';
}

/*--------------------------------------------------------------
Karma - Social Icons
--------------------------------------------------------------*/
function render_karma_social_shortcode( $atts, $content = null ) {
extract(shortcode_atts(array(
'style'            => 'image',
'target'           => '_self',
'show_title'       => '',
'rss'              => '',
'rss_title'        => 'RSS',
'twitter'          => '',
'twitter_title'    => 'Twitter',
'facebook'         => '',
'facebook_title'   => 'Facebook',
'email'            => '',
'email_title'      => 'Email',
'flickr'           => '',
'flickr_title'     => 'Flickr',
'youtube'          => '',
'youtube_title'    => 'YouTube',
'linkedin'         => '',
'linkedin_title'   => 'Linkedin',
'pinterest'        => '',
'pinterest_title'  => 'Pinterest',
'instagram'        => '',
'instagram_title'  => 'Instagram',
'foursquare'       => '',
'foursquare_title' => 'FourSquare',
'delicious'        => '',
'delicious_title'  => 'Delicious',
'digg'             => '',
'digg_title'       => 'Digg',
'google'           => '',
'google_title'     => 'Google +',
'dribbble'         => '',
'dribbble_title'   => 'Dribbble',
'skype'            => '',
'skype_title'      => 'Skype',
'vkontakte'        => '',
'vkontakte_title'  => 'Vkontakte', 
'vimeo'            => '',
'vimeo_title'      => 'Vimeo', 
'soundcloud'       => '',
'soundcloud_title' => 'Soundcloud',
'odnoklassniki'    => '',
'odnoklassniki_title' => 'Odnoklassniki', 
'animate' => ''
), $atts));
  
  if('image' == $style){ $style = 'tt_image_social_icons';}
  if('vector' == $style){ $style = 'tt_vector_social_icons';}
  if('vector_color' == $style){ $style = 'tt_vector_social_icons tt_vector_social_color';}
   
  
$output = '
<ul class="social_icons tt_'.$animate.' '.$style;

if('true' == $show_title){ $output .=' tt_show_social_title'; }else{ $output .=' tt_no_social_title'; }

$output .='">';

if(!empty($rss)):
$output .='<li><a title="'.$rss_title.'" class="rss" href="'.$rss.'" target="'.$target.'">'.$rss_title.'</a></li>'; endif;
if(!empty($twitter)):
$output .='<li><a title="'.$twitter_title.'" class="twitter" href="'.$twitter.'" target="'.$target.'">'.$twitter_title.'</a></li>'; endif;
if(!empty($facebook)):
$output .='<li><a title="'.$facebook_title.'" class="facebook" href="'.$facebook.'" target="'.$target.'">'.$facebook_title.'</a></li>'; endif;
if(!empty($email)):
$output .='<li><a title="'.$email_title.'" class="email" href="'.$email.'" target="'.$target.'">'.$email_title.'</a></li>'; endif;
if(!empty($flickr)):
$output .='<li><a title="'.$flickr_title.'" class="flickr" href="'.$flickr.'" target="'.$target.'">'.$flickr_title.'</a></li>'; endif;
if(!empty($youtube)):
$output .='<li><a title="'.$youtube_title.'" class="youtube" href="'.$youtube.'" target="'.$target.'">'.$youtube_title.'</a></li>'; endif;
if(!empty($linkedin)):
$output .='<li><a title="'.$linkedin_title.'" class="linkedin" href="'.$linkedin.'" target="'.$target.'">'.$linkedin_title.'</a></li>'; endif;
if(!empty($pinterest)):
$output .='<li><a title="'.$pinterest_title.'" class="pinterest" href="'.$pinterest.'" target="'.$target.'">'.$pinterest_title.'</a></li>'; endif;
if(!empty($instagram)):
$output .='<li><a title="'.$instagram_title.'" class="instagram" href="'.$instagram.'" target="'.$target.'">'.$instagram_title.'</a></li>'; endif;
if(!empty($foursquare)):
$output .='<li><a title="'.$foursquare_title.'" class="foursquare" href="'.$foursquare.'" target="'.$target.'">'.$foursquare_title.'</a></li>'; endif;
if(!empty($delicious)):
$output .='<li><a title="'.$delicious_title.'" class="delicious" href="'.$delicious.'" target="'.$target.'">'.$delicious_title.'</a></li>'; endif;
if(!empty($digg)):
$output .='<li><a title="'.$digg_title.'" class="digg" href="'.$digg.'" target="'.$target.'">'.$digg_title.'</a></li>'; endif;
if(!empty($google)):
$output .='<li class="google-plus"><a title="'.$google_title.'" class="google +" href="'.$google.'" target="'.$target.'">'.$google_title.'</a></li>'; endif;
if(!empty($dribbble)):
$output .='<li><a title="'.$dribbble_title.'" class="dribbble" href="'.$dribbble.'" target="'.$target.'">'.$dribbble_title.'</a></li>'; endif;
if(!empty($skype)):
$output .='<li><a title="'.$skype_title.'" class="skype" href="'.$skype.'" target="'.$target.'">'.$skype_title.'</a></li>'; endif;
if(!empty($vkontakte)):
$output .='<li><a title="'.$vkontakte_title.'" class="vkontakte" href="'.$vkontakte.'" target="'.$target.'">'.$vkontakte_title.'</a></li>'; endif;
if(!empty($vimeo)):
$output .='<li><a title="'.$vimeo_title.'" class="vimeo" href="'.$vimeo.'" target="'.$target.'">'.$vimeo_title.'</a></li>'; endif;
if(!empty($soundcloud)):
$output .='<li><a title="'.$soundcloud_title.'" class="soundcloud" href="'.$soundcloud.'" target="'.$target.'">'.$soundcloud_title.'</a></li>'; endif;
if(!empty($odnoklassniki)):
$output .='<li><a title="'.$odnoklassniki_title.'" class="odnoklassniki" href="'.$odnoklassniki.'" target="'.$target.'">'.$odnoklassniki_title.'</a></li>'; endif;

$output .='</ul>
';

return $output;	
}

/*--------------------------------------------------------------
* Karma - Accordions
* Need jQuery ui, which is enqueued in footer via script-enqueue.php
--------------------------------------------------------------*/
public function render_accordion( $atts, $content = null ){

    extract(shortcode_atts(array(
    'class' => 'accord1', //accordion class
    'active' => 'false', //since 3.0.1 close by default
    'custom_css_class'   => '',
    'animate'            => '',
    ), $atts));

    $output = '';
    $output .= '<ul class="accordion '.$class.' '.$custom_css_class.' tt_'.$animate.'">';
    $output .= do_shortcode($content) ;
    $output .= '</ul>';
    
 	//added @since version 2.6 to allow open tab by default.
 	//uses jQuery UI version 1.8.15
    //jquery initialise individual accordions.
    $output .= "<script type='text/javascript'>jQuery(document).ready(function() {jQuery( \".".$class."\" ).accordion(";

    //if user wants to open any tab be default, user will set active='1'
    if(isset($active)){
    //the first tab is actually 0, so we use active tab minus 1.
    if($active != 'false'){ //fixed since 3.0.1
		$active = $active-1;
    }	
    $output .= "{ active: ".$active.", autoHeight: false, heightStyle: \"content\", header: \".opener\", collapsible: true, event: \"click\"}";
    }

    $output .= ");});</script>";
    
    /* if(function_exists('truethemes_formatter')){
        //this will prevent raw tags from showing when using with other shortcodes..
    	return $output;	
    }else{
    	return $output;
    } */
    return $output;
    
}

/*--------------------------------------------------------------
Karma - Accordions [panel]
--------------------------------------------------------------*/
public function render_slide( $atts, $content = null ) {

    extract(shortcode_atts(array(), $atts));
    $slide   = $atts['name'];
    $output  = '';
    $output .= '<li><a href="#" class="opener"><strong>' .$slide. '</strong></a>';
    $output .= '<div class="slide-holder"><div class="slide">';
    $output .= '' . do_shortcode($content) .'';
    $output .= '</div></div></li>';
    return $output;
}	

/*--------------------------------------------------------------
Karma - Tabs
--------------------------------------------------------------*/
public function render_tabset($atts, $content = null) {
    global $i;
    extract(shortcode_atts(array('tab_labels'=>'','animate'=>''), $atts)); //originally there is no attribute here, we added $tab_labels for vc_map();
    
    if(!empty($tab_labels)){
    /*
    * new way of doing this, we added an attribute called $tab_labels, as mentioned above.
    * in $tab_labels, user enter names of tabs with comma as separator, via VC popup.
    * we explode it and keep in array, then use the foreach loop to print out the tab headers.
    */
    $indiviual_tab_labels = explode(',',$tab_labels);
    
	    $output = '';
	    $output .= '<div class="tabs-area tt_'.$animate.'">';
	    $output .= '<ul class="tabset">';
	    foreach ($indiviual_tab_labels as $tab) {
	    $tabID = "tab-" . $i++;
	    $output .= '<li><a href="#' . $tabID . '" class="tab"><span>' .$tab. '</span></a></li>';
	    }
	    $output .= '</ul>';
	    $output .= do_shortcode($content) .'</div>';
    
    
    }else{
    	
    	/*
    	* original codes from karma theme, keep this for backward compatibility
    	* the old codes sweep all attributes from tabset shortcode and print it's value using a foreach loop
    	* does not look for any specify attribute.
    	*/
    	
	    $output = '';
	    $output .= '<div class="tabs-area">';
	    $output .= '<ul class="tabset">';
	    foreach ($atts as $tab) {
	    $tabID = "tab-" . $i++;
	    $output .= '<li><a href="#' . $tabID . '" class="tab"><span>' .$tab. '</span></a></li>';
	    }
	    $output .= '</ul>';
	    $output .= do_shortcode($content) .'</div>';
	    
	 }
    
    return $output;
}

public function render_tab($atts, $content = null) {
    global $j;
    extract(shortcode_atts(array(), $atts));
    $output = '';
    $tabID = "tab-" . $j++;
    $output .= '<div id="' . $tabID . '" class="tab-box">' . do_shortcode($content) .'</div>';	
    return $output;	
}

/*--------------------------------------------------------------
Karma - Testimonials
--------------------------------------------------------------*/
public function render_testimonial_wrap( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => '',
 	 'animate' => ''
 	 ), $atts));
   $code = '<div class="true-testimonial-wrapper tt_'.$animate.' '.$custom_css_class.'"><div class="testimonials flexslider"><ul class="slides">' . do_shortcode($content) . '</ul></div></div><!-- END testimonials -->';
   return $code;
}

public function render_testimonial( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'client_name'     => '' //add client name so that it works properly on vc pop up.
 	 ), $atts));
 	 if(!empty($client_name)){
 	 $client = "<cite>&ndash;$client_name</cite>";
 	 }
   $code = '<li><blockquote><p>' . do_shortcode($content) . $client .'</p></blockquote></li>';
   return $code;
}

//leave this for backward compatibility.
public function render_client_name( $atts, $content = null ) {
   return '<cite>&ndash;' . do_shortcode($content) . '</cite>';
}

/*--------------------------------------------------------------
Karma - Team Members
--------------------------------------------------------------*/
public function render_team_member($atts, $content = null) {
  extract(shortcode_atts(array(
  //new attributes
  'local_uploaded_image_id'=> '',
  'external_image_url'=> '',
  'image_alt' => '',
  'url' => '',
  'animate' => '',
  //old attributes, do not remove
  'members_name'   => '',
  'members_title'  => '',
  'style'          => '',
  'size'           => 'square',
  'image_path'     => '', //do not remove this attribute, it is needed for backward compatibility
  'link_to_page'   => '', //do not remove this attribute, it is needed for backward compatibility
  'description'    => '', //do not remove this attribute, it is needed for backward compatibility
  'target' => '',
  'last_item'      => '',
  ), $atts));
  
  
    //prepare image, if existing shortcode, it will not have these two new attributes, instead it will have image_path attribute.
    
    //get uploaded image id to get image src..
  	$uploaded_image_src = wp_get_attachment_image_src( $local_uploaded_image_id,'full');
  	if(!empty($uploaded_image_src[0])){
  	$image_path = $uploaded_image_src[0];
  	}
  	
  	//external image will overwrite uploaded image.
  	if(!empty($external_image_url)){
  	$image_path = $external_image_url;
  	}		
  	
  	//if existing shortcode it will not have $url attribute, it will use $link_to_page, 
  	//if it is new $url, we parse and assign $url['ur'] to $link_to_page...etc
	$url = vc_build_link( $url );
	if(!empty($url['url'])){
	$link_to_page = $url['url'];
	$description = $url['title'];
	$target = $url['target'];
	}

  
  	$framesize = $style.'_'.$size;
  
	$output ='<div class="member-wrap tt_'.$animate.'';
	if('true' == $last_item): $output.=' member-last-item'; endif;
	$output.='"><div class="member-photo">';
	$output.= truethemes_image_frame_constructor($style,"img-preload",$image_path,190,180,$framesize,$lightbox='',$link_to_page,'square',$target,$image_alt,$lightbox_group='',$float='');
	$output.='</div><!-- END member-photo -->';
	$output.='<div class="member-bio"><h4 class="team-member-name">'.$members_name.'</h4><p class="team-member-title">'.$members_title.'</p>' . do_shortcode($content) . '</div><!-- END member-bio -->';
	
	$output.='</div><!-- END member-wrap -->';
	
return $output;
}

/*--------------------------------------------------------------
Karma - Typography
--------------------------------------------------------------*/
public function render_h1( $atts, $content = null ) {
   return '<h1>' . do_shortcode($content) . '</h1>';
}
public function render_h2( $atts, $content = null ) {
   return '<h2>' . do_shortcode($content) . '</h2>';
}
public function render_h3( $atts, $content = null ) {
   return '<h3>' . do_shortcode($content) . '</h3>';
}
public function render_h4( $atts, $content = null ) {
   return '<h4>' . do_shortcode($content) . '</h4>';
}
public function render_h5( $atts, $content = null ) {
   return '<h5>' . do_shortcode($content) . '</h5>';
}
public function render_h6( $atts, $content = null ) {
   return '<h6>' . do_shortcode($content) . '</h6>';
}

/*--------------------------------------------------------------
Karma - Text Callout
--------------------------------------------------------------*/
//do not remove or comment out, leave for backward compatibility
public function render_callout1( $atts, $content = null ) {
  $content ='<div class="callout-wrap"><span>' . do_shortcode($content) . '</span></div><!-- END callout-wrap --><br class="clear" />
';  
 return $content;

}
//do not remove or comment out, leave for backward compatibility
public function render_callout2( $atts, $content = null ) {
   $content = '<p class="callout2"><span>' . do_shortcode($content) . '</span></p><br class="clear" />';
    return $content;
}
public function render_karma_callout_text($atts, $content = null){
  extract(shortcode_atts(array(
  'style'  => '',
  'custom_css_class'=>'',
  'animate' => ''
  ), $atts));
  
  $output = '';
  
  if($style=='karma_callout_1'){
  $output = '<div class="callout-wrap tt_'.$animate.' '.$custom_css_class.'"><span>' . do_shortcode($content) . '</span></div><!-- END callout-wrap --><br class="clear" />
';  
  }

  if($style=='karma_callout_2'){
   $output = '<p class="callout2 '.$custom_css_class.'"><span>' . do_shortcode($content) . '</span></p><br class="clear" />';
  }
  
  return $output;

}

public function render_heading_horizontal($atts, $content = null) {
  extract(shortcode_atts(array(
  'type'  => 'h3',
  'margin_top'  => '20px',
  'margin_bottom'  => '20px'
  ), $atts));
  
  $output = '<'.$type.' class="heading-horizontal" style="margin:'.$margin_top.' 0 '.$margin_bottom.' 0;"><span>'. do_shortcode($content) .'</span></'.$type.'>';
  return $output;
  
}

/*--------------------------------------------------------------
Karma - Video Layouts
--------------------------------------------------------------*/
public function render_video_left( $atts, $content = null ) {
   $output = '<div class="video-wrap video_left">' . do_shortcode($content) . '</div><!-- END video-wrap -->';
     return $output;
}
public function render_video_right( $atts, $content = null ) {
   $output = '<div class="video-wrap video_right">' . do_shortcode($content) . '</div><!-- END video-wrap -->';
     return $output;
}
public function render_video_frame( $atts, $content = null ) {
extract(shortcode_atts(array(
'iframe_url'   => '',
'width'     => '100%',
'height'    => '312',
), $atts));

if(!empty($iframe_url)){
	$iframe = '<iframe src="'.$iframe_url.'" title="" width="'.$width.'" height="'.$height.'"></iframe>';
}else{
	$iframe = '';
}
   return '<div class="video-main">
	<div class="video-frame">' . $iframe . do_shortcode($content) . '</div><!-- END video-frame -->
</div><!-- END video-main -->';
}
public function render_video_text( $atts, $content = null ) {
   $output = '<div class="video-sub">' . do_shortcode($content) . '</div><!-- END video-sub --><br class="clear" />';
     return $output;
}

/*--------------------------------------------------------------
Karma - Misc.
--------------------------------------------------------------*/
/* ----- IFRAME SHORTCODE ----- */
public function render_iframe($atts, $content=null) {
extract(shortcode_atts(array(
'url'   => '',
'width'     => '100%',
'height'    => '500',
), $atts));
 
if (empty($url)) return 'http://';
return '<iframe src="'.$url.'" title="" width="'.$width.'" height="'.$height.'">'.$content.'</iframe>';
}

/*--------------------------------------------------------------
Karma - Blog Posts
--------------------------------------------------------------*/
//@since 4.0 - completely re-written shortcode for better optimize
public function render_blog_posts($atts, $content=null) {
extract(shortcode_atts(array(
'button_color'    => 'black',
'character_count' => '115',
'count'           => '3',
'image_path'      => '',
'layout'          => '',
'link_text'       => 'Read more',
'linkpost'        => '',
'post_category'   => '',
'style'           => 'modern',
'title'           => '',
'excluded_cat'    => '',
'animate' => ''
), $atts));

if($layout == 'right_sidebar'):

		/*-----------------------------*/
		/* Drag-to-Share
		/*-----------------------------*/
		//$dragshare comes from top of this file
		global $ttso;
		$dragshare = $ttso->ka_dragshare;
		if($dragshare == "true"):
		
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
			
	
			//Bitly API script
			wp_enqueue_script( 'bitly-api','http://bit.ly/javascript-api.js?version=latest&login=scaron&apiKey=R_6d2a7b26f3f521e79060a081e248770a', array('jquery'),'1.0',$in_footer = true);
			
			wp_enqueue_script( 'pretty-sociable', TRUETHEMES_JS .'/jquery.prettySociable.js', array('jquery'),'1.2.1',$in_footer = true);
			
			//localize prettySociable.js (must be placed after enqueue)		
			wp_localize_script('pretty-sociable', 'social_data', $pretty_data);
				

		endif; // if($dragshare)		


		ob_start();
        global $post;
		if($excluded_cat != ''){
		remove_filter('pre_get_posts','wploop_exclude');
		$exclude = $excluded_cat;
		}else{
		add_filter('pre_get_posts','wploop_exclude');
		$exclude = B_getExcludedCats();
		}
		
		
		if ($post_category != ''){
		//@since mod by denzel to use WP_Query class instead of get_posts, so that WPML works.
		$b_posts = new WP_Query('posts_per_page='.$count.'&offset=0&category_name='.$post_category.'');
		}else{
		$b_posts = new WP_Query('posts_per_page='.$count.'&cat='.$exclude);
		}
		
		if ( $b_posts->have_posts() ) : while ( $b_posts->have_posts() ) : $b_posts->the_post();
		
	    $format = get_post_format($post->ID);
	    
	    //standard, image, video, and link post formats all use the same default-content.php file      
	    if($format == '' || $format == 'image' || $format == 'video'){
	        //use require, do not use require_once, for some reason it does not work properly.
	    	require( WP_PLUGIN_DIR.'/karma_builder/post-formats/default-content.php' );
	    }else{
	        //for audio, gallery and quote post formats, use their own file.. eg.. default-audio.php
	    	require(  WP_PLUGIN_DIR.'/karma_builder/post-formats/default-'.$format.'.php' );    
	    }
		
		endwhile; endif;
		
		wp_reset_postdata();
		return ob_get_clean();

else:
//old blog_posts shortcode layouts. 

		$title            = $title;
		$count            = $count;
		$truethemes_count = 0;
		$truethemes_col   = 0;
		
		//@since ver 4.0.3 dev 5 mod by denzel to use either user input exclude cat or site option exclude cat.
		global $post;
		if($excluded_cat != ''){
		remove_filter('pre_get_posts','wploop_exclude');
		$exclude = $excluded_cat;
		}else{
		add_filter('pre_get_posts','wploop_exclude');
		$exclude = B_getExcludedCats();
		}
		
		
		if ($post_category != ''){
		//@since mod by denzel to use WP_Query class instead of get_posts, so that WPML works.
		$myposts = new WP_Query('posts_per_page='.$count.'&offset=0&category_name='.$post_category.'');
		}else{
		$myposts = new WP_Query('posts_per_page='.$count.'&cat='.$exclude);
		}
		
		if($layout == 'default'){
		$output = '<div class="blog-posts-shortcode-outer-wrap tt_'.$animate.'"><ul class="tt-recent-posts">';
		}else{
		$output = '<div class="blog-posts-shortcode-outer-wrap tt_'.$animate.'">';
		}
		if ($title != '') {$output .= '<h3>'.$title.'</h3>';};
		
		$truethemes_count = 0;
		$truethemes_col   = 0;
		
		//define values below to be used in loop below
		if ('default' == $layout){
		$image_width     = 65;
		$image_height    = 65;
		}
		
		if ('two_col_large' == $layout){
		$tt_frame_size   = 'two_col_large';
		$tt_column_size  = 'one_half';
		$tt_column_count = 2;
		$image_width     = 437;
		$image_height    = 234;
		$zoom            = '2';
		}
		
		if ('three_col_large' == $layout){
		$tt_frame_size   = 'three_col_large';
		$tt_column_size  = 'one_third';
		$tt_column_count = 3;
		$image_width     = 275;
		$image_height    = 145;
		$zoom            = '3';
		}
		
		if ('four_col_large' == $layout){
		$tt_frame_size   = 'four_col_large';
		$tt_column_size  = 'one_fourth';
		$tt_column_count = 4;
		$image_width     = 190;
		$image_height    = 111;
		$zoom            = '4';
		}
		
		if ('two_col_small' == $layout){
		$tt_frame_size   = 'two_col_small';
		$tt_column_size  = 'one_half';
		$tt_column_count = 2;
		$image_width     = 324;
		$image_height    = 180;
		$zoom            = '2-small';
		}
		
		if ('three_col_small' == $layout){
		$tt_frame_size   = 'three_col_small';
		$tt_column_size  = 'one_third';
		$tt_column_count = 3;
		$image_width     = 202;
		$image_height    = 113;
		$zoom            = '3-small';
		}
		
		if ('four_col_small' == $layout){
		$tt_frame_size   = 'four_col_small';
		$tt_column_size  = 'one_fourth';
		$tt_column_count = 4;
		$image_width     = 135;
		$image_height    = 76;
		$zoom            = '4-small';
		}
			
		if ( $myposts->have_posts() ) : while ( $myposts->have_posts() ) : $myposts->the_post();
		
		$permalink          = get_permalink($post->ID);
		$linkpost           = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
		$video_url          = get_post_meta($post->ID,'truethemes_video_url',true);
		$post_thumb         = null; //declare empty variable to prevent error.
		$thumb              = get_post_thumbnail_id(); //featured image
		$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true); //featured image (external source)
        $image_width        =(!isset($image_width))?null:$image_width;
        $image_height       =(!isset($image_height))?null:$image_height;
        $image_src          = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
				
		if ($linkpost == ''): $truethemeslink = $permalink; else: $truethemeslink = $linkpost; endif;
		
		
		/*--------------------------------------------*/
		/* blog post column layouts
		/*--------------------------------------------*/
		if ('default' != $layout){

		$truethemes_count++;
		$truethemes_col ++;
        try{
            $tt_column_count          =(!isset($tt_column_count))?null:$tt_column_count;
            $mod = ($truethemes_count % $tt_column_count == 0) ? 0 : $tt_column_count - $truethemes_count % $tt_column_count;
        }catch (Throwable $t){
                if($t->getMessage()=="Modulo by zero"){
                    $mod = 0;
                }
        }
		if($truethemes_col == $tt_column_count){$last = '_last';$truethemes_col = 0;}else{$last = '';}
            $tt_column_size        =(!isset($tt_column_size))?null:$tt_column_size;
            $tt_frame_size         =(!isset($tt_frame_size))?null:$tt_frame_size;
            $zoom         =(!isset($zoom))?null:$zoom;
            $output .= '
		<div class="'.$tt_column_size.$last.' tt-column">
			<div class="'.$style.'_img_frame '.$style.'_'.$tt_frame_size.'">
				<div class="img-preload lightbox-img">
					<a href="'.$truethemeslink.'" class="attachment-fadeIn">
						<div class="lightbox-zoom zoom-'.$zoom.' zoom-link" style="position:absolute; display: none;">&nbsp;</div>';
		
		//post thumbnail
		if (!empty($image_src)):
		$output .= '<img src="'.$image_src.'" alt="'.get_the_title().'" />';
		
		//video embed
		elseif(!empty($video_url)):
		$post_thumb .= '<span class="tt-blog-placeholder tt-blog-'.$tt_frame_size.' tt-blog-video">&nbsp;</span>';
		
		//placeholder image
		else:
		$post_thumb .= '<span class="tt-blog-placeholder tt-blog-'.$tt_frame_size.'">&nbsp;</span>';
		
		endif;
		$output .= $post_thumb;
		$output .= '</a></div></div>';
		
		} //END blog post column layouts
		
		
		
		/*--------------------------------------------*/
		/* blog post default layout (small thumbs)
		/*--------------------------------------------*/
		if ('default' == $layout){
			
		$output .= '<li><a class="tt-recent-post-link" title="'.get_the_title().'" href="'.$truethemeslink.'">';
		
		//post thumbnail
		if (!empty($image_src)):
		$output .= '<img src="'.$image_src.'" alt="'.get_the_title().'" class="tt-blog-sc-img" />';
		
		//video embed
		elseif(!empty($video_url)):
		$output .= '<span class="tt-blog-placeholder tt-blog-default tt-blog-video tt-blog-sc-img">&nbsp;</span>';
		
		//placeholder image
		else:
		$output .= '<span class="tt-blog-placeholder tt-blog-default tt-blog-sc-img">&nbsp;</span>';
		//$output .= '<span class="tt-blog-placeholder tt-blog-default tt-blog-sc-img fa fa-file-text-o">&nbsp;</span>';
		endif; 
		
		} //END blog post default layout (small thumbs)
		
		
		//remove <!--nextpage--> and show only first page content		
		$post_content =  explode('<!--nextpage-->',$post->post_content);
		$post_content =  (string)$post_content[0];
		$post_content =  apply_filters( 'the_content', $post_content );
		$post_content =  str_replace(']]>', ']]&gt;', $post_content);
		$post_content =  wp_strip_all_tags( $post_content );		
		$post_content =  substr(strip_tags($post_content),0,$character_count);
		$post_content =  rtrim($post_content); //remove space from end of string
		$post_content =  str_replace("<br>","",$post_content);
		$post_content =  strip_shortcodes( $post_content );
				
		$output .= '<h4>'.get_the_title().'</h4>';
		$output .= '<p>'.$post_content.'...</p>';
		
		//if ('default' != $layout){ $output .= '<a href="'.$truethemeslink.'" class="ka_button small_button small_black">'.$link_text.'</a></div>';}
		
		if ('default' != $layout){ $output .= '<a href="'.$truethemeslink.'">'.$link_text.'</a></div>';}
		if ('default' == $layout){ $output .= '</a></li>'; }
		
		endwhile; endif;
		if($layout ==  'default'){
		$output .= '</ul></div><br class="clear" />';
		}else{
		$output .= '</div><br class="clear" />';
		}
		
		wp_reset_postdata();
		return $output;

endif;
}


//related posts
public function render_related_posts( $atts ) {
	extract(shortcode_atts(array(
		'title' 	=> '',
		'limit' 	=> '',
		'post_id' 	=> '',
		'style' 	=> '',
		'target'	=> '',
	), $atts));

return related_posts_shortcode(array('title'=>$title,'limit'=>$limit,'post_id'=>$post_id,'style'=>$style,'target'=>$target));
}



/* ----- CATEGORIES ----- */
public function render_categorie_display($atts) {
	extract(shortcode_atts(array(
'title'   => 'Categories',
), $atts));
	
	$pos_excluded = positive_exlcude_cats();
	$pos_cats = $pos_excluded;
	$pos_args = array('orderby' => 'name', 'exclude' => $pos_cats, 'title_li' => '');	
	echo '<h3>'.$title.'</h3>';
	wp_list_categories($pos_args);
}


public function render_karma_list( $atts, $content = null ) {
 	 extract(shortcode_atts(array(
 	 'custom_css_class'     => '',
 	 'animate' => ''
 	 ), $atts));
 	 
	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);
		
   $list = '<ul class="list tt_'.$animate.' '.$custom_css_class.' fa-ul">' . $content . '</ul>';
   return $list;
}


public function render_karma_list_item( $atts, $content = null ){
	$defaults = array(
	    'type' => 'fontawesome',
	    'icon_fontawesome' => 'fa fa-adjust',
	    'icon_openiconic' => '',
	    'icon_typicons' => '',
	    'icon_entypoicons' => '',
	    'icon_linecons' => '',
	    'icon_entypo' => '',
		'custom_color' => '',
	
	);
	$atts = vc_shortcode_attribute_parse( $defaults, $atts );
	extract( $atts );
	
	// Enqueue needed icon font.
	vc_icon_element_fonts_enqueue( $type );

	// Visual Composer helper function
	if(function_exists('wpb_js_remove_wpautop')){ $content = wpb_js_remove_wpautop($content, false);
	} else { $content = do_shortcode(shortcode_unautop($content)); }

	// Ensure HTML tags get closed
	$content = force_balance_tags($content);

	$list_html .= '<li><i class="fa-li '.esc_attr( ${"icon_" . $type} ).'" style="color:'.esc_attr($custom_color).';"></i>'.$content.'</li>';

	return $list_html;
}


// Enqueue scripts and styles for the front end
public function karma_builder_enqueue_script() {
	//JavaScript
	wp_enqueue_script( 'karma-builder-bootstrap-js', plugins_url('js/bootstrap.min.js', __FILE__), array(), NULL, true );
	wp_enqueue_script( 'appear', plugins_url('js/appear.min.js', __FILE__), array(), NULL, true );
	wp_enqueue_script( 'waypoints', plugins_url('js/waypoints.min.js', __FILE__), array(), NULL, true );
	wp_enqueue_script( 'easyCharts', plugins_url('js/easy-pie-chart.min.js', __FILE__), array(), NULL, true );
	wp_enqueue_script( 'karma-builder', plugins_url('js/karma-builder.js', __FILE__), array(), NULL, true );

	//CSS
	wp_enqueue_style( 'karma-builder', plugins_url('css/karma-builder.css', __FILE__) );
}



}//end class KarmaBuilderVCExtendAddonClass

// Finally initialize code
new KarmaBuilderVCExtendAddonClass();

function karma_builder_extend_vc_class(){

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	    class WPBakeryShortCode_karma_builder_testimonial_1 extends WPBakeryShortCodesContainer {
	    }
	    class WPBakeryShortCode_karma_builder_testimonial_2 extends WPBakeryShortCodesContainer {
	    }
	    class WPBakeryShortCode_karma_builder_tab_1 extends WPBakeryShortCodesContainer {
	    }
	    class WPBakeryShortCode_karma_builder_tab_2 extends WPBakeryShortCodesContainer {
	    }
	    class WPBakeryShortCode_karma_builder_tab_3 extends WPBakeryShortCodesContainer {
	    }	
	    class WPBakeryShortCode_karma_builder_accordion extends WPBakeryShortCodesContainer {
	    } 
	    class WPBakeryShortCode_arrow_list extends WPBakeryShortCodesContainer {
	    }  	    
	    class WPBakeryShortCode_star_list extends WPBakeryShortCodesContainer {
	    } 
	    class WPBakeryShortCode_circle_list extends WPBakeryShortCodesContainer {
	    } 
	    class WPBakeryShortCode_check_list extends WPBakeryShortCodesContainer {
	    }  		    
	    class WPBakeryShortCode_caret_list extends WPBakeryShortCodesContainer {
	    } 
	    class WPBakeryShortCode_plus_list extends WPBakeryShortCodesContainer {
	    }
	    class WPBakeryShortCode_double_angle_list extends WPBakeryShortCodesContainer {
	    }  	
	    class WPBakeryShortCode_full_arrow_list extends WPBakeryShortCodesContainer {
	    }  
	    class WPBakeryShortCode_one_sixth extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_one_sixth_last extends WPBakeryShortCodesContainer {
	    }	
	    class WPBakeryShortCode_one_fifth extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_one_fifth_last extends WPBakeryShortCodesContainer {
	    }		    
	    class WPBakeryShortCode_one_fourth extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_one_fourth_last extends WPBakeryShortCodesContainer {
	    }		    
	    class WPBakeryShortCode_one_third extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_one_third_last extends WPBakeryShortCodesContainer {
	    }		    
	    class WPBakeryShortCode_one_half extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_one_half_last extends WPBakeryShortCodesContainer {
	    }		    
	    class WPBakeryShortCode_two_thirds extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_two_thirds_last extends WPBakeryShortCodesContainer {
	    }		    
	    class WPBakeryShortCode_three_fourth extends WPBakeryShortCodesContainer {
	    }	    
	    class WPBakeryShortCode_three_fourth_last extends WPBakeryShortCodesContainer {
	    }		     
	    class WPBakeryShortCode_accordion extends WPBakeryShortCodesContainer {
	    }
	    class WPBakeryShortCode_tabset extends WPBakeryShortCodesContainer {
	    } 		     	    		    	      		     		    		     	 	    
	    class WPBakeryShortCode_testimonial_wrap extends WPBakeryShortCodesContainer {
	    } 
	    class WPBakeryShortCode_video_left extends WPBakeryShortCodesContainer {
	    }  
	    class WPBakeryShortCode_video_right extends WPBakeryShortCodesContainer {
	    } 
	    class WPBakeryShortCode_karma_list extends WPBakeryShortCodesContainer {
	    } 	              
	}

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_karma_builder_testimonial_1_slide extends WPBakeryShortCode {
	    }
	    class WPBakeryShortCode_karma_builder_testimonial_2_slide extends WPBakeryShortCode {
	    }
	    class WPBakeryShortCode_karma_builder_tab_1_content extends WPBakeryShortCode {
	    }	
	    class WPBakeryShortCode_karma_builder_tab_2_content extends WPBakeryShortCode {
	    }	   
	    class WPBakeryShortCode_karma_builder_tab_3_content extends WPBakeryShortCode {
	    }	
	    class WPBakeryShortCode_karma_builder_accordion_panel extends WPBakeryShortCode {
	    }          	    
        // Single grow box class
	    class WPBakeryShortCode_karma_builder_single_grow_box extends WPBakeryShortCode {
	    } 
	    class WPBakeryShortCode_list_item extends WPBakeryShortCode {
	    } 
	    class WPBakeryShortCode_slide extends WPBakeryShortCode {
	    }
	    class WPBakeryShortCode_tab extends WPBakeryShortCode {
	    } 
	    class WPBakeryShortCode_testimonial extends WPBakeryShortCode {
	    } 	    	    
	    class WPBakeryShortCode_video_frame extends WPBakeryShortCode {
	    }  
	    class WPBakeryShortCode_video_text extends WPBakeryShortCode {
	    } 
	    class WPBakeryShortCode_karma_list_item extends WPBakeryShortCode {
	    }	    	              	    
	}		   		  		

}
add_action('after_setup_theme','karma_builder_extend_vc_class');

// AJAX stuff for getting the url of an image based on its wp id.
add_action('admin_footer', 'karma_builder_print_get_image_URL_function');

function karma_builder_print_get_image_URL_function() {
    ?>
    <script>
    window.karma_builder_get_image_url = function (id, cb) {
        var data = {
            'action': 'karma_builder_get_image_url',
            'image_id': id
        };
        jQuery.get(ajaxurl, data, function(response) {
            cb(response);
        });
    }
    </script>
    <?php
}
add_action('wp_ajax_karma_builder_get_image_url', 'karma_builder_get_image_url_callback');
function karma_builder_get_image_url_callback() {
    if (!isset($_GET['image_id'])) {
        echo "";
        return;
    }
    $id = $_GET['image_id'];
    $size = "large";
    $url = wp_get_attachment_image_src($id, $size);
    $url = $url[0];
    echo $url;
    die();
}


//helper function
function karma_builder_generate_blog_image($image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url){


//show video embed only if there is featured video url.
if(!empty($video_url)){
$embed_video = apply_filters('the_content', "[embed width=\"538\" height=\"418\"]".$video_url."[/embed]");
$html = $embed_video;
return $html;
} 

//began normal layout.

if(!empty($image_src)): //there is either post thumbnail of external image

//determine which div css class to use.
if($blog_image_frame == 'shadow'){
	$html .= '<div class="shadow_img_frame tt-blog-featured">';
}else{
	$html .= '<div class="modern_img_frame tt-blog-featured">';
}

$html.= '<div class="img-preload">';

//determine link to post or link to external site.
//added checks for single.php @since version 2.6
if ($linkpost == ''){
    //there is no link to external url
	if(!is_single()){
	//if not single we link to post
	$truethemeslink = $permalink;
	}else{
	//else we link to nothing;
	$truethemeslink = '';
	}
	
}elseif($linkpost!=''){
    //there is an external url link, we assign it.
	$truethemeslink = $linkpost;
	
}else{
    //do nothing, this is for closing the if statement only.
}

//get post title for image title. 
global $post;
$title = get_the_title($post->ID);

if(!empty($truethemeslink))://show image link only if there is a link assigned.
//start link
$html .= "<a href='$truethemeslink' title='$title' class='attachment-fadeIn'>";
endif;

//image
$html .= "<img src='$image_src' width='$image_width' height='$image_height' alt='$title' />";

if(!empty($truethemeslink)): //show image link only if there is a link assigned.
//close link
$html.= "</a>";
endif;

//close divs
$html .= "</div><!-- END img-preload -->";
$html .= "</div><!-- END post_thumb -->";


endif;

//that's all!
return $html;
}


// AJAX stuff for getting the url of an image based on its wp id.
add_action('admin_footer', 'karma_print_get_image_URL_function');

function karma_print_get_image_URL_function() {
    ?>
    <script>
    window.karma_get_image_url = function (id, cb) {
        var data = {
            'action': 'karma_get_image_url',
            'image_id': id
        };
        jQuery.get(ajaxurl, data, function(response) {
            cb(response);
        });
    }
    </script>
    <?php
}
add_action('wp_ajax_karma_get_image_url', 'karma_get_image_url_callback');
function karma_get_image_url_callback() {
    if (!isset($_GET['image_id'])) {
        echo "";
        return;
    }
    $id = $_GET['image_id'];
    $size = "large";
    $url = wp_get_attachment_image_src($id, $size);
    $url = $url[0];
    echo $url;
    die();
}
