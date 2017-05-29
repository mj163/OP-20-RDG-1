<?php 
defined( 'ABSPATH' ) or die( 'Access Denied!' );
require_once REMS_PLUGIN_DIR.'/admin/admin.php';
require_once REMS_PLUGIN_DIR.'/class/class-emgt-db.php';
require_once REMS_PLUGIN_DIR.'/real_estate_functions.php';
require_once REMS_PLUGIN_DIR.'/emgt_ajax_functions.php';
require_once REMS_PLUGIN_DIR.'/class/class-emgt-tasks.php';
require_once REMS_PLUGIN_DIR.'/class/emgt_payment_class.php';

add_action( 'admin_print_scripts-post-new.php', 'emgt_post_admin_script');
add_action( 'admin_print_scripts-post.php', 'emgt_post_admin_script' );


function emgt_post_admin_script() {
	global $post_type;
	if( 'emgt_add_listing' == $post_type ) 
	{ 
		wp_enqueue_style('ct-img-up',REMS_PLUGIN_URL.'/css/custom-image-uploader.css');
		wp_enqueue_style('geo-map',REMS_PLUGIN_URL.'/css/geocode.css');
		wp_enqueue_style('jq-validat-eng',REMS_PLUGIN_URL.'/lib/validationEngine/css/validationEngine.jquery.css');
		wp_enqueue_script('jq-lib',REMS_PLUGIN_URL.'/js/jquery-1.11.1.js');		
		wp_enqueue_script( 'img-uploader',REMS_PLUGIN_URL.'/js/custom-image-uploader.js',array("jquery"),false,true);
		wp_enqueue_script('google-geocode-js',REMS_PLUGIN_URL.'/js/geocode.js',array("jquery"),false,true);
		wp_enqueue_script('jq-val-en',REMS_PLUGIN_URL.'/lib/validationEngine/js/languages/jquery.validationEngine-en.js');
		wp_enqueue_script('jq-validate-script',REMS_PLUGIN_URL.'/lib/validationEngine/js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('ad-validation',REMS_PLUGIN_URL.'/js/custom_post_validation.js');
		wp_enqueue_style('fa-aw',REMS_PLUGIN_URL.'/css/font-awesome.min.css');
		
		wp_enqueue_script('gl-js',"https://maps.googleapis.com/maps/api/js?key=AIzaSyDaf6Sl-FNf_pbN1hyJMhN0d2zPl8kZq4U&signed_in=true&libraries=places&callback=initMap",array("jquery"),false,true);
	}
		
		?>
	<!--	<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyDaf6Sl-FNf_pbN1hyJMhN0d2zPl8kZq4U&signed_in=true&libraries=places&callback=initMap"
        async defer></script> -->
<?php 	}
	
function real_estate_scripts_initialize()
{
	wp_enqueue_media();

	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "emgt_fields")
	{
		wp_enqueue_style('bootstrap-min',REMS_PLUGIN_URL.'/lib/bootstrap-vertical-tabs-master/bootstrap.min.css');
		wp_enqueue_style('bootstrap-min-v-tabs',REMS_PLUGIN_URL.'/css/bootstrap.vertical-tabs.min.css');
	}
	else{
		wp_enqueue_style('bootstrap-min-356',REMS_PLUGIN_URL.'/css/bs3.5.6/bootstrap.min-backend.css');
	}
		wp_enqueue_style('style',REMS_PLUGIN_URL.'/css/style.css');
		
		
	// wp_enqueue_style('bootstrap',REMS_PLUGIN_URL.'/css/bs3.5.6/bootstrap.css');
	wp_enqueue_style('realestate',REMS_PLUGIN_URL.'/css/real_estate.css');
	
	wp_enqueue_style('fa',REMS_PLUGIN_URL.'/css/font-awesome.css');
	wp_enqueue_style('fa-min',REMS_PLUGIN_URL.'/css/font-awesome.min.css');
	wp_enqueue_style('datatable-min',REMS_PLUGIN_URL.'/css/jquery.dataTables.min.css');
	wp_enqueue_style('sortable-ui',REMS_PLUGIN_URL.'/js/sortable/jquery-ui.css');
	wp_enqueue_style('jq-datetime-style',REMS_PLUGIN_URL.'/css/bootstrap-datetimepicker.min.css');
	wp_enqueue_style('popup-ui',REMS_PLUGIN_URL.'/css/popup.css');
	wp_enqueue_style('custom-style',REMS_PLUGIN_URL.'/css/custom-style.css');
	// wp_enqueue_style('bs-vertical-tabs',REMS_PLUGIN_URL.'/lib/bootstrap-vertical-tabs-master/bootstrap.vertical-tabs.css');
	wp_enqueue_style('jq-validation',REMS_PLUGIN_URL.'/lib/validationEngine/css/validationEngine.jquery.css');
	
	
	
	wp_enqueue_script('jq-lib',REMS_PLUGIN_URL.'/js/jquery-1.11.1.js');
	wp_enqueue_script('jq-mnt',REMS_PLUGIN_URL.'/js/moment.js');
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "emgt_estate")
	{
		wp_enqueue_style('cal-css',REMS_PLUGIN_URL.'/css/fullcalendar.css');
			// wp_enqueue_script('smgt-calender', plugins_url( '/js/moment.min.js', __FILE__ ), array( 'jquery' ), '4.1.1', true );
		// wp_enqueue_script('cal-min-script',REMS_PLUGIN_URL.'/js/fullcalendar.min.js',array('jquery'),false,true);
		wp_enqueue_script('cal-script',REMS_PLUGIN_URL.'/js/fullcalendar.js',array('jquery'),false,true);
	}
	wp_enqueue_script('jq-datetime',REMS_PLUGIN_URL.'/js/bootstrap-datetimepicker.min.js');
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == "emgt_fields")
	{
		wp_enqueue_script('bootstrap-min-js-3.2.2',REMS_PLUGIN_URL.'/js/bs3.2.2/bootstrap.min.js',array('jquery'));
	}
	else{
		wp_enqueue_script('bootstrap-min-js',REMS_PLUGIN_URL.'/js/bs3.5.6/bootstrap.min.js',array('jquery'));
	}
	
	// wp_enqueue_script('bootstrap-js',REMS_PLUGIN_URL.'/js/bs3.5.6/bootstrap.js',array('jquery')); //modal popup issue if uncomment
	wp_enqueue_script('datatable-min-js',REMS_PLUGIN_URL.'/js/jquery.dataTables.min.js');
	wp_enqueue_script('sortable-js',REMS_PLUGIN_URL.'/js/sortable/jquery-ui.js');
	// wp_enqueue_script('bootstrap-tool',REMS_PLUGIN_URL.'/js/umd/tooltip.js');	
	wp_enqueue_script('ajax-script',REMS_PLUGIN_URL.'/js/ajax.js',array('jquery'));	
	wp_enqueue_script('jscolor-picker',REMS_PLUGIN_URL.'/js/jscolor.js',array('jquery'));	
	wp_enqueue_script('addrule',REMS_PLUGIN_URL.'/js/jquery.addrule.js',array('jquery'));	
	
	// wp_enqueue_script('jq-validate-lib',REMS_PLUGIN_URL.'/lib/validationEngine/js/jquery-1.8.2.min.js');
	wp_enqueue_script('jq-val-en',REMS_PLUGIN_URL.'/lib/validationEngine/js/languages/jquery.validationEngine-en.js');
	wp_enqueue_script('jq-validate-script',REMS_PLUGIN_URL.'/lib/validationEngine/js/jquery.validationEngine.js',array('jquery'));
	wp_enqueue_script('jq-single-image-upload',REMS_PLUGIN_URL.'/js/image_upload_single.js');
	wp_enqueue_script('jq-validate-script',REMS_PLUGIN_URL.'/lib/validationEngine/js/fullcalendar.min.js',array('jquery'));
	wp_localize_script('ajax-script','emgt',array( 'ajax' => admin_url( 'admin-ajax.php' ) ),array('jquery'));	
	
}

if(isset($_REQUEST['page']) && mb_substr($_REQUEST['page'],0,5) == 'emgt_')
{
	add_action('admin_enqueue_scripts','real_estate_scripts_initialize');	
}


function real_estate_frontend_scripts_load()
{	
	global $post;
	if($post != null) :
	$pl_page = get_option("emgt_property_list_page");
	
	if($post->post_type == "emgt_add_listing" || $post->ID == $pl_page || is_page())
	{ 
		wp_enqueue_media();
		if(is_single())
		{
			wp_enqueue_script('gl-js23',"https://maps.googleapis.com/maps/api/js?key=AIzaSyDaf6Sl-FNf_pbN1hyJMhN0d2zPl8kZq4U&signed_in=true&libraries=places&callback=initMap",array("jquery"),false,true);
		}	
	$template = get_option('template');	
	if($template=="twentyfifteen")
	{
		wp_enqueue_style('fifteen-custom',REMS_PLUGIN_URL.'/css/twentyfifteen.css');
	}
	if($template=="twentyfourteen")
	{
		wp_enqueue_style('fourteen-custom',REMS_PLUGIN_URL.'/css/twentyfourteen.css');
	}
	if($template=="twentysixteen")
	{
		wp_enqueue_style('sixteen-custom',REMS_PLUGIN_URL.'/css/twentysixteen.css');
	}
	
	if($template == "wprealty" || $template=="twentysixteen" || $template=="twentyfifteen" || $template=="twentyfourteen")
	{		
		// wp_enqueue_style('bootstrap-min-356',REMS_PLUGIN_URL.'/css/bs3.5.6/bootstrap_custom.css');
		// wp_enqueue_style('bootstrap-min-356',REMS_PLUGIN_URL.'/css/bs3.5.6/bootstrap.min-Copy.css');
		wp_enqueue_style('custom-glyphicon',REMS_PLUGIN_URL.'/css/bs3.5.6/custom_glyphicon.css');
		wp_enqueue_style('grid-12',REMS_PLUGIN_URL.'/css/bs3.5.6/grid12.css');		
		wp_enqueue_style('bs-min',REMS_PLUGIN_URL.'/css/bs3.5.6/bootstrap.min.css');
		// wp_enqueue_style('bs-custom',REMS_PLUGIN_URL.'/css/bs3.5.6/bs_custom_style.css');
		wp_enqueue_script('bootstrap-min-js',REMS_PLUGIN_URL.'/js/bs3.5.6/bootstrap.min.js',array('jquery'));
	}
		// wp_enqueue_style('style',REMS_PLUGIN_URL.'/css/style.css');		
		// wp_enqueue_style('realestate',REMS_PLUGIN_URL.'/css/real_estate.css');
	
		wp_enqueue_style('fa',REMS_PLUGIN_URL.'/css/font-awesome.css');
		wp_enqueue_style('fa-min',REMS_PLUGIN_URL.'/css/font-awesome.min.css');
		wp_enqueue_style('datatable-min',REMS_PLUGIN_URL.'/css/jquery.dataTables.min.css');
		wp_enqueue_style('sortable-ui',REMS_PLUGIN_URL.'/js/sortable/jquery-ui.css');
		wp_enqueue_style('jq-datetime-style',REMS_PLUGIN_URL.'/css/bootstrap-datetimepicker.min.css');
		wp_enqueue_style('popup-ui',REMS_PLUGIN_URL.'/css/popup.css');
		// wp_enqueue_style('custom-style',REMS_PLUGIN_URL.'/css/custom-style.css');
		// wp_enqueue_style('bs-vertical-tabs',REMS_PLUGIN_URL.'/lib/bootstrap-vertical-tabs-master/bootstrap.vertical-tabs.css');
		wp_enqueue_style('jq-validation',REMS_PLUGIN_URL.'/lib/validationEngine/css/validationEngine.jquery.css');
		wp_enqueue_style('fr-end',REMS_PLUGIN_URL.'/css/frontend_style.css');
		wp_enqueue_style('fancy-box',REMS_PLUGIN_URL.'/css/jquery.fancybox.css');	
		// wp_enqueue_style('rev-sldier',REMS_PLUGIN_URL.'/css/settings.css');	
		wp_enqueue_script('addrule',REMS_PLUGIN_URL.'/js/jquery.addrule.js',array('jquery'));	
		
		wp_enqueue_script('jq-lib',REMS_PLUGIN_URL.'/js/jquery-1.11.1.js');
		wp_enqueue_script('jq-mnt',REMS_PLUGIN_URL.'/js/moment.js');
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == "emgt_estate")
		{
			wp_enqueue_style('cal-css',REMS_PLUGIN_URL.'/css/fullcalendar.css');
				// wp_enqueue_script('smgt-calender', plugins_url( '/js/moment.min.js', __FILE__ ), array( 'jquery' ), '4.1.1', true );
			// wp_enqueue_script('cal-min-script',REMS_PLUGIN_URL.'/js/fullcalendar.min.js',array('jquery'),false,true);
			wp_enqueue_script('cal-script',REMS_PLUGIN_URL.'/js/fullcalendar.js',array('jquery'),false,true);
		}
		wp_enqueue_script('jq-datetime',REMS_PLUGIN_URL.'/js/bootstrap-datetimepicker.min.js');
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == "emgt_fields")
		{
			wp_enqueue_script('bootstrap-min-js-3.2.2',REMS_PLUGIN_URL.'/js/bs3.2.2/bootstrap.min.js',array('jquery'));
		}
		else{
			// wp_enqueue_script('bootstrap-min-js',REMS_PLUGIN_URL.'/js/bs3.5.6/bootstrap.min.js',array('jquery'));
		}
		wp_enqueue_script('fancy-box-js',REMS_PLUGIN_URL.'/js/jquery.fancybox.js');
		// wp_enqueue_script('bootstrap-js',REMS_PLUGIN_URL.'/js/bs3.5.6/bootstrap.js',array('jquery')); //modal popup issue if uncomment
		wp_enqueue_script('datatable-min-js',REMS_PLUGIN_URL.'/js/jquery.dataTables.min.js');
		wp_enqueue_script('sortable-js',REMS_PLUGIN_URL.'/js/sortable/jquery-ui.js');
		// wp_enqueue_script('bootstrap-tool',REMS_PLUGIN_URL.'/js/umd/tooltip.js');	
		wp_enqueue_script('ajax-script',REMS_PLUGIN_URL.'/js/ajax.js',array('jquery'));	
		// wp_enqueue_script('r-slider',REMS_PLUGIN_URL.'/js/jquery.themepunch.revolution.min.js',array('jquery'));	
		// wp_enqueue_script('revo-slider',REMS_PLUGIN_URL.'/js/jquery.themepunch.tools.min.js',array('jquery'));	
		
		
		// wp_enqueue_script('jq-validate-lib',REMS_PLUGIN_URL.'/lib/validationEngine/js/jquery-1.8.2.min.js');
		wp_enqueue_script('jq-val-en',REMS_PLUGIN_URL.'/lib/validationEngine/js/languages/jquery.validationEngine-en.js');
		wp_enqueue_script('jq-validate-script',REMS_PLUGIN_URL.'/lib/validationEngine/js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('jq-single-image-upload',REMS_PLUGIN_URL.'/js/image_upload_single.js');
		wp_enqueue_script('jq-validate-script',REMS_PLUGIN_URL.'/lib/validationEngine/js/fullcalendar.min.js',array('jquery'));
		wp_localize_script('ajax-script','emgt',array( 'ajax' => admin_url( 'admin-ajax.php' ) ),array('jquery'));	
	}
	endif;
}

add_action("wp_enqueue_scripts","real_estate_frontend_scripts_load");
add_filter( 'wp_authenticate_user', 'emgt_login_activation_hash_check', 10, 2 );

function emgt_login_activation_hash_check( $user, $password ) {
     global $wpdb;
	 $table_users=$wpdb->prefix.'users';
	 $user_id =  $user->ID; // prints the id of the user 
	  
	if( get_user_meta($user_id, 'emgt_hash', true) )
	{			
		$WP_Error = new WP_Error();		
		$WP_Error ->add( 'broke', __( "<strong>ERROR</strong>:Your account not activated yet!", "school-mgt" ) );	
		return $WP_Error;		
	}
	else
	{
		return $user;
	}	
}