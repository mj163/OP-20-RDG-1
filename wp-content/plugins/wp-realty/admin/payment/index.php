<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "payment_list";
$db = new Emgt_Db;
$currency = emgt_get_currency_symbol(get_option("emgt_system_currency",true));
$edit = 0;
?>
<?php 
if(isset($_POST['add_payment']))
{
	$plan_db = $db->emgt_get_rows("emgt_plans","id",$_POST['plan']);
	$plan_price = $plan_db[0]['price'];
	if($_POST['add_payment'] == "Assign Plan")
	{		
		$data = array('user_id' => $_POST['user'],'plan' => $_POST['plan']);
		$data["status"] = 0	;
		$data["created_date"] = date('Y-m-d H:i:s');	
		$data["plan_price"] = $plan_price;
		$data["remaining"] = $plan_price;
		$data["payment_status"] = 1;
		$chk = $db->emgt_insert("emgt_payments",$data);		
		if($chk)
		{
			$userdata = get_userdata($_POST['user']);
			$plan = $db->emgt_get_rows("emgt_plans","id",$_POST['plan']);
			$email = $userdata->user_email;
			$sname = get_option("emgt_system_name");
			$semail = get_option("emgt_system_email");
			$header[] = 'Content-Type: text/html; charset=UTF-8';
			$header[] = "From : {$sname} <{$semail}>";				
			$subject = "Congratulations !! New Plan has been assigned";
			$body = "<p>Hi,</p>";
			$body .= "<p>New Plan has been assigned to you and now you can add listings to out system.</p>";
			$body .= "<p>Plan Name : ". $plan[0]['name'] ."</p>";
			$body .= "<p>Plan Price : $ ". $_POST['price'] ."</p>";
			$body .= "<p>Please, Check website for more details about plan.</p>";
			$body .= "<p>Thank You.</p>";
			$chk_ml = wp_mail($email,$subject,$body,$header);

			$success = 1;
			$tab = "payment_list";
		}else{
			$success = 2;
		}		
		
	}
	if($_POST['add_payment'] == "Update Plan")
	{
		$data = array('plan' => $_POST['plan']);
		$data["plan_price"] = $plan_price;
		$data["remaining"] = $plan_price;
		$uid = $_POST['uid'];
		$plan_data = $db->emgt_get_rows("emgt_payments","user_id",$uid);
		if($plan_data[0]['status']) // if plan is activated then update expiry date as NEW PLAN
		{
			$a_date = $plan_data[0]['activated_date'];
			$date = new DateTime($a_date);
			$date->modify("+{$plan_db[0]['plan_validity']} {$plan_db[0]['plan_period']}");			
			$exp_date = $date->format('Y-m-d H:i:s');
			$data["expire_date"] = $exp_date;			
		}		
		if(isset($_POST['ex_date']))
		{
			// $data["activated_date"] = $_POST['ac_date'];
			// var_dump($plan_data[0]['expire_date']);die;
			$curr_date = date("Y-m-d H:i:s");
			$chk_e = emgt_check_plan_expiry($curr_date,$_POST['ex_date']);
			if(!$chk_e)
			{  
				$data['status'] = 2;
			}else{
				$data['status'] = 1;
			}
			$data["expire_date"] = $_POST['ex_date'];			
		}
		// $used_ads = $db->emgt_get_rows("emgt_plan_usage","user_id",$uid);
		// $used_ads = intval($used_ads[0]["used_ads"]);
		// var_dump($used_ads);die;
			
		// $remaining_ads = $plan_total_ads - $used_ads;
		if($_POST['plan'] != $plan_data[0]['plan'] )
		{
			$plan_total_ads = $db->emgt_get_rows("emgt_plans","id",$_POST['plan']);
			$avail_ads = intval($plan_total_ads[0]["quantity"]);	
			$db->emgt_db_update("emgt_plan_usage",array("remaining_ads"=>$avail_ads,"plan"=>$_POST['plan']),array("user_id" => $uid));
		}
		
		$chk = $db->emgt_db_update("emgt_payments",$data,array("user_id" => $uid));
		($chk) ? $success = 1 : $success = 2;		
	}
}

if(isset($_POST['add_user_payment']))
{
	$paid = intval($_POST['paid']);
	$paid_via = $_POST['paid_via'];
	$pay_id = $_POST['pay_id'];
	$plan = $_POST['plan'];
	$prev_paid = $db->emgt_get_rows("emgt_payments","id",$pay_id);
	$prev_paid = intval($prev_paid[0]['paid']);
	$prev_remaining = intval($prev_paid[0]['remaining']);	
	$plan_price = $db->emgt_get_rows("emgt_plans","id",$plan);
	$plan_price = intval($plan_price[0]['price']);
	// $remaining = $prev_paid[0]['remaining'] - $paid;  //remaining should count first then paid counts.dont change.
	$remaining = $plan_price - ($prev_paid + $paid);	
	$paid = $prev_paid + $paid;
	// $paid = $prev_paid[0]['paid'] + $paid;
	if($remaining <= 0)
	{
		$payment_status = 0;		
	}
	else if($remaining >= 0)	{
		$payment_status = 2;
		
	}
	
	$chk = $db->emgt_db_update("emgt_payments",array("paid"=>$paid,"paid_via"=>$paid_via,"remaining"=>$remaining,"payment_status" => $payment_status),array("id"=>$pay_id));
	($chk) ? $success = 3 : $success = 2;
	
	$data = array(
			"user_id" => $_POST['user_id'],
			"plan_id" => $_POST['plan'],
			"paid_amount" => $paid,			
			"paid_via" => $paid_via,	
			"paid_date" => date("Y-m-d"),
			"created_by" => get_current_user_id(),
			"transaction_id" => ""
			);
	$db->emgt_insert("emgt_payments_history",$data);
}

if(isset($_GET['eid']))
{
	$eid = intval($_GET['eid']);
	$tab = "add_payment";
	$edit = 1;
}
if(isset($_GET['did']))
{
	$did = intval($_GET['did']);
	$chk = $db->emgt_delete_record("emgt_payments","id",$did);
	($chk) ? $success = 1 : $success = 2;
}
if(isset($_GET['vid']))
{
	$vid = intval($_GET['vid']);
	$user = intval($_GET['user']);
}
if(isset($_GET['pay_id']))
{
	$pay_id = intval($_GET['pay_id']);
}
?>
<script type="text/javascript">
$(document).ready(function() {
	jQuery('#example').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},	
	                  {"bSortable": true},	
					  {"bSortable": true},
					  {"bSortable": true,"width":"20px"},
	                  {"bSortable": true,"width":"20px"},					  
					  {"bSortable": true},
	                  {"bSortable": true,"width":"100px"},
					  {"bSortable": true,"width":"100px"},						 
	                  {"bSortable": false}]});	
});
</script>   
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
    <div class="modal-content">
    <div class="result">
    </div>
	
    </div>
    </div> 
    
</div>

<div class="page-inner" style="min-height:1631px !important">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo get_option( 'emgt_system_name' );?></h3>
	</div>
	<div id="main-wrapper" class="class_list">
	<?php if(isset($success)) : 
		switch($success)
		{
			CASE 1: ?>
					<div class="alert alert-success alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-check-circle"></i>&nbsp;<?php _e('Success !','estate-emgt');?></strong> <?php _e('Operation completed successfully!','estate-emgt');?>
				</div>	
			<?php
			break;			
			CASE 2:?>
					<div class="alert alert-danger alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-warning"></i>&nbsp;<?php _e('Error !','estate-emgt');?></strong> <?php _e('Operation failed. Pleas try again later!','estate-emgt');?>
					</div>		
				</div>					
			<?php	
			break;
			CASE 3: ?>
					<div class="alert alert-success alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-check-circle"></i>&nbsp;<?php _e('Success !','estate-emgt');?></strong> <?php _e('Payment successfully added!','estate-emgt');?>
				</div>	
			<?php
		}
		endif;
	?>	
	<div class="panel panel-white">		
	<div class="panel-body">
	<h3> 
		<ul class="nav nav-tabs" id="topmenu" role="tablist">	 
		  <li class="nav-item <?php echo ($tab=='payment_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_payment&tab=payment_list" role="tab"><i class="fa fa-usd"></i> <?php _e('Payment List','estate-emgt');?></a>
		  </li>	
	<?php if(REMS_CURRENT_ROLE == 'administrator')
		  { ?>
		  <li class="nav-item  <?php echo ($tab=='add_payment')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_payment&tab=add_payment" role="tab"><i class="fa fa-briefcase"></i> <?php ($edit) ? _e('Edit','estate-emgt') : _e('Assign Plan','estate-emgt');?></a>
		  </li>
		  <?php }
		  if($tab == "view_payment")
		  { ?>	  
		  <li class="nav-item  <?php echo ($tab=='view_payment')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_payment&tab=view_payment&vid=<?php echo $vid;?>&user=<?php echo $user;?>" role="tab"><i class="fa fa-briefcase"></i> <?php ($edit) ? _e('View Details','estate-emgt') : _e('View Details','estate-emgt');?></a>
		  </li> 
	<?php } ?>
		</ul>
	</h3>
	<div class="tab-content"> 
	<?php 
	if($tab == "payment_list")
	{?>
		 <br>
			 <br>
					<table id="example" class="display" cellspacing="0" width="100%">
						<thead>
							<th><?php _e("Photo","estate-emgt");?></th>
							<th><?php _e("Name","estate-emgt");?></th>
							<th><?php _e("Plan","estate-emgt");?></th>
							<th><?php _e("Price","estate-emgt");?></th>
							<th><?php _e("Amount Paid","estate-emgt");?></th>
							<th><?php _e("Amount Due","estate-emgt");?></th>							
							<th><?php _e("Plan Status","estate-emgt");?></th>
							<th><?php _e("Plan Created Date","estate-emgt");?></th>
							<th><?php _e("Plan Activated Date date","estate-emgt");?></th>							
							<th><?php _e("Action","estate-emgt");?></th>
						</thead>
						<tfoot>
							<th><?php _e("Photo","estate-emgt");?></th>
							<th><?php _e("Name","estate-emgt");?></th>
							<th><?php _e("Plan","estate-emgt");?></th>
							<th><?php _e("Price","estate-emgt");?></th>
							<th><?php _e("Amount Paid","estate-emgt");?></th>
							<th><?php _e("Amount Due","estate-emgt");?></th>							
							<th><?php _e("Plan Status","estate-emgt");?></th>
							<th><?php _e("Plan Created Date","estate-emgt");?></th>
							<th><?php _e("Plan Activated Date date","estate-emgt");?></th>							
							<th><?php _e("Action","estate-emgt");?></th>
						</tfoot>
						<tbody>
					<?php		
						// $payments = $db->emgt_db_get("emgt_payments");
						if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
						{
							$payments =  $db->emgt_get_rows("emgt_payments","user_id",get_current_user_id());
						}
						else{
							$payments = $db->emgt_db_get_join("emgt_payments","users","user_id","ID"); // returns only if user is exists 
						}
						
						
						foreach($payments as $pay)
						{	
							$cdate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $pay['created_date']);
							$adate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $pay['activated_date']);							
							
							switch($pay['status'])
							{
								CASE 0:
									$status = "<span class='bg-info'>&nbsp;&nbsp;".__('Not Activated','estate-emgt')."&nbsp;&nbsp;</span>";
									$adate = "<span class='bg-info'>&nbsp;&nbsp;".__('Not Activated','estate-emgt')."&nbsp;&nbsp;</span>";
								break;
								CASE 1:
									$status = "<span class='bg-success'>&nbsp;&nbsp;".__('Activated','estate-emgt')."&nbsp;&nbsp;</span>";
								break;
								CASE 2:
									$status = "<span class='bg-danger'>&nbsp;&nbsp;".__('Expired','estate-emgt')."&nbsp;&nbsp;</span>";
								break;
							}							
							$user = get_userdata($pay['user_id']);
							$user = $user->first_name ." ". $user->last_name;
							$plan = $db->emgt_get_rows("emgt_plans","id",$pay['plan']);
							echo "<tr>";
							echo "<td><img src='".get_user_meta($pay['user_id'],'user_photo',true)."'  height='50px' width='50px' class='img-circle'></td>
							<td>{$user}</td>
							<td>{$plan[0]['name']}</td>
							<td>{$currency} {$plan[0]['price']}</td>
							<td>{$currency} {$pay['paid']}</td>
							<td>{$currency} {$pay['remaining']}</td>					
							<td>{$status}</td>
							<td>{$cdate}</td>
							<td>{$adate}</td>
							<td>
							<a href='?page=emgt_payment&vid={$pay['id']}&user={$pay['user_id']}&tab=view_payment' class='btn btn-primary radius' title='View'><i class='fa fa-eye'></i></a>&nbsp;														
						
							";
							if(REMS_CURRENT_ROLE == 'administrator')
							{
								echo "<a href='?page=emgt_payment&eid={$pay['user_id']}' class='btn btn-info radius' title='Edit'><i class='fa fa-edit'></i></a>&nbsp;
										<a href='?page=emgt_payment&did={$pay['user_id']}' class='btn btn-danger radius' title='Delete' onclick=\"return confirm('Are you sure you want to delete this record ?');\"><i class='fa fa-remove'></i></a>&nbsp;
									<a href='#' pay_id='{$pay['id']}' user='{$pay['user_id']}' plan='{$pay['plan']}' id='pay_now' class='btn btn-default btn-sm' title='Edit'>Pay</a>&nbsp;";
							}							
							echo "</td></tr>";
						}
					?>	
						
						</tbody>
					</table>
<?php } 
	if($tab == "add_payment")
	{		
	   include_once "assignt_plan.php";
	}
	
	if($tab == "view_payment")
	{		
	   include_once "view_payment.php";
	}						
	?>

	
</div>
</div>
</div>
</div>