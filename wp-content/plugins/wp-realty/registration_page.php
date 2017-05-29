<?php 

$db = new Emgt_Db;
if(isset($_POST['register_user']))
	{
		$user_id = wp_create_user(sanitize_user($_POST['username']),$_POST['password'],sanitize_email($_POST['email']));
		$userdata = array('ID'=>$user_id,'display_name'=> sanitize_text_field($_POST['first_name'])." ".sanitize_text_field($_POST['last_name']));
		$chk = wp_update_user($userdata);
		global $wpdb;
		if(!empty($_FILES['user_photo']['name']))
		{
			$image = $_FILES['user_photo']['name'];			
			if (!function_exists('wp_generate_attachment_metadata')){
			  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			  require_once(ABSPATH . "wp-admin" . '/includes/media.php');
			 }
			$overrides = array( 'test_form' => false);
			$file = wp_handle_upload($_FILES['user_photo'], $overrides);
			$content = $file['url'];
		}
		else{
			$content = get_option("emgt_system_logo");
		}
		if(!is_wp_error($user_id))
		{
			$data = array(
					"plan_id"=>intval($_POST['plan_id']),
					"first_name"=>sanitize_text_field($_POST['first_name']),
					"middle_name"=>sanitize_text_field($_POST['middle_name']),
					"last_name"=>sanitize_text_field($_POST['last_name']),
					"display_name"=> sanitize_text_field($_POST['first_name'])." ".sanitize_text_field($_POST['last_name']),
					"gender"=>sanitize_text_field($_POST['gender']),
					"address"=>sanitize_text_field($_POST['address']),
					"phone"=>intval($_POST['phone']),
					"wp_capabilities"=>array(sanitize_text_field($_POST['role'])=>true),
					"user_photo" => $content
					);
			foreach($data as $key=>$value)
			{
				$chk = update_user_meta($user_id,$key,$value);
				if($chk == true)
				{
					$success = 1;
				}		
			}
			update_user_meta($user_id,"emgt_hash","deactivated");			
			$price = $db->emgt_get_rows("emgt_plans","id",intval($_POST['plan_id']));
			$price = $price[0]["price"];
			$insert = array(
						"user_id"=>$user_id,
						"plan"=>intval($_POST['plan_id']),
						"plan_price"=>$price,
						"status" => 0,
						"created_date"=>date("Y-m-d H:i:s")
						);
			$currency = emgt_get_currency_symbol(get_option("emgt_system_currency"));
			$result = $db->emgt_insert("emgt_payments",$insert);
			$sname = get_option("emgt_system_name");
			$semail = get_option("emgt_system_email");
			$sphone = get_option('emgt_system_phone');
			$sadd = get_option("emgt_system_address");
			$fname = sanitize_text_field($_POST['first_name']);
			$header[] = "Content-Type: text/html; charset=UTF-8";
			$header[] = "From : {$sname} <{$semail}>";
			$sub = "Registration Successfully Completed";
			$body = "<p>Dear {$fname} ,</p>";
			$body .= "<p>Thank you for registering on our system.</p>";
			$body .= "<p>Your Username is <strong>".sanitize_user($_POST['username'])."</strong></p>";
			$body .= "<p>You can login to our system once after admin activates your account.You will be require to pay selected plan amount.</p>";
			$body .="<p>Selected Plan : ".sanitize_text_field($_POST['plan_name'])."</p>";
			$body .="<p>Plan Price : {$currency} {$price}</p>";
			$body .= "<p>You can contact us from below details for any questions.</p>";
			$body .= "<p>Email : {$semail}</p>";
			$body .= "<p>Phone : {$sphone}</p>";
			$body .= "<p>Address : {$sadd}</p>";
			$body .= "<p>Thank You.</p>";
			$to = sanitize_email($_POST['email']);
			wp_mail($to,$sub,$body,$header);
			$success = 1;
		}
		else{
			$error_string = $user_id->get_error_message();
			echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';			
		}	
	}
?>

<style>
/* Hide page title in page content */
.entry-title {
       display:none;
}
.entry-content{
	max-width:100%;
}
.hentry{
	padding:0;
}
.plan-show th, .plan-show td{
    text-align: center;
}
.fa{
	color:green;
}
.modal-dialog,
.modal-content {
    /* 80% of window height */
    height: 810px;
}

.modal-body {
    /* 100% = dialog height, 120px = header + footer */
    max-height: calc(100% - 120px);
    overflow-y: scroll;
}

.emgt_breadcrumb{
	margin-top: -30px;
    border-radius: 0;
}

.feat_box{
	min-height : 67px;
}
</style>
<?php 
	$template = get_option('template');
	if($template == "twentysixteen" || $template == "twentyfifteen" || $template == "twentyfourteen")
	{ 	echo "<style>
			table,th,td{
				border:none !important;
				border-bottom:1px solid #dedede !important;
			}
			h3{
				font-weight: normal !important;
				padding-top: 10px;
				font-size: 13px !important;
			}
			.col-sm-4{
				padding-right:0 !important;
				padding-left:0 !important;
			}
			.tbl-right{
				text-align:left;
			}
			td:first-child{
				width:38%;
			}
		</style>";
	}
	if($template == "twentyfifteen" || $template == "twentyfourteen")
	{
		echo "<style>
			.plans-title{
				margin : 0 0 17px 0!important;				
			}
			.margin-box {
				margin: 0 !important;
				padding:10px !important;
			}
			
		</style>";
		?>
				<script>$(document).ready(function(){jQuery('.entry-content').removeClass();});</script>
				<?php
	}
	if($template == "twentyfourteen")
	{
		echo "<style>
		.entry-header{
			display:none;
		}
		.rems_container{
			margin-left:30px;
		}
		</style>";
	}
	if($template == "wprealty")
	{
		echo "<style>
		.container{
			margin-left: 38px !important;
		}
		</style>";	
	}
?>
<?php 

if(get_template() == "wprealty")
{ ?>
	<ol class="breadcrumb emgt_breadcrumb">
		<li><a href="<?php echo esc_url(home_url('/'));?>"><?php _e("Home","estate-emgt");?></a></li>
		<li><a href="<?php echo $post->guid;?>"><?php _e("Register Page","estate-emgt"); ?></a></li>			
	</ol>
	<div class="container">
<?php }
else{
	?>
	<ol class="breadcrumb emgt_breadcrumb">
		<li><a href="<?php echo esc_url(home_url('/'));?>"><?php _e("Home","estate-emgt");?></a></li>
		<li><a href="<?php echo $post->guid;?>"><?php _e("Register Page","estate-emgt"); ?></a></li>			
	</ol>
	<div class="rems_container">
	<?php	
}
?>

<div class="row">
<?php
if(isset($success) && $success == true)
{?>
	<span class="bg bg-success">&nbsp;&nbsp;<i class="fa fa-check"></i> <strong><?php _e("SUCCESS","estate-emgt");?></strong>:<?php _e("Registration Successfully Completed.Please Check Email","estate-emgt");?>.&nbsp;&nbsp;</span>
<?php
}
?>
<div class="col-md-12">
	<div class="col-sm-8 no-padding">	
	<?php 
		$plans = $db->emgt_db_get("emgt_plans");
		$total_plan = sizeof($plans);
	
	if($total_plan > 0)
	{
	?>
	<h4 class="plans-title"><strong><?php _e("Available Plans","estate-emgt");?></strong></h4>
	<br>
	<?php 
		foreach($plans as $plan)
		{?>
			<div class="emgt_plan">
				<ul class="no-margin">
					<li class="pl_name"><h3><?php echo $plan['name'];?></h3></li>
					<li class="pl_price"><?php echo emgt_get_currency_symbol(get_option("emgt_system_currency"))." ".$plan['price'];?></li>
					<li><?php echo$plan['quantity']." ". __("Ads","estate-emgt");?></li>
					<li class="feat_box">
						<?php 
						$features = $plan['features'];
						$features = explode(",",$features);
						$ft = "";
						foreach($features as $feature)
						{
							switch($feature)
							{
								CASE "video" :
									$ft .= ",Video";
								break;
								CASE "map" :
									$ft .= ",Show Map";
								break;
								CASE "visibility" :
									$ft .= ",Featured View";
								break;
							}
						}
						$ft = trim($ft,",");
						echo $ft;
						?>
					</li>
					<li><?php echo $plan['plan_validity']." ".$plan['plan_period']." ". __("Plan validity","estate-emgt");?></li>
					<li><?php echo $plan['ads_validity']." ".$plan['ads_period']." ". __("Ads validity","estate-emgt");?></li>		
					<li class="pl_sign_up"><button data-toggle="modal" data-target=".bs-example-modal-lg" class="buynow white" id="<?php echo $plan['id'];?>" pln="<?php echo $plan['name'];?>"><?php _e("Sign up","estate-emgt");?></button></li>		
				</ul>
			</div>
  <?php }
	}
	else{
	echo "<p> <bR><br><br><br>";
	echo __("No Plans Added to subscribe, Please visit again later.","estate-emgt")."!!";
	echo "</p>";
	}
	?>
	</div>
	<div class="col-sm-4">
		<br><br><br>
		<div class="margin-box">
		<div class="agents_data">
		<h3><?php _e("You can also reach us by","estate-emgt");?> </h3>
		<table class="tabel table-hover tbl-center no-margin reg_cont_box">			
			<tbody>
				<tr>
					<td><?php _e("Number","estate-emgt");?> :</td>		
					<td class="tbl-right"><?php  echo (!empty(get_option( 'emgt_system_phone' )))?get_option( 'emgt_system_phone' ):" Not Available "; ?></td>
				</tr>
				<tr>
					<td><?php _e("Email","estate-emgt");?> :</td>
					<td class="tbl-right"><a href="mailto:<?php echo get_option( 'emgt_system_email' );?>"><?php echo (!empty(get_option( 'emgt_system_email' )))?get_option( 'emgt_system_email' ):" Not Available "; ?></a></td>		
				</tr>
				<tr>
					<td><?php _e("Address","estate-emgt");?> :</td>		
					<td class="tbl-right"><?php  echo (!empty(get_option( 'emgt_system_address' )))?get_option( 'emgt_system_address' ):" Not Available "; ?></td>	
				</tr>
				<tr>
					<td><?php _e("Country","estate-emgt");?> :</td>	
					<td class="tbl-right"><?php  echo (!empty(get_option( 'emgt_system_country' )))?get_option( 'emgt_system_country' ):" Not Available "; ?></td>	
				</tr>
			</tbody>
		</table>	
		</div>
		</div>
	</div>
</div>
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">	
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"><?php _e("Registration Form","estate-emgt");?></h4>
      </div>
	  <div class="modal-body">
		<form  role="form" id="register-form" enctype="multipart/form-data" method="post" action="<?php echo get_page_link();?>" class="form-horizontal">
			<input type="hidden" name="plan_id" id="pln_id">
			<div class="form-group">
				<label class="text-right col-sm-offset-2 col-sm-2" for=""><?php _e("Plan Selected","estate-emgt");?></label>
				<div class="col-sm-4">
					<input type="text" name="plan_name" class="" id="sel_plan" value="" readonly>				
				</div>
			</div>
			<div class="form-group">
				<label class="text-right col-sm-offset-2 col-sm-2" for="type"><?php _e("I am","estate-emgt");?></label>
				<div class="col-sm-4">
					<input type="radio" name="role" class="" id="type" checked value="emgt_role_agent"> <?php _e("Agent","estate-emgt");?>
					<input type="radio" name="role" class="" id="type" value="emgt_role_owner"> <?php _e("Owner","estate-emgt");?>
				</div>
			</div>
			<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="first_name"><?php _e('First Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="first_name" id="first_name" class="validate[required] " />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="middle_name"><?php _e('Middle Name','estate-emgt');?></label>
			<div class="col-sm-8">
			<input type="text" name="middle_name" id="middle_name"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="last_name"><?php _e('Last Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="last_name" id="last_name" class="validate[required] "  />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="gender"><?php _e('Gender','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<input type="radio" name="gender" id="gender" class="validate[required] " value="male" checked /> <?php _e("Male","estate-emgt");?>
				<input type="radio" name="gender" id="gender" class="validate[required] " value="female" /> <?php _e("Female","estate-emgt");?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="email"><?php _e('Email','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<div class="input-group">
				<span class="input-group-addon">@</span>
				<input type="text" name="email" id="email" class="validate[required,custom[email]]" />
			</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="address"><?php _e('Address','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<textarea type="text" name="address" id="address" class="validate[required]"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="phone"><?php _e('Mobile Number','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<div class="input-group">
				<span class="input-group-addon">+</span>
				<input type="text" name="phone" id="phone" class="validate[required,custom[phone]]" />
			</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="username"><?php _e('Username','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="username" id="username" class="validate[required]" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 col-sm-offset-2 text-right" for="password"><?php _e('Password','estate-emgt');?></label>
			<div class="col-sm-8">
			<input type="password" name="password" id="password" class="validate[required]" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2  col-sm-offset-2 control-label" for=""><?php _e('Photo','estate-emgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
				<input type="file" id="" name="user_photo">
			</div>
		</div>
		</div>
		<div class="form-group">
			<label class="col-sm-8 text-right"><span class="bg bg-danger">* <?php _e("Your Account will be activated once admin activates.","estate-emgt");?></span>
		</div>     
	 
        <button  data-dismiss="modal" class="white"><?php _e("Close","estate-emgt");?></button>
        <input type="submit" class="btn btn-primary" name="register_user" value="<?php _e("Sign Up","estate-emgt");?>">
		</form>
      </div>
    </div>
  </div>
</div>
<script>
$("#register-form").validationEngine();
$(".buynow").click(function(){
	var plan = $(this).attr("pln");
	var pln_id = $(this).attr("id");
	$("#sel_plan").val(plan);
	$("#pln_id").val(pln_id);
});
</script>