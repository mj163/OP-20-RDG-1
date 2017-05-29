<?php

require_once REMS_PLUGIN_DIR."/class/class-emgt-db.php";
require_once REMS_PLUGIN_DIR.'/class/emgt-plan-check.php';
require_once REMS_PLUGIN_DIR."/class/emgt_custom_fields.php";

add_action( 'init', 'emgt_additional_features_taxanomy', 2 );
add_action( 'init', 'emgt_add_listing_page');

########### META BOX HOOKS ########
add_action("add_meta_boxes_emgt_add_listing","add_meta_basics",2);
add_action("add_meta_boxes_emgt_add_listing","add_meta_advance",2);
add_action("add_meta_boxes_emgt_add_listing","add_meta_optional",2);
if(get_option("gallery_field_show"))
{
	add_action("add_meta_boxes_emgt_add_listing","emgt_add_meta_gallery",2);	
}
add_action("add_meta_boxes_emgt_add_listing","add_meta_attachement",2);
add_action("add_meta_boxes_emgt_add_listing","add_meta_compliances",2);
add_action("add_meta_boxes_emgt_add_listing","add_meta_google_map",2);
########### META BOX HOOKS ########

##################  SAVE META DATA and ADD Image COLUMN   ##################################
add_action('save_post_emgt_add_listing', 'emgt_save_post',2);
add_filter('manage_emgt_add_listing_posts_columns', 'emgt_columns_head');
add_action('manage_emgt_add_listing_posts_custom_column', 'emgt_columns_content', 2, 2);
###########################################################################################


function emgt_add_listing_page() {	
    $labels = array(
		'name'               => _x( 'Property Listing', 'post type general name', 'estate-emgt' ),
		'singular_name'      => _x( 'property-listing', 'post type singular name', 'estate-emgt' ),
		'menu_name'          => _x( 'Property Listing', 'admin menu', 'estate-emgt' ),
		'name_admin_bar'     => _x( 'Property Listing', 'add new on admin bar', 'estate-emgt' ),
		'add_new'            => _x( 'Add New', 'Property', 'estate-emgt' ),
		'add_new_item'       => __( 'Add New Property', 'estate-emgt' ),
		'new_item'           => __( 'New Property', 'estate-emgt' ),
		'edit_item'          => __( 'Edit Property', 'estate-emgt' ),
		'view_item'          => __( 'View Property', 'estate-emgt' ),
		'all_items'          => __( 'Property Management', 'estate-emgt' ),
		'search_items'       => __( 'Search Properties', 'estate-emgt' ),
		'parent_item_colon'  => __( 'Parent Properties:', 'estate-emgt' ),
		'not_found'          => __( 'No Properties found.', 'estate-emgt' ),
		'not_found_in_trash' => __( 'No Properties found in Trash.', 'estate-emgt' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'estate-emgt' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		// 'show_in_menu'       => 'emgt_estate',
		'show_in_nav_menus'  => true,
		'show_in_admin_bar'  => true,		
		'menu_icon'          => 'dashicons-location',
		'query_var'          => true,
		// 'rewrite'         => array( 'slug' => 'book' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,		
		'menu_position'       => 19,
		// 'register_meta_box_cb' => 'add_meta_basics',
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail')
		// 'taxonomies'          => array( 'emgt_baths','emgt_baths','emgt_beds','emgt_status','emgt_city')
	);
    register_post_type( 'emgt_add_listing', $args );
}

#############################################################################################################
###############################################################################################################

function emgt_bath_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Bathrooms', 'estate-emgt' ),
		'singular_name'     => _x( 'Bathrooms', 'estate-emgt' ),
		'search_items'      => __( 'Search Bathrooms' ),
		'all_items'         => __( 'All Bathrooms' ),
		'parent_item'       => __( 'Parent Bathrooms' ),
		'parent_item_colon' => __( 'Parent Bathrooms:' ),
		'edit_item'         => __( 'Edit Bathrooms' ),
		'update_item'       => __( 'Update Bathrooms' ),
		'add_new_item'      => __( 'Add New Bathrooms' ),
		'new_item_name'     => __( 'New Bathrooms Name' ),
		'menu_name'         => __( 'Bathrooms' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'Bathrooms' ),
	);

register_taxonomy('emgt_baths', 'emgt_add_listing',$args);

}

#############################################################################################################
###############################################################################################################

function emgt_beds_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Bedrooms', 'estate-emgt' ),
		'singular_name'     => _x( 'Bedrooms', 'estate-emgt' ),
		'search_items'      => __( 'Search Bedrooms' ),
		'all_items'         => __( 'All Bedrooms' ),
		'parent_item'       => __( 'Parent Bedrooms' ),
		'parent_item_colon' => __( 'Parent Bedrooms:' ),
		'edit_item'         => __( 'Edit Bedrooms' ),
		'update_item'       => __( 'Update Bedrooms' ),
		'add_new_item'      => __( 'Add New Bedrooms' ),
		'new_item_name'     => __( 'New Bedrooms Name' ),
		'menu_name'         => __( 'Bedrooms' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'Beds' ),
	);

register_taxonomy('Beds', 'emgt_add_listing',$args);

}



#############################################################################################################
###############################################################################################################



function emgt_status_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Status', 'estate-emgt' ),
		'singular_name'     => _x( 'Status', 'estate-emgt' ),
		'search_items'      => __( 'Search Status' ),
		'all_items'         => __( 'All Status' ),
		'parent_item'       => __( 'Parent Status' ),
		'parent_item_colon' => __( 'Parent Status:' ),
		'edit_item'         => __( 'Edit Status' ),	
		'update_item'       => __( 'Update Status' ),
		'add_new_item'      => __( 'Add New Status' ),
		'new_item_name'     => __( 'New Status Name' ),
		'menu_name'         => __( 'Status' )		
	);

	$args = array(
		'hierarchical'      => true,	
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'Status' ),
	);

register_taxonomy('emgt_status', 'emgt_add_listing',$args);

}

#############################################################################################################
###############################################################################################################



function emgt_city_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'City', 'estate-emgt' ),
		'singular_name'     => _x( 'City', 'estate-emgt' ),
		'search_items'      => __( 'Search City' ),
		'all_items'         => __( 'All City' ),
		'parent_item'       => __( 'Parent City' ),
		'parent_item_colon' => __( 'Parent City:' ),
		'edit_item'         => __( 'Edit City' ),
		'update_item'       => __( 'Update City' ),
		'add_new_item'      => __( 'Add New City' ),
		'new_item_name'     => __( 'New City Name' ),
		'menu_name'         => __( 'City' ),
	);

	$args = array(
		'hierarchical'      => false,
		'slug' => 'emgt_city',
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'City' ),
	);

register_taxonomy('emgt_city', 'emgt_add_listing',$args);

}


#############################################################################################################
###############################################################################################################



function emgt_state_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'State', 'estate-emgt' ),
		'singular_name'     => _x( 'State', 'estate-emgt' ),
		'search_items'      => __( 'Search State' ),
		'all_items'         => __( 'All State' ),
		'parent_item'       => __( 'Parent State' ),
		'parent_item_colon' => __( 'Parent State:' ),
		'edit_item'         => __( 'Edit State' ),
		'update_item'       => __( 'Update State' ),
		'add_new_item'      => __( 'Add New State' ),
		'new_item_name'     => __( 'New State Name' ),
		'menu_name'         => __( 'State' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'City' ),
	);

register_taxonomy('emgt_state', 'emgt_add_listing',$args);

}

#############################################################################################################
###############################################################################################################


function emgt_Zipcode_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Zipcode', 'estate-emgt' ),
		'singular_name'     => _x( 'Zipcode', 'estate-emgt' ),
		'search_items'      => __( 'Search Zipcode' ),
		'all_items'         => __( 'All Zipcode' ),
		'parent_item'       => __( 'Parent Zipcode' ),
		'parent_item_colon' => __( 'Parent Zipcode:' ),
		'edit_item'         => __( 'Edit Zipcode' ),
		'update_item'       => __( 'Update Zipcode' ),
		'add_new_item'      => __( 'Add New Zipcode' ),
		'new_item_name'     => __( 'New Zipcode Name' ),
		'menu_name'         => __( 'Zipcode' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'City' ),
	);

register_taxonomy('emgt_zipcode', 'emgt_add_listing',$args);

}

#############################################################################################################
###############################################################################################################



function emgt_additional_features_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Additional Features', 'estate-emgt' ),
		'singular_name'     => _x( 'Additional Features', 'estate-emgt' ),
		'search_items'      => __( 'Search Additional Features' ),
		'all_items'         => __( 'All Additional Features' ),
		'parent_item'       => __( 'Parent Additional Features' ),
		'parent_item_colon' => __( 'Parent Additional Features:' ),
		'edit_item'         => __( 'Edit Additional Features' ),
		'update_item'       => __( 'Update Additional Features' ),
		'add_new_item'      => __( 'Add New Additional Features' ),
		'new_item_name'     => __( 'New Additional Features Name' ),
		'menu_name'         => __( 'Additional Features' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'City' ),
	);

register_taxonomy('emgt_features', 'emgt_add_listing',$args);

}

#############################################################################################################
###############################################################################################################



function emgt_types_taxanomy() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Property Type', 'estate-emgt' ),
		'singular_name'     => _x( 'Property Type', 'estate-emgt' ),
		'search_items'      => __( 'Search Property Types' ),
		'all_items'         => __( 'All Property Types' ),
		'parent_item'       => __( 'Parent Property Types' ),
		'parent_item_colon' => __( 'Parent Property Types:' ),
		'edit_item'         => __( 'Edit Property Types' ),
		'update_item'       => __( 'Update Property Types' ),
		'add_new_item'      => __( 'Add New Property Types' ),
		'new_item_name'     => __( 'New Property Types' ),
		'menu_name'         => __( 'Property Types' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'City' ),
	);

register_taxonomy('emgt_types', 'emgt_add_listing',$args);

}

####################     META BOX FUNCTIONS              #########################################################################################
###########################################               META BOX FUNCTIONS           ####################################################################


	function add_meta_basics()
	{
			add_meta_box('emgt_meta_basics_box',
						'Basic Details','emgt_meta_callback_basic','emgt_add_listing','normal','default');			
	}
	function emgt_meta_callback_basic($post, $metabox)
	{ 		
		$basic_stored_meta = get_post_meta($post->ID);
		echo "<input type='hidden' name='emgtfld_emgt_meta_nonce' value='".wp_create_nonce(basename(__FILE__))."'>";
		$fields = new Emgt_fields;
		$cs_fields = $fields->emgt_show_sections(1);		
	}
	
######################################################################################       
######################################################################################  

	function add_meta_advance()
	{
			add_meta_box('emgt_meta_advance',
						'Advance','emgt_meta_callback_advance','emgt_add_listing','normal','default');			
	}
	function emgt_meta_callback_advance($post,$metabox)
	{		
		$fields = new Emgt_fields;
		$cs_fields = $fields->emgt_show_sections(2);			
	}	

######################################################################################       
######################################################################################

	function add_meta_optional()
	{
			add_meta_box('emgt_meta_optional',
						'Optional','emgt_meta_callback_optional','emgt_add_listing','normal','default');			
	}
	function emgt_meta_callback_optional($post, $metabox)
	{ 
		$fields = new Emgt_fields;
		$cs_fields = $fields->emgt_show_sections(3);	
	}

######################################################################################       
######################################################################################
	function emgt_add_meta_gallery()
	{
			add_meta_box('emgt_meta_gallery',
						'Gallery','emgt_meta_callback_gallery','emgt_add_listing','normal','default');			
	}
	function emgt_meta_callback_gallery()
	{
		global $post;
	$gallery_data = get_post_meta( $post->ID, '4_emgtfld_gallery', true );
	
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'noncename_so_14445904' );
	?> 
<div id="dynamic_form"> 
    <div id="field_wrap_1">
    <?php 
    if ( isset( $gallery_data['image_url'] ) && !empty($gallery_data['image_url'][0]) ) 
    { 		
        // for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ ) 		 
			foreach($gallery_data['image_url'] as $img)
			{
				if($img != "")
				{ ?>				 
					<div class="field_row">
			 
					  <div class="field_left">
						<div class="form_field">
						  <label><?php _e("Image URL","estate-emgt");?></label>
						  <input type="text"
								 class="meta_image_url validate[required]"
								 name="4_emgtfld_gallery[image_url][]"
								 value="<?php esc_html_e($img);//esc_html_e( $gallery_data['image_url'][$i] ); ?>"
						  />
						</div>
					  </div>
			 
					  <div class="field_right image_wrap">
						<img src="<?php esc_html_e($img);//esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="65" width="65" />
					  </div>
			 
					  <div class="field_right">
						<input class="button" type="button" value="<?php _e("Select Image","estate-emgt");?>" onclick="add_image(this)" /><br />
						<input class="button" type="button" value="<?php _e("Remove","estate-emgt");?>" onclick="remove_field(this)" />
					  </div>
			 
					  <div class="clear" /></div> 
					</div>
			<?php
				} 
			}// endif
    } // endforeach
    ?>
    </div>
 
    <div style="display:none" id="master-row-1">
    <div class="field_row">
        <div class="field_left">
            <div class="form_field">
                <label><?php _e("Image URL","estate-emgt");?></label>
                <input class="meta_image_url validate[required]" value="" type="text" name="4_emgtfld_gallery[image_url][]" />
            </div>
        </div>
        <div class="field_right image_wrap">
        </div> 
        <div class="field_right"> 
            <input type="button" class="button" value="<?php _e("Select Image","estate-emgt");?>" onclick="add_image(this)" />
            <br />
            <input class="button" type="button" value="<?php _e("Remove","estate-emgt");?>" onclick="remove_field(this)" /> 
        </div>
        <div class="clear"></div>
    </div>
    </div>
 
    <div id="add_field_row">
      <input class="button" type="button" value="<?php _e("Add Image","estate-emgt"); ?>" onclick="add_field_row(1)" />
    </div>
 
</div>
 
  <?php
	}	

######################################################################################       
######################################################################################

	function add_meta_compliances()
	{
			add_meta_box('add_meta_compliances',
						'Compliances','emgt_meta_callback_compliances','emgt_add_listing','normal','default');			
	}
	function emgt_meta_callback_compliances()
	{	
		$fields = new Emgt_fields;
		$cs_fields = $fields->emgt_show_sections(5);	
	}		
	
######################################################################################       
######################################################################################
function add_meta_google_map()
	{
			add_meta_box('emgt_google_map',
						'Location','emgt_meta_callback_google_map','emgt_add_listing','normal','high');			
	}
	
	function emgt_meta_callback_google_map()
	{ 
		$show = 1;
		if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
		{
			$db_obj = new Emgt_Db;
			$plan_obj =  new Emgt_PlanCheck($db_obj);
			$plan = $db_obj->emgt_get_rows("emgt_payments","user_id",get_current_user_id());
			
			// $plan = $db_obj->emgt_get_rows("emgt_plans","id",$plan[0]['plan']);		
			// $show = $plan_obj->emgt_plan_feature_check("map",$plan[0]['name']);
			$show = $plan_obj->emgt_plan_feature_check("map",$plan[0]['plan']);				
		}
		global $post;
		if($show)
		{			
			$map_lng = get_post_meta($post->ID,"1_emgtfld_map_longitude",true);	
			$map_lat = get_post_meta($post->ID,"1_emgtfld_map_latitude",true);	
			$map_address = get_post_meta($post->ID,"emgtfld_pac_input",true);
			$edit = 0;
			if($map_lng != "" && $map_lat != "" /* && $map_address != "" */)
			{
				$edit = 1;
			} 

?>
	<input type="hidden" name="1_emgtfld_map_longitude" class="mp_lng" value=<?php echo ($edit) ? $map_lng : ""; ?>>
	<input type="hidden" name="1_emgtfld_map_latitude" class="mp_lat" value=<?php echo ($edit) ? $map_lat : ""; ?>>
		<div class="inside">
				<div style="width:100%;border-top: 1px solid #eee;">
					<div style='width:35%;float:left;'>
					<span style='font-style: italic;line-height: 2em;font-weight: 600;font-size: small;color:gray;'><?php _e("Address","estate-emgt");?></span>
						</div>
					<div style="width: 60%;float: right;">
					<div style="float: left;width: 37%;">
						<span style='font-style: italic;line-height: 2em;font-weight: 600;font-size: small;color:gray;'><?php _e("Longitude","estate-emgt");?></span>
					</div>
					<div>
						<span style='font-style: italic;line-height: 2em;font-weight: 600;font-size: small;color:gray;'><?php _e("Latitude","estate-emgt");?></span>
					</div>
					</div>
				</div>
				<div style="clear: both;">
					<input id="pac-input" name="emgtfld_pac_input" class="" type="text" placeholder="Enter a location" value="<?php echo ($edit) ? $map_address : "";?>">
						<span>&nbsp;&nbsp;<?php _e("OR","estate-emgt"); ?>&nbsp;&nbsp;</span>
					<input type="text" name="1_emgtfld_map_longitude" id ="lang" class="mp_lng" value=<?php echo ($edit) ? $map_lng : ""; ?>>
					<input type="text" name="1_emgtfld_map_latitude" id="lat" class="mp_lat" value=<?php echo ($edit) ? $map_lat : ""; ?>>					
					<button id="show_by_latlang" type="button"><?php _e("Find Coordinate","estate-emgt");?></button>
				</div>
		</div>
		<div id="type-selector" class="controls">
      <input type="radio" name="type" id="changetype-all" checked="checked">
      <label for="changetype-all">All</label>

      <input type="radio" name="type" id="changetype-establishment">
      <label for="changetype-establishment">Establishments</label>

      <input type="radio" name="type" id="changetype-address">
      <label for="changetype-address">Addresses</label>

      <input type="radio" name="type" id="changetype-geocode">
      <label for="changetype-geocode">Geocodes</label>
    </div>
	<div style="height:350px;width:100%;">
		<div id="map"></div>
	</div>
<?php }
	else{
		echo "<span><i class='fa fa-warning'></i> ".__('This feature is not available in subscribed plan.','estate-emgt')."</span>";
	 }
}



######################################################################################       
######################################################################################
function add_meta_attachement ()
{
	add_meta_box('emgt_meta_attachement',
						'Attachment','emgt_meta_callback_attachement','emgt_add_listing','normal','default');
}

function emgt_meta_callback_attachement()
{

if(get_option("video_field_show"))
{	$show = 1;
	if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
	{
		$db_obj = new Emgt_Db;
		$plan_obj =  new Emgt_PlanCheck($db_obj);
		$plan = $db_obj->emgt_get_rows("emgt_payments","user_id",get_current_user_id());
		// $plan = $db_obj->emgt_get_rows("emgt_plans","id",$plan[0]['plan']);		
		// $show = $plan_obj->emgt_plan_feature_check("video",$plan[0]['name']);
		 $show = $plan_obj->emgt_plan_feature_check("video",$plan[0]['plan']);
	}
	if($show)
	{
		global $post;
		$video = get_post_meta($post->ID,"emgtfld_video",true);	
		$edit = 0;
		if($video != "")
		{
			$edit = 1;
		}
		$floor_plan = get_post_meta($post->ID,"5_emgtfld_floor_plan",true);	
	?>
		<div id="emgt_meta_basic_details" class="postbox true">
			<h3><span><?php _e("Video","estate-emgt");?></span></h3>
			<div class="inside">
				<div style="border-top: 1px solid #eee;">
					<label style='font-style: italic;line-height: 2em;font-weight: 600;font-size: small;color:gray;'><?php _e("Video URL","estate-emgt");?></label>
				</div>
				<div>
					<textarea cols="70" name="emgtfld_video" placeholder="<?php _e("Enter youtube embed iframe link","estate-emgt");?>"><?php echo ($edit) ? $video : "";?></textarea>
				</div>
			</div>
		</div>
	<?php
	}
	else{
		echo "<p><span><i class='fa fa-warning'></i> ".__('Add video feature is not available in subscribed plan.','estate-emgt')."</span></p>";
	}
	} 
	
	if(get_option("floor_plan_field_show"))
	{ ?>	
	<div id="emgt_meta_basic_details" class="postbox true">
		<h3><span><?php _e("Floor Plan","estate-emgt");?></span></h3>
		<div class="inside">
			<div style="border-top: 1px solid #eee;">
					<label style='font-style: italic;line-height: 2em;font-weight: 600;font-size: small;color:gray;'><?php _e("Floor Plan Images","estate-emgt");?></label>
			</div>
			<div>
				
				
				
										
						<div id="dynamic_form"> 
							<div id="field_wrap_2">
							<?php 							
							if ( isset( $floor_plan['image_url'] ) && !empty($floor_plan['image_url'][0]) ) 
							{ 		
								// for( $i = 0; $i < count( $floor_plan['image_url'] ); $i++ ) 		 
									foreach($floor_plan['image_url'] as $img)
									{
										if($img != "")
										{ ?>				 
											<div class="field_row">
									 
											  <div class="field_left">
												<div class="form_field">
												  <label><?php _e("Image URL","estate-emgt");?></label>
												  <input type="text"
														 class="meta_image_url validate[required]"
														 name="5_emgtfld_floor_plan[image_url][]"
														 value="<?php esc_html_e($img);//esc_html_e( $floor_plan['image_url'][$i] ); ?>"
												  />
												</div>
											  </div>
									 
											  <div class="field_right image_wrap">
												<img src="<?php esc_html_e($img);//esc_html_e( $floor_plan['image_url'][$i] ); ?>" height="65" width="65" />
											  </div>
									 
											  <div class="field_right cout_size">
												<input class="button" type="button" value="<?php _e("Select Image","estate-emgt");?>" onclick="add_image(this)" /><br />
												<input class="button" type="button" value="<?php _e("Remove","estate-emgt");?>" onclick="remove_field(this)" />
											  </div>
									 
											  <div class="clear" /></div> 
											</div>
									<?php
										} 
									}// endif
							} // endforeach
							?>
							</div>
						 
							<div style="display:none" id="master-row-2">
							<div class="field_row">
								<div class="field_left">
									<div class="form_field">
										<label><?php _e("Image URL","estate-emgt");?></label>
										<input class="meta_image_url validate[required]" value="" type="text" name="5_emgtfld_floor_plan[image_url][]" />
									</div>
								</div>
								<div class="field_right image_wrap">
								</div> 
								<div class="field_right cout_size"> 
									<input type="button" class="button" value="<?php _e("Select Image","estate-emgt");?>" onclick="add_image(this)" />
									<br />
									<input class="button" type="button" value="<?php _e("Remove","estate-emgt");?>" onclick="remove_field(this)" /> 
								</div>
								<div class="clear"></div>
							</div>
							</div>
						 
							<div id="add_field_row">
							  <input class="button" type="button" value="<?php _e("Add Image","estate-emgt"); ?>" onclick="add_field_row(2)" />
							</div>
						 
						</div>
				
				
				
			</div>
		</div>	
		</div>
<?php }
} 


######################################################################################       
######################################################################################

function emgt_save_post($post_id)
{	
	if(@!wp_verify_nonce($_POST['emgtfld_emgt_meta_nonce'], basename(__FILE__)))
	{
		return;
	}	
  else if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
     }
	else if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return false;
		}
	else{
		$field_obj = new Emgt_fields;
		if(!isset($_POST['2_emgtfld_appliances']))
		{
			$_POST['2_emgtfld_appliances'] = "";
		}
		if(!isset($_POST['2_emgtfld_amenities']))
		{
			$_POST['2_emgtfld_amenities'] = "";
		}
		if(!isset($_POST['6_emgtfld_extra']))
		{
			$_POST['6_emgtfld_extra'] = "";
		}
		
		foreach($_POST as $form_field => $value)
		{
			// $fld = "";			
			// preg_match('~_(.*?)_~', $form_field, $output); //new added
			// $fld = $output[1];  //new added
			// $fld = substr($form_field,0,8);
			if (strpos($form_field, "emgtfld") != false || $form_field == "emgtfld_video")  //if($fld == "emgtfld_")
			{	
				$field_obj->emgt_save_posts($form_field,$value);
			}			
		} 
		//set property as featured if applicable to plan
	if(REMS_CURRENT_ROLE != "administrator")
	{	
		$db_obj = new Emgt_Db;
		$plan_obj =  new Emgt_PlanCheck($db_obj);
		$dt = $db_obj->emgt_get_rows("emgt_payments","user_id",get_current_user_id());				
		$visibility = $plan_obj->emgt_plan_feature_check("visibility",$dt[0]['plan']);
		
		if($visibility)
		{
			$check =  $db_obj->emgt_check_unique_field("emgt_featured_property","property_id",$post_id);
			if($check) //if not already added than add as featured
			{
				$db_obj->emgt_insert("emgt_featured_property",array("property_id"=>$post_id,"status"=>1));
			}
		}
	}
	}
}	

	
	function emgt_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
        return $post_thumbnail_img[0];
    }
}
	// ADD NEW COLUMN
	function emgt_columns_head($columns) {	
		echo "<style>						
				table.posts thead th#id{
					width:40px;
				}		
		</style>";
		 $new = array();
		 foreach($columns as $key => $title) {
			if ($key=='title')  // Put the Thumbnail column before the Title column
			{				
				$new['id'] = '#ID';
				$new['featured_image'] = 'Featured Image';
				$new[$key] = $title;
			}
			if ($key=='author')
			{				
				$new['type'] = 'Property Type';
				$new['for'] = 'Property For';
				//$new['country'] = 'Country';
				$new['state'] = 'State';
				$new[$key] = $title;
			}			
			if(get_option("emgt_system_property_approval"))
			{
				if ($key=='date')  // Put the Thumbnail column before the Title column
				{	$new['activation'] = 'Activation';					
					$new['sold_status'] = 'Status';					
					$new[$key] = $title;					
				}
			}
		}	   
		  return $new;
		
		// $defaults['featured_image'] = 'Featured Image';
		// return $defaults;
}
 
// SHOW THE FEATURED IMAGE
	function emgt_columns_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = emgt_get_featured_image($post_ID);
        if ($post_featured_image) {
            // echo the_post_thumbnail( "thumbnail" ); //get_the_post_thumbnail($post_ID, 'thumbnail');
			echo '<img src="'.$post_featured_image.'" width="100" height="100"/>';
        }
		else {
            // NO FEATURED IMAGE, SHOW THE DEFAULT ONE
            echo '<img src="' . REMS_PLUGIN_URL.'/images/no-thumbnail.png" style="height: 100px;width: 100px;"/>';
        }
	}
	 if ($column_name == 'activation') {
		global $post;
		if($post->post_status == "draft")
		{
			echo "<span style='background-color:#DE551D;color:#fff;border-radius:2px;'>&nbsp;&nbsp;". __("Pending","estate-emgt") ."&nbsp;&nbsp;</span>";
		}
		if($post->post_status == "publish")
		{
			echo "<span style='background-color:green;color:#fff;border-radius:2px;'>&nbsp;&nbsp;". __("Activated","estate-emgt") ."&nbsp;&nbsp;</span>";
		}
	 }	 
	 if($column_name == 'sold_status'){
		$get_sold_status = get_post_meta($post_ID,"emgt_sold_status",true);
		if(empty($get_sold_status))
		{
			echo "<span style='background-color:#DE551D;color:#fff;border-radius:2px;'>&nbsp;&nbsp;". __("Unsold","estate-emgt") ."&nbsp;&nbsp;</span>";
		}
		else{
			echo "<span style='background-color:green;color:#fff;border-radius:2px;'>&nbsp;&nbsp;". __("Sold","estate-emgt") ."&nbsp;&nbsp;</span>";
		}
	 }
	  if ($column_name == 'type') {
		echo get_post_meta($post_ID,"1_emgtfld_type",true);
	  }
	  if ($column_name == 'for') {
		echo get_post_meta($post_ID,"1_emgtfld_for",true);
	  }
	  if ($column_name == 'country') {
		// echo get_post_meta($post_ID,"1_emgtfld_country",true);
	  }
	  if ($column_name == 'state'){
		echo get_post_meta($post_ID,"1_emgtfld_state",true);
	  }	 
	  if ($column_name == 'id'){
		echo $post_ID;
	  }
}



if(get_option("emgt_system_property_approval"))
{
	if(defined('REMS_CURRENT_ROLE')):
	if(REMS_CURRENT_ROLE == "administrator")
	{	
		add_filter( 'page_row_actions', 'emgt_ad_active_deactive_links', 10, 2 );
		add_filter( 'post_row_actions', 'emgt_ad_active_deactive_links', 10, 2 );
		
		// add_filter( 'page_row_actions', 'emgt_ad_sold_link', 10, 2 );
		add_filter( 'post_row_actions', 'emgt_ad_sold_link', 10, 2 );
	}
	if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
	{		
		add_filter( 'post_row_actions', 'emgt_ad_delete_link',10,2 );
		add_filter( 'post_row_actions', 'emgt_ad_sold_link',10, 2 );
	}
	endif;
}

function emgt_ad_sold_link($actions,$post)
{ 
	$sold_status = get_post_meta($post->ID,"emgt_sold_status",true);
	if(empty($sold_status) && get_current_user_id() == $post->post_author)
	{
		$actions['inline hide-if-no-js'] ='<a style="color:red;" href="'. get_site_url() .'/wp-admin/edit.php?post_type=emgt_add_listing&sold=true&sid='. $post->ID .'" class="submitdelete" onclick=\'return confirm("Are you sure you want to set this as SOLD?"	);\'>'. esc_html__('Set as Sold', 'estate-emgt') .'</a>';	
	}
	return $actions;
}

function emgt_ad_delete_link($actions, $post ){
	
	if ($post->post_type =="emgt_add_listing" && $post->post_author == get_current_user_id()){
		unset( $actions['trash'] );	
		$actions['delete'] = '<a class="submitdelete" title="Delete this item permanently" href="'. get_delete_post_link( $post->ID,'',true ) .'" onclick=\' return confirm("Are you sure you want to delete this property? It will be removed permanently. ")\'>Delete Permanently</a>';
	}
	return $actions;
}


function emgt_ad_active_deactive_links( $actions, $post ) {
	
	 if ($post->post_type =="emgt_add_listing"){
		// unset( $actions['inline hide-if-no-js']);
		// unset( $actions['view'] );
		if ( get_post_status ( $post->ID ) == 'draft' ){
		$actions['Activate'] ='<a href="'. get_site_url() .'/wp-admin/edit.php?post_type=emgt_add_listing&activated=true&id='. $post->ID .'&status=publish" class="approve_post">'. esc_html__('Activate', 'estate-emgt') .'</a></span></div>';
		}else{
		 // $actions['Deactivate'] ='<a href="'. get_site_url() .'/wp-admin/edit.php?post_type=emgt_add_listing&activated=false&id='. $post->ID .'&status=draft" class="unapprove_post" onclick=\'return confirm("Are you sure you want to deactivate this Ad?"	);\'>'. esc_html__('Deactivate', 'estate-emgt') .'</a></span></div>';	
			// $actions['Sold'] = '<a class="submitdelete" title="Sold" href="'. get_site_url() .'/wp-admin/edit.php?post_type=emgt_add_listing&sold=yes&sid='.$post->ID .'>Sold</a></span></div>';	
		}
		 // $actions['delete'] ='<a href="'. get_site_url() .'/wp-admin/edit.php?post_type=emgt_add_listing&sold=true&sid='. $post->ID .'" class="submitdelete" onclick=\'return confirm("Are you sure you want to set this as SOLD?"	);\'>'. esc_html__('Sold', 'estate-emgt') .'</a>';	

    }
    return $actions;
}

if ( (isset($_GET['activated']) && $_GET['activated'] == 'true') && (isset($_GET['id']) && $_GET['id']!= '') ) {
    add_action('init', 'change_post_status_publish');
}
if ( (isset($_GET['activated']) && $_GET['activated'] == 'false') && (isset($_GET['id']) && $_GET['id']!= '') ) {
    // add_action('init', 'change_post_status_draft');
}

if(isset($_GET['sold']) && $_GET['sold'] == "true")
{
	$sid = $_GET['sid'];
	$sdate = date("Y-m-d H:i:s");
	global $wpdb;
	$tbl = $wpdb->prefix . "emgt_sold_properties";
	$chk_exist = $wpdb->get_var("SELECT COUNT(*) FROM {$tbl} WHERE property_id = {$sid}");
	$chk_exist = intval($chk_exist);
	if($chk_exist == 0)
	{		
		$wpdb->insert($tbl,array("property_id"=>$sid,"sold_date"=>$sdate));
		update_post_meta($sid,"emgt_sold_status",1);
	}
}


if (!function_exists('change_post_status_publish')) {
    function change_post_status_publish(){
        $current_post['ID'] = $_GET['id'];
        $current_post['post_status'] = 'publish';
        wp_update_post($current_post);
	}
}

if (!function_exists('change_post_status_draft')) {
    function change_post_status_draft(){
        $current_post['ID'] = $_GET['id'];
        $current_post['post_status'] = 'draft';
        wp_update_post($current_post);
    }
}

// if(get_option("emgt_system_property_approval"))
// {
	// add_action(  'auto-draft_to_publish',  'on_publish_emgt_property', 10, 1 );
	// function on_publish_emgt_property($post)
	// {
		// global $wpdb;
		// $tb = $wpdb->prefix ."posts";
		// $wpdb->query("UPDATE ".$tb." SET post_status = 'draft' WHERE id = ".$post->ID);
	// }	
// }

if(get_option("emgt_system_property_approval"))
{	
	if(defined('REMS_CURRENT_ROLE')):
	if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
	{
		add_action('save_post', 'emgt_change_post_status'); // to add new property to pending list if approval system is enable
		
		function emgt_change_post_status($post_id) {
		  remove_action('save_post', __FUNCTION__);
		  $post = get_post($post_id); 
		  if($post->post_type == "emgt_add_listing")
		  {
			  // if(isset($_GET['action']) && ($_GET['action'] == "trash" || $_GET['action'] == "delete"))
			  // {
				  // Now, Post will move to trash when click on trash.
			  // }else{ 
					if(isset($_POST['original_post_status']) && $_POST['original_post_status'] == "auto-draft")
					{
						$post->post_status = "draft";
						wp_update_post($post,true);  
					}				 	
				// }
		
		  }	  
		}
	}
	endif;
}
?>