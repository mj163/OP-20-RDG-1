<?php 
defined( 'ABSPATH' ) or die( 'Access Denied!' );

function emgt_install_page()
{ 
	if ( !get_option('emgt_property_list_page') ) {
	$curr_page = array(
	'post_title' => __('Property List', 'estate-emgt'),
	'post_content' => '',
	'post_status' => 'publish',
	'post_type' => 'page',
	'comment_status' => 'closed',
	'ping_status' => 'closed',
	'post_category' => array(1),
	'post_parent' => 0 );

	$curr_created = wp_insert_post( $curr_page );	
	update_option( 'emgt_property_list_page', $curr_created );
	} 
	if(!get_option("emgt_register_page"))
	{ 
		$reg_page = array(
			'post_title' => __('Register', 'estate-emgt'),
			'post_content' => '[emgt_registration_form]',
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_category' => array(1),
			'post_parent' => 0 );

		$curr_created = wp_insert_post( $reg_page );		
		update_option("emgt_register_page","1");
	}
	if(!get_option("emgt_login_page"))
	{ 
		$login_page = array(
			'post_title' => __('Login', 'estate-emgt'),
			'post_content' => '[emgt_login_form]',
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_category' => array(1),
			'post_parent' => 0 );

		$curr_created = wp_insert_post( $login_page );		
		
		update_option("emgt_login_page","1");
	}
}
add_action('init','emgt_install_page');

function get_remote_file($url, $timeout = 30){
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return ($file_contents) ? $file_contents : FALSE;
}


function emgt_property_published($post_id) //$post will be different for both hooks. ID for publish hook and post for state change hook
{  
	$db = new Emgt_Db;	
	$userid = get_current_user_id();
	$plan = $db->emgt_get_rows("emgt_payments","user_id",$userid);
	$plan = $plan[0]['plan'];
	////////////////////////////////PLAN ACTIVATION BLOCK////////////////////////////////////////////////
	$status = Emgt_Plancheck::emgt_plan_status_check($userid,$plan);	
	if($status == "0")
	{
		$period = $db->emgt_get_rows("emgt_plans","id",$plan);
		$validity = $period[0]['plan_validity'];
		$period = $period[0]['plan_period'];
		$act_date = date("Y-m-d H:i:s");
		$date = new DateTime($act_date);
		$date->modify("+{$validity} {$period}");
		$exp_date = $date->format('Y-m-d H:i:s');		
		$data = array("status" => 1,
					"activated_date" => $act_date,
					"expire_date" => $exp_date);
		$db->emgt_db_update("emgt_payments",$data,array("user_id"=>$userid,"plan"=>$plan));
	}
	////////////////////////////////PLAN USAGE UPDATE/INSERT BLOCK/////////////////////////////////////////////////
	$quantity = $db->emgt_get_rows("emgt_plans","id",$plan); //count ads quantity for plan.
	$quantity = $quantity[0]['quantity'];
	$chk = $db->emgt_check_unique_field_multiple("emgt_plan_usage","user_id = {$userid} AND plan = {$plan}");	
	if(!$chk) //check if plan usage already added for this user and plan if yes means count=0 and insert new usage detail.
	{		
		$usage = $db->emgt_get_rows_multiple("emgt_plan_usage","user_id = {$userid} AND plan = {$plan}");
		$data = array(					
					"used_ads" => intval($usage[0]['used_ads'])+1,
					"remaining_ads" => intval($usage[0]['remaining_ads'])-1
					);
		$success = $db->emgt_db_update("emgt_plan_usage",$data,array("user_id" => $userid,"plan" => $plan)); //update used ads usage. 
	}else{
		$remaining = $quantity - 1;
		$data = array(
					"user_id" => $userid,
					"plan" => $plan,
					"used_ads" => 1,
					"remaining_ads" => $remaining
					);
		$success = $db->emgt_insert("emgt_plan_usage",$data);
	}
	////////////////////////////////Ads Table update///////////////////////////////////////////////////////////////	
	$chk = $db->emgt_check_unique_field_multiple("emgt_ads","user_id={$userid} AND post_id = {$post_id}");
	
	if($chk) //if ad is not added than execute block
	{
		$period = $db->emgt_get_rows("emgt_plans","id",$plan);
		$ads_validity = $period[0]['ads_validity'];
		$ads_period = $period[0]['ads_period'];
		$ads_a_date = date("Y-m-d H:i:s");
		$date = new DateTime($ads_a_date);
		$date->modify("+{$ads_validity} {$ads_period}");
		$ads_e_date = $date->format('Y-m-d H:i:s');
		$data = array(
						"user_id" => $userid,
						"post_id" => $post_id,
						"activated_date" => $ads_a_date,
						"expiry_date" => $ads_e_date
					);
		$success = $db->emgt_insert("emgt_ads",$data);
	}
	//////////////////////////////////////////////////////////////////////
}
if(defined('REMS_CURRENT_ROLE')) :
if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
	{
		if(get_option("emgt_system_property_approval") == "0")
		{	
			add_action("publish_emgt_add_listing","emgt_property_published",10, 2 ); 
		}
		// else {	add_action("draft_to_publish","emgt_property_published",10, 1 ); }
	}
endif;

function emgt_pre_publish_check($post)
{ 	
	if($post->post_type == "emgt_add_listing"):
	$db = new Emgt_Db;
	//check is user paid full money for plan?
	$sub_chk = $db->emgt_check_unique_field_multiple("emgt_payments","user_id = ".get_current_user_id()." AND payment_status = 0"  );
	
	if($sub_chk)
	{
		echo "<br>Hello User,Your plan not activated yet due to pending payments. Thank You.<br><br><br>";
		echo "<a href='edit.php?post_type=emgt_add_listing'>Click Here</a> to go back.";
		die;// wp_redirect("edit.php?post_type=emgt_add_listing");
	}
		
	// Check plan is expired or not.	
	$userid = get_current_user_id();	
	$plan_data = $db->emgt_get_rows("emgt_payments","user_id",$userid);
	$plan = $plan_data[0]['plan'];
	// $status = Emgt_Plancheck::emgt_plan_status_check($userid,$plan);
	$c_date = date("Y-m-d H:i:s");
	$x_date = $plan_data[0]["expire_date"];
	if($x_date != null)
	{
		$expire_status = emgt_check_plan_expiry($c_date,$x_date);	
			
		if(!$expire_status)
		{
			echo "<br>Hello User,Your Subscribed Plan validity is expired.Please re-new plan to add more ads.Thank You.<br><br><br>";
			echo "<a href='edit.php?post_type=emgt_add_listing'>Click Here</a> to go back.";
			die;// wp_redirect("edit.php?post_type=emgt_add_listing");
		}
	}
	//check remaining quantity atleast 1 available
	$userid = get_current_user_id();
	$args = array("post_type" => "emgt_add_listing",'posts_per_page'=>-1,"author"=>$userid,"post_status"=>"draft");
	$posts = get_posts($args);
	$total_post = sizeof($posts);
	$usage = $db->emgt_get_rows_multiple("emgt_plan_usage","user_id = {$userid} AND plan = {$plan}");
	
	if(!empty($usage))
	{
		// $used = intval($usage[0]['used_ads']);
		$total_used =  intval($usage[0]['remaining_ads']) - $total_post;
		if($total_used <= 0)
		{
			echo "<br>Hello User,You have used all of your Ads quota.Please upgrade your plan to avail more Ads.Thank You.<br><br><br>";
			echo "<a href='edit.php?post_type=emgt_add_listing'>Click Here</a> to go back.";
			die;
		}
	}		
	endif;
}

if(defined('REMS_CURRENT_ROLE')) :
if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
{ 
	add_action('new_to_auto-draft', 'emgt_pre_publish_check',10, 1 ); // Checks whether or not current user plan is expired and ads quota available. and gives abort message.
	// add_filter( 'wp_insert_post_data', 'emgt_pre_publish_check', '99', 2 );  
}
// if($post->post_parent == 0 && $post->post_type == 'post' && !$post->ID)
	// save_post
endif;

function emgt_add_propetry_to_pending($post) {
	die("called");
}

function emgt_ad_activated($post_ID, $post_after, $post_before)
{ 
 if(!is_super_admin($post_before->post_author)) //if post author is admin do not execute part
 {
	if($post_before->post_type == "emgt_add_listing")
	{
		if(isset($_GET['activated']) && isset($_GET['status']))
		{
			$db = new Emgt_Db;
			$userid = $db->emgt_get_rows("posts","ID",$post_ID);
			$userid = $userid[0]['post_author'];
			$plan = $db->emgt_get_rows("emgt_payments","user_id",$userid);
			$plan = $plan[0]['plan'];
			if($_GET['activated'] == "true" && $_GET['status'] == "publish")
			{				
				$chk = $db->emgt_check_unique_field_multiple("emgt_ads","user_id={$userid} AND post_id = {$post_ID}");
				if($chk)
				{
					$period = $db->emgt_get_rows("emgt_plans","id",$plan);
					$ads_validity = $period[0]['ads_validity'];
					$ads_period = $period[0]['ads_period'];
					$ads_a_date = date("Y-m-d H:i:s");
					$date = new DateTime($ads_a_date);				
					$date->modify("+{$ads_validity} {$ads_period}");
					$ads_e_date = $date->format('Y-m-d H:i:s');
					$data = array(
									"user_id" => $userid,
									"post_id" => $post_ID,
									"activated_date" => $ads_a_date,
									"expiry_date" => $ads_e_date
								);
					$success = $db->emgt_insert("emgt_ads",$data);
				}
				////////////////////////////////PLAN ACTIVATION BLOCK////////////////////////////////////////////////
				$status = Emgt_Plancheck::emgt_plan_status_check($userid,$plan);	
				if($status == "0")
				{
					$period = $db->emgt_get_rows("emgt_plans","id",$plan);
					$validity = $period[0]['plan_validity'];
					$period = $period[0]['plan_period'];
					$act_date = date("Y-m-d H:i:s");
					$date = new DateTime($act_date);
					$date->modify("+{$validity} {$period}");
					$exp_date = $date->format('Y-m-d H:i:s');		
					$data = array("status" => 1,
								"activated_date" => $act_date,
								"expire_date" => $exp_date);
					$db->emgt_db_update("emgt_payments",$data,array("user_id"=>$userid,"plan"=>$plan));
				}
				////////////////////////////////PLAN USAGE UPDATE/INSERT BLOCK/////////////////////////////////////////////////
				$quantity = $db->emgt_get_rows("emgt_plans","id",$plan); //count ads quantity for plan.
				$quantity = $quantity[0]['quantity'];
				$chk = $db->emgt_check_unique_field_multiple("emgt_plan_usage","user_id = {$userid} AND plan = {$plan}");	
				if(!$chk) //check if plan usage already added for this user and plan if yes means count=0 and insert new usage detail.
				{		
					$usage = $db->emgt_get_rows_multiple("emgt_plan_usage","user_id = {$userid} AND plan = {$plan}");
					$data = array(					
								"used_ads" => intval($usage[0]['used_ads'])+1,
								"remaining_ads" => intval($usage[0]['remaining_ads'])-1
								);
					$success = $db->emgt_db_update("emgt_plan_usage",$data,array("user_id" => $userid,"plan" => $plan)); //update used ads usage. 
				}else{
					$remaining = $quantity - 1;
					$data = array(
								"user_id" => $userid,
								"plan" => $plan,
								"used_ads" => 1,
								"remaining_ads" => $remaining
								);
					$success = $db->emgt_insert("emgt_plan_usage",$data);
				}		
				
			}
			else if($_GET['activated'] == "false" && $_GET['status'] == "draft")
			{				
				// ads status as draft
			}
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////
		
		
	}	
  }
}

if(defined('REMS_CURRENT_ROLE')) :
if(REMS_CURRENT_ROLE == "administrator")
{
	if(get_option("emgt_system_property_approval"))
		{	
			add_action( 'post_updated', 'emgt_ad_activated', 10, 3 );
		}
}
endif;



###########################
function egt_start_wrap()
{
	if(@file_exists(get_stylesheet_directory().'/rems_template/wrappers/wrapper-start.php'))
	{			require get_stylesheet_directory().'/rems_wplms_template/wrappers/wrapper-start.php';

	}
		else
   require(REMS_PLUGIN_DIR."/template/wrappers/wrapper-start.php");		
}

function egt_end_wrap()
{
	if(@file_exists(get_stylesheet_directory().'/mj_wplms_template/wrappers/wrapper-end.php'))
	{			
		require get_stylesheet_directory().'/mj_wplms_template/wrappers/wrapper-end.php';
	}
	else
		require(REMS_PLUGIN_DIR."/template/wrappers/wrapper-end.php");		
}
add_action('emgt_start_wrap', 'egt_start_wrap');	
add_action('emgt_end_wrap', 'egt_end_wrap');


function emgt_registration_form_shortcode()
{ 		
	ob_start();
	include_once REMS_PLUGIN_DIR."/template/registration_page.php";
	return ob_get_clean();
}


function emgt_properties_list_shortcode()
{ 		
	ob_start();
	include_once REMS_PLUGIN_DIR."/template/emgt_property_list.php";
	return ob_get_clean();
}


add_shortcode( 'emgt_registration_form', 'emgt_registration_form_shortcode' );
add_shortcode( 'emgt_properties_list', 'emgt_properties_list_shortcode' );
add_shortcode( 'emgt_agent_list', 'emgt_agent_list_shortcode' );
add_shortcode( 'emgt_single_property_sidebar', 'emgt_single_property_sidebar_shortcode' );

function emgt_single_property_sidebar_shortcode()
{
	ob_start();
	include_once REMS_PLUGIN_DIR."/template/right-sidebar.php";
	// return ob_get_clean();
}

function emgt_agent_list_shortcode(){
	?>	
	<div class="margin-box col-md-3 col-sm-12 col-xs-12" style="width:auto;margin-bottom:30px;">
	<div class="agents_data">
			<h3 class="h_border"><?php _e("Agents","estate-emgt");?></h3>	
			<div class="agents_profile">
			<ul id="agent_ul">	
			<?php 	
			$args = array( "role" => 'emgt_role_agent');
			$agents = get_users($args);
			if(!empty($agents))
			{
			foreach ($agents as $agent)
			{ 
			$upload = wp_upload_dir();			
				$photo = (!empty($agent->user_photo)) ? $agent->user_photo : REMS_PLUGIN_URL."/images/default_user_logo.png";
				// $photo = (!empty($agent->user_photo)) ? $upload['baseurl']."/".$agent->user_photo : get_template_directory_uri()."/images/default_user_logo.png";
			?>
				<li><a href="<?php echo esc_url(home_url("/"));?>?view_profile=yes&id=<?php echo $agent->ID;?>"><img src="<?php echo $photo; ?>" alt="dp_img" class="agent_dp" height='80px' width='80px'></img>
				&nbsp;&nbsp;&nbsp;<?php echo $agent->first_name ." ". $agent->last_name; ?>
				</a>&nbsp;&nbsp;&nbsp;</li>
		<?php }
			}
			else{
				echo "<li>No agents available !</li>";
			} ?>
			</ul>
				</div>
	</div>	
	</div>		
	<?php
}

function emgt_login_form_shortcode(){
	
	if ( is_user_logged_in() )
	{
		// echo '<ol class="breadcrumb emgt_breadcrumb">
				// <li><a href="'.home_url('/').'"> '. __("Home","estate-emgt").'</a></li>
				// <li><a href="'.$post->guid.'">'.$post->post_title.'</a></li></ol>';
		echo '<p>You are already logged in!</p>';
		echo "<p><a href='".get_dashboard_url(get_current_user_id(),"admin.php?page=emgt_estate")."'>Dashboard</a></p>";
		echo "<p><a href='".wp_logout_url(home_url('/login-page/'))."'>Logout</a></p>";		
	}
	else{		
	
		echo "<style>
			.entry-header{
				display : none;
			}
			</style>";
		// echo '<ol class="breadcrumb emgt_breadcrumb">
				// <li><a href="'.home_url('/').'"> '. __("Home","estate-emgt").'</a></li>
				// <li><a href="'.$post->guid.'">'.$post->post_title.'</a></li></ol>';
		$args = array(
        'redirect' => admin_url(), 
        'form_id' => 'emgt-custom-login-st',       
        'label_remember' => __( 'Remember Me ' ),
        'label_log_in' => __( 'Log In' ),
        'remember' => true
    );
	?>
<!-- 	<div class="rems_container">
	<div class="row">
	<div class="col-sm-8"> -->
	<?php 
	$template = get_option('template');
	
	if($template == "twentysixteen" || $template == "twentyfifteen")
	{ 	
		echo "<style>
				// p.login-password [type=password] {
				// margin-left:0 !important;
				// }
				// .lost-pass-link {
					// position: absolute;
					// color: #333 !important;
					// font-weight: bold;
					// left: 180px;
					// top: 172px;
				// }					
			</style>";
	}	
?>
	<div class="login-box">
		<div class="login-title">
			<?php _e("Login","estate-emgt");?>
		</div>
		<div class="login-frm-box">
	<?php	echo wp_login_form($args);
	echo "</div>
		
		</div>";
	}
}

add_shortcode( 'emgt_login_form', 'emgt_login_form_shortcode' );
add_action( 'login_form_middle', 'add_lost_password_link' );

function add_lost_password_link() {
	return '<a href="'.esc_url(home_url("/wp-login.php")).'?action=lostpassword" class="lost-pass-link">Lost Password?</a>';
}

if(isset($_GET['view_profile']) && $_GET['view_profile'] == "yes" && !empty($_GET['id']))
{
	if(@file_exists(get_template_directory_uri().'/wprealty_template/view_profile.php'))
	{include_once get_template_directory_uri().'/wprealty_template/view_profile.php';}
	else	
	{include_once REMS_PLUGIN_DIR."/template/view_profile.php";}
			exit;
}

if(isset($_POST['emgt_search']) && $_POST['emgt_search'] == "yes")
{ 
	if(@file_exists(get_template_directory().'/wprealty_template/searched_properties.php'))
	{ include_once get_template_directory().'/wprealty_template/searched_properties.php';exit;}
	else{include_once REMS_PLUGIN_DIR."/template/searched_properties.php";exit;}
			
}

if(defined('REMS_CURRENT_ROLE')) :
if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")	
{
	if(get_option("emgt_system_property_approval"))
		{
			add_action('admin_print_styles-post.php',     'emgt_hide_publishing_block_using_css');
			add_action('admin_print_styles-post-new.php', 'emgt_hide_publishing_block_using_css');
		}
}
endif;

function emgt_hide_publishing_block_using_css(){
	global $post;
	
	if($post->post_type == "emgt_add_listing" && $post->post_status != 'publish')
	{
		echo '<style>
		#misc-publishing-actions{display:none;}
		#major-publishing-actions{display:none;}
		</style>';
	}
}


function emgt_get_currency_symbol( $currency = '' ) {			

			switch ( $currency ) {
			case 'AED' :
			$currency_symbol = 'د.إ';
			break;
			case 'AUD' :
			case 'CAD' :
			case 'CLP' :
			case 'COP' :
			case 'HKD' :
			case 'MXN' :
			case 'NZD' :
			case 'SGD' :
			case 'USD' :
			$currency_symbol = '&#36;';
			break;
			case 'BDT':
			$currency_symbol = '&#2547;&nbsp;';
			break;
			case 'BGN' :
			$currency_symbol = '&#1083;&#1074;.';
			break;
			case 'BRL' :
			$currency_symbol = '&#82;&#36;';
			break;
			case 'CHF' :
			$currency_symbol = '&#67;&#72;&#70;';
			break;
			case 'CNY' :
			case 'JPY' :
			case 'RMB' :
			$currency_symbol = '&yen;';
			break;
			case 'CZK' :
			$currency_symbol = '&#75;&#269;';
			break;
			case 'DKK' :
			$currency_symbol = 'kr.';
			break;
			case 'DOP' :
			$currency_symbol = 'RD&#36;';
			break;
			case 'EGP' :
			$currency_symbol = 'EGP';
			break;
			case 'EUR' :
			$currency_symbol = '&euro;';
			break;
			case 'GBP' :
			$currency_symbol = '&pound;';
			break;
			case 'HRK' :
			$currency_symbol = 'Kn';
			break;
			case 'HUF' :
			$currency_symbol = '&#70;&#116;';
			break;
			case 'IDR' :
			$currency_symbol = 'Rp';
			break;
			case 'ILS' :
			$currency_symbol = '&#8362;';
			break;
			case 'INR' :
			$currency_symbol = 'Rs.';
			break;
			case 'ISK' :
			$currency_symbol = 'Kr.';
			break;
			case 'KIP' :
			$currency_symbol = '&#8365;';
			break;
			case 'KRW' :
			$currency_symbol = '&#8361;';
			break;
			case 'MYR' :
			$currency_symbol = '&#82;&#77;';
			break;
			case 'NGN' :
			$currency_symbol = '&#8358;';
			break;
			case 'NOK' :
			$currency_symbol = '&#107;&#114;';
			break;
			case 'NPR' :
			$currency_symbol = 'Rs.';
			break;
			case 'PHP' :
			$currency_symbol = '&#8369;';
			break;
			case 'PLN' :
			$currency_symbol = '&#122;&#322;';
			break;
			case 'PYG' :
			$currency_symbol = '&#8370;';
			break;
			case 'RON' :
			$currency_symbol = 'lei';
			break;
			case 'RUB' :
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
			case 'SEK' :
			$currency_symbol = '&#107;&#114;';
			break;
			case 'THB' :
			$currency_symbol = '&#3647;';
			break;
			case 'TRY' :
			$currency_symbol = '&#8378;';
			break;
			case 'TWD' :
			$currency_symbol = '&#78;&#84;&#36;';
			break;
			case 'UAH' :
			$currency_symbol = '&#8372;';
			break;
			case 'VND' :
			$currency_symbol = '&#8363;';
			break;
			case 'ZAR' :
			$currency_symbol = '&#82;';
			break;
			default :
			$currency_symbol = $currency;
			break;
	}
	return $currency_symbol;

}

function emgt_check_expiry($p_id)
{
	$db = new Emgt_Db;
	$data = $db->emgt_get_rows("emgt_ads","post_id",$p_id);
	if(!empty($data))
	{	
		$curr_date = strtotime(date("Y-m-d H:i:s"));
		$expire_date = strtotime($data[0]['expiry_date']);	
		if ($curr_date > $expire_date)
		{
			return false; // Ad is expired
		}
		else 
		{
			return true; 
		}	
	}else{
		return true;
	}
}

function emgt_check_plan_expiry($curr_date,$expire_date)
{
	$curr_date = strtotime($curr_date);
	$expire_date = strtotime($expire_date);	
	if ($curr_date > $expire_date)
	{
		return false; // Plan is expired
	}
	else 
	{
		return true; 
	}	
}

function emgt_home_page_redirect()
{
    if( is_page( 'Sample Page' ) )
    {
        wp_redirect( home_url( '/property-list/' ) );
        exit();
    }
}
add_action( 'template_redirect', 'emgt_home_page_redirect' );
add_action('wp_logout',create_function('','wp_redirect(home_url("/"));exit();'));

add_action('wp_head','emgt_get_color');

function emgt_get_color() {
	 $bg_color = get_option("emgt_system_front_color");
	 $bg_text_color = get_option("emgt_system_front_text_color");	
	 ?>
	 <script>
		jQuery(document).ready(function(){	
			/* registration page css */
			jQuery('li.emgt_dyn_color').css("background-color",'<?php echo $bg_color; ?>');
			jQuery('li.emgt_dyn_color').css("color",'<?php echo $bg_text_color; ?>');
			jQuery('.agents_data h3').css("color",'<?php echo $bg_color; ?>');
		
			/* List Page Css */			
			jQuery('.search_for').css("background-color",'<?php echo $bg_color; ?> !important');
			jQuery('.search_for').css("color",'<?php echo $bg_text_color; ?>');
			jQuery('.sel_for').css("background-color",'<?php echo $bg_color; ?>');		
			jQuery('.sel_for').css("color",'<?php echo $bg_text_color; ?>');
			addRule(".for_active:before", "color: <?php echo $bg_color; ?>");			
			addRule(".icon_marker:before", "color: <?php echo $bg_color; ?>");			
			addRule(".icon_type:before", "color: <?php echo $bg_color; ?>");			
			addRule(".icon_beds:before", "color: <?php echo $bg_color; ?>");			
			addRule(".for_active", "background-color: rgba(0, 0, 0, 0.66) !important");
			addRule(".for_active", "color: #FFF");			
			$('.ad_list_item').hover(function(e){
				$(this).css("outline",e.type === "mouseenter"?"1px solid <?php echo $bg_color; ?>":"transparent");
			});
			jQuery('.body-box').css("border-right",'2px solid <?php echo $bg_color; ?>');
			jQuery('.fa').css("color",'<?php echo $bg_color; ?>');
			jQuery('.emgt_price').css("color",'<?php echo $bg_color; ?>');
			jQuery('.view_btn').css("background-color",'<?php echo $bg_color; ?>');
			jQuery('.view_btn').css("color",'<?php echo $bg_text_color; ?>');
			jQuery('.listing_right').css("border-left",'1px solid <?php echo $bg_color; ?>');
		 
			/* Single Page Style */
			jQuery('.success-label').css("color",'<?php echo $bg_color; ?>');
			jQuery('.borderless .o_td').css("color",'<?php echo $bg_color; ?>');			
			jQuery('.sng-amt').css("color",'<?php echo $bg_color; ?>');
			
			
			
			

			
			
		});
	 </script>
	<?php
}
