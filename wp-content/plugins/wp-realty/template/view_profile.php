<?php 
get_header();
wp_enqueue_style('wplms-popup-css',REMS_PLUGIN_URL .'/css/frontend_style.css');
// wp_enqueue_style('btp-css',REMS_PLUGIN_URL .'/css/bs3.5.6/bootstrap_custom.css');
wp_enqueue_style('custom-glyphicon',REMS_PLUGIN_URL.'/css/bs3.5.6/custom_glyphicon.css');
wp_enqueue_style('grid-12',REMS_PLUGIN_URL.'/css/bs3.5.6/grid12.css');	
wp_enqueue_style('btp-css',REMS_PLUGIN_URL .'/css/bs3.5.6/bootstrap.min.css');
// wp_enqueue_style('btp-css',REMS_PLUGIN_URL .'/css/bs3.5.6/bootstrap.min-Copy.css');
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
do_action('emgt_start_wrap');
$author = intval($_GET['id']);
$details = get_user_meta($author);
$upload = wp_upload_dir();	
$photo  = (!empty($details['user_photo'][0])) ? $details['user_photo'][0] : get_template_directory_uri()."/images/default_user_logo.png";
// $photo  = (!empty($details['user_photo'][0])) ? $upload['baseurl']."/".$details['user_photo'][0] : get_template_directory_uri()."/images/default_user_logo.png";

$bg_color = get_option("emgt_system_front_color");
$bg_text_color = get_option("emgt_system_front_text_color");	
?>
<script>
jQuery(document).ready(function(){				
	jQuery('.agents_data i, i').css("color",'<?php echo $bg_color; ?>');
	jQuery('.ag_name').css("color",'<?php echo $bg_color; ?>');
	jQuery('.info-title').css("color",'<?php echo $bg_color; ?>');
});		
</script>

<ol class="breadcrumb emgt_breadcrumb no-margin">
	<li><a href="<?php echo esc_url(home_url('/'));?>"><?php _e("Home","estate-emgt");?></a></li>
	<li><a href="<?php echo @$post->guid;?>"><?php _e("Profile View","estate-emgt"); ?></a></li>			
</ol>
<div class="rems_container">
<div class="row profile-box no-margin">	
	<div class="col-sm-3 text-center photo-box">
		<div class="photo-box-border agents_data">	
		<br><br><br>
		<p>
			<img class="agent-profile-photo" src="<?php echo $photo; ?>" alt="dp_img"></img>
		</p>		
		<h2 class='ag_name'><?php echo $details['first_name'][0] ." ".$details['last_name'][0]; ?></h2>
		<hr>
		<h5><?php _e("AGENT","estate-emgt");?></h5>		
	</div>
	</div>
	<div class="col-sm-8 profile-detail">
		<div class="biography agents_data">
			<div class="profile_title no-margin">
				<h3 class="no-margin"><?php _e("Hello I'am","estate-emgt");?> <strong><i class="ag_name"><?php echo $details['first_name'][0] ." ".$details['last_name'][0]; ?></i></strong></h3>
			</div>
			<div class="emgt_agent_bio">
			<?php  if(empty($details['description'][0]))
				{
					echo "<blockquote>I am real estate agent.</blockquote>";
				}
				else{
					echo "<blockquote>{$details['description'][0]}</blockquote>";
				}?>
				
			</div>
		</div>
		<div class="biography agents_data">
			<div class="emgt_agent_info">
				<h3 class="no-margin"><i class="fa fa-info-circle"></i>  <?php _e("Personal Info","estate-emgt");?></h3>
			</div>
			<div class="info-row">
				<span class="info-title"><?php _e("Website","estate-emgt");?> :</span> <?php  echo (get_the_author_meta('user_url',$author)) ? get_the_author_meta('user_url',$author) : "Not Available" ;?>
			</div>
			<div class="info-row">
				<span class="info-title"><?php _e("Mobile","estate-emgt");?> :</span> +<?php  echo get_the_author_meta( 'phone',$author );?>
			</div>
			<div class="info-row">
				<span class="info-title"><?php _e("Email-id","estate-emgt");?> :</span> <a href="mailto:<?php  echo get_the_author_meta( 'user_email',$author );?>"><?php  echo get_the_author_meta( 'user_email',$author );?></a>
			</div>
			<div class="info-row">
				<span class="info-title"><?php _e("Address","estate-emgt");?>:</span> <?php  echo get_the_author_meta( 'address',$author );?>
			</div>
		</div>
	</div>
</div>
<p></p>
</div>
<?php 
do_action('emgt_end_wrap');
get_footer();
?>