<?php

######################   AJAX FUNCTION DECLARATION	#########

add_action('wp_ajax_emgt_fields_order_save','emgt_fields_order_save');
add_action('wp_ajax_emgt_edit_field','emgt_edit_field');
add_action('wp_ajax_emgt_add_new_section','emgt_add_new_section');
add_action('wp_ajax_emgt_delete_new_section','emgt_delete_new_section');
add_action('wp_ajax_emgt_show_add_field_form','emgt_show_add_field_form');
add_action('wp_ajax_emgt_custom_field_edit','emgt_custom_field_edit');
add_action('wp_ajax_emgt_popup_section_edit','emgt_popup_section_edit');
add_action('wp_ajax_emgt_show_field_status','emgt_show_field_status');
add_action('wp_ajax_emgt_get_property_title','emgt_get_property_title');
add_action('wp_ajax_emgt_get_user_by_role','emgt_get_user_by_role');
add_action('wp_ajax_emgt_get_user_data','emgt_get_user_data');
add_action('wp_ajax_emgt_view_inquiry_task','emgt_view_inquiry_task');
add_action('wp_ajax_emgt_view_inquiry_note','emgt_view_inquiry_note');
add_action('wp_ajax_emgt_get_user_by_property','emgt_get_user_by_property');
add_action('wp_ajax_emgt_add_user_payment','emgt_add_user_payment');
add_action('wp_ajax_emgt_view_bill','emgt_view_bill');
###################### ###################### ###################### 

function emgt_view_bill()
{
	$user = $_POST['user'];
	$data = Emgt_PlanCheck::emgt_get_user_plan_data($user);
	$user = get_userdata($user);
	$currency = emgt_get_currency_symbol(get_option("emgt_system_currency"));
?>
	
	<div class="modal-header">
			<a href="#" class="close-btn badge badge-success pull-right">X</a>
			<h4 class="modal-title"><?php echo get_option( 'emgt_system_name' );?></h4>
	</div>
	<div class="modal-body">
	
	<div id="invoice_print"> 
		<table width="100%" border="0">
						<tbody>
							<tr>
								<td width="68%">
									<img style="max-height:80px;" src="<?php echo get_option( 'emgt_system_logo' ) ?>">
								</td>
								<td align="right" width="26%">
									<h4> </h4>
									<h5><?php _e("Issue Date","estate-emgt"); ?> : <?php echo mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT,date("Y-m-d H:i:") );?></h5>
									<h5><?php _e("Status","estate-emgt"); ?>  :
									<?php 
									if($data['payment_status'] == "0") {
										echo '<span class="btn btn-success btn-xs" style="border-radius:0px;box-shadow:none;">Fully Paid</span>';
									}
									else if($data['payment_status'] == "2") {
										echo '<span class="btn btn-primary btn-xs" style="border-radius:0px;box-shadow:none;">Partially Paid</span>';
									}
									else{
										echo '<span class="btn btn-danger btn-xs" style="border-radius:0px;box-shadow:none;">Not Paid</span>';
									}
									?>	
									
									</h5>
								</td>
							</tr>
						</tbody>
					</table>
					<hr>
					<table width="100%" border="0">
						<tbody>
							<tr>
								<td align="left">
									<h4><?php _e("Payment To","estate-emgt"); ?> </h4>
								</td>
								<td align="right">
									<h4><?php _e("Bill To","estate-emgt"); ?> </h4>
								</td>
							</tr>
							<tr>
								<td valign="top" align="left">
									<?php echo get_option( 'emgt_system_name' );?><br><?php echo get_option( 'emgt_system_address' );?><br><?php echo get_option( 'emgt_system_phone' );?><br>									
								</td>
								<td valign="top" align="right">
									<?php echo $user->first_name . " " .$user->last_name;?><br><?php echo $user->address;?>,<br><?php echo $user->phone;?><br>								</td>
							</tr>
						</tbody>
					</table>
					<hr>
					<table class="table table-bordered" width="100%" border="1" style="border-collapse:collapse;">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center"> <?php _e("Plan Name","estate-emgt"); ?></th>
								<th><?php _e("Plan Price","estate-emgt"); ?></th>								
							</tr>
						</thead>
						<tbody>
							<tr><td>1</td>
							<td><?php echo $data['plan'];?></td>
							<td><?php echo $currency ." ". $data['plan_price'];?></td>
						</tr></tbody>
						</table>
						<table width="100%" border="0">
						<tbody>
							
							<tr>
								<td width="80%" align="right"><?php _e("Sub Total","estate-emgt"); ?> :</td>
								<td align="right"><?php echo $data['plan'];?></td>
							</tr>
							<tr>
								<td width="80%" align="right"><?php _e("Payment Made","estate-emgt"); ?> :</td>
								<td align="right"><?php echo $currency ." ". $data['paid'];?></td>
							</tr>
							<tr>
								<td width="80%" align="right"><?php _e("Due Amount","estate-emgt"); ?>  :</td>
								<td align="right"><?php echo $currency ." ". $data['remaining'];?></td>
							</tr>
							
						</tbody>
					</table>
					<hr>
					<h4></h4>
					<table class="table table-bordered" width="100%" border="1" style="border-collapse:collapse;">
					<thead>
							<tr>
								<th><?php _e("Date ","estate-emgt");?></th>
								<th><?php _e("Amount ","estate-emgt");?> </th>
								<th><?php _e("Method ","estate-emgt");?> </th>
								
							</tr>
						</thead>
						<tbody>
						<?php 
						if(!empty($data['payment_history']))
						{
							foreach($data['payment_history'] as $history)
							{
								$pdate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT,$history['paid_date'] );
								echo "<tr>
								<td>{$pdate}</td>
								<td>{$currency} {$history['paid_amount']}</td>
								<td>{$history['paid_via']}</td>
								</tr>";
							}
						}
						else{
								echo "<tr><td colspan='3' align='center'>".__("No Records found!!","estate-emgt")."</td></tr>";
						} ?>
						</tbody>
					</table>
						</div>
	</div>

	
<?php
	wp_die();
}

function emgt_add_user_payment()
{
	$pay_id = $_POST['pay_id'];
	$user = $_POST['user'];
	$plan = $_POST['plan'];
	?>
	<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
		<h4 id="myLargeModalLabel" class="modal-title"><i class="fa fa-money"></i>&nbsp;&nbsp;<?php _e("Add Payment","estate-emgt");?></h4>
	</div>
	<hr />
	<div class="modal-body">
			<form class="form-horizontal" id="pay_form" method="post">
			<input type="hidden" name="user_id" value="<?php echo $user;?>">
			<input type="hidden" name="plan" value="<?php echo $plan;?>">
				<div class="form-group">
					<label class="control-label col-sm-3"><?php _e('Paid Amount','estate-emgt'); ?> <span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input name="paid" class="form-control validate[required,min[1]]">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3"><?php _e('Paid Via','estate-emgt'); ?> <span class="require-field">*</span></label>
					<div class="col-sm-8">	
						<select class="form-control" name="paid_via">
							<option value="cash"><?php _e('Cash','estate-emgt'); ?></option>
							<option value="cheque" selected><?php _e('Cheque','estate-emgt'); ?></option>
							<option value="bank"><?php _e('Bank','estate-emgt'); ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<input type="hidden" name="pay_id" value="<?php echo $pay_id;?>">
					<div class="col-sm-2 col-sm-offset-3">
						<input type="submit" class="btn btn-primary" value="Pay" name="add_user_payment">
					</div>
				</div>
			</form>	
			<script>
				$("#pay_form").validationEngine();
			</script>
	</div>
<?php
	wp_die();
}

function emgt_get_user_by_property()
{
	$property = $_POST['property'];
	$db = new Emgt_Db;
	$users = $db->emgt_get_rows("posts","ID",$property);
	echo "<option value=''>".__('Select User','estate-emgt')."</option>";
	foreach($users as $user)
	{
		$u = get_userdata($user['post_author']);
		echo "<option value={$u->ID}>{$u->display_name}</option>";	
	}
	wp_die();
}

function emgt_view_inquiry_note()
{
	$note = $_POST['note'];
	$db = new Emgt_Db;
	$data = $db->emgt_get_rows("emgt_notes","id",$note);
	$note = $data[0]; ?>
	<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
		<h4 id="myLargeModalLabel" class="modal-title"><i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;<?php _e("Note Details","estate-emgt");?></h4>
	</div>
	<hr />
	<div class="panel panel-white">
		<div class="row">
		<div class="col-sm-5 col-sm-offset-3">
		<table class="table table-bordered">
			<tr>
			<td><strong><?php _e("Note Title ","estate-emgt");?></strong></td>	
			<td><?php echo $note['title'];?></td>
			</tr>
		<tr>
			<td><strong><?php _e("Note Description","estate-emgt");?></strong></td>	
			<td><?php echo $note['description'];?></td>
		</tr>		
		</table>
		</div>
		</div>
	</div>
<?php 
	wp_die();
}

function emgt_view_inquiry_task()
{
	$task = $_POST['task'];
	$db = new Emgt_Db;
	$data = $db->emgt_get_rows("emgt_tasks","id",$task);
	$task = $data[0]; ?>
	<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
		<h4 id="myLargeModalLabel" class="modal-title"><i class="fa fa-tasks"></i>&nbsp;&nbsp;<?php _e("Task Details","estate-emgt");?></h4>
	</div>
	<hr />
	<div class="panel panel-white">
		<div class="row">
		<div class="col-sm-5 col-sm-offset-3">
		<table class="table table-bordered">
			<tr>
			<td><strong><?php _e("Task Description","estate-emgt");?></strong></td>	
			<td><?php echo $task['task_detail'];?></td>
			</tr>
		<tr>
			<td><strong><?php _e("Schedule Date","estate-emgt");?></strong></td>	
			<td><?php echo $task['schedule_date'];?></td>
		</tr>
		<tr>
			<td><strong><?php _e("Status","estate-emgt");?></strong></td>	
			<td><?php echo $task['status'];?></td>
		</tr>
		</table>
		</div>
		</div>
	</div>
<?php 
	wp_die();
}

function emgt_get_user_data()
{
	$id = $_POST['id'];
	if(!empty($id))
	{
		$userdata = get_userdata($id);
		$data = array();
		$data["gender"] = $userdata->gender;
		$data["email"] = $userdata->user_email;
		$data["address"] = $userdata->address;
		$data["phone"] = $userdata->phone;
		echo json_encode($data);
	}	
	wp_die();
}

function emgt_get_user_by_role()
{
	$role = $_POST['role'];
	if(!empty($role))
	{		
		$users = get_users(array("role" => $role));
		if(!empty($users))
		{
			echo "<option value=''>". __("Select User") ."</option>";
			foreach($users as $user)
			{
				echo "<option value={$user->ID}>{$user->first_name} {$user->last_name}</option>";
			}
		}
	}
	else{
		echo "<option value=''>". __("No User Found") ."</option>";
	}
	wp_die();
}

function emgt_get_property_title()
{
	$id = $_POST['id'];
	$property = get_post($id);	
	if(!empty($property))
	{
		$title = $property->post_title;
		echo $title;
	}else{
		echo "Estate not found!!";
	}
	
	wp_die();
}

function emgt_show_field_status()
{
	$id = $_POST['id'];
	$check = $_POST['check'];
	$option_name = $id."_field_show";
	$status = ($check === "yes") ? $status = "1" : $status = "0";	
	$change = update_option($option_name,$status,true);
	echo $change;
	wp_die();
}


function emgt_popup_section_edit()
{
	$db = new Emgt_Db();
	$data = array("name" => $_REQUEST['s_name']);
	$id = array("section_id" => $_REQUEST['s_id']);
	$chk = $db->emgt_db_update("emgt_sections",$data,$id);
	if($chk)
	{
		echo "true";
	}
	wp_die();	
}

function emgt_custom_field_edit()
{
	$e_db = new Emgt_Db();
	$edit = 1;	
	$type = $_POST['type'];
	$data = $e_db->emgt_get_rows('emgt_fields','field_id',$_REQUEST['id']);
	
	switch ($type)
	{
		CASE "Textbox" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/textbox.php";		
		break;
		
		CASE "Checkbox" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/checkbox.php";
		break;
		
		CASE "Radio-Buttons" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/radio.php";
		break;
		
		CASE "Textarea" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/textarea.php";
		break;
		
		CASE "Dropdown" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/dropdown.php";
		break;
		
		CASE "section" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/newsection.php";
		break;
		
		default: ?>
			<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
				  <h4 id="myLargeModalLabel" class="modal-title"><i class="fa fa-cogs"></i>&nbsp; <?php _e('Field Configuration','estate-emgt');?></h4>
			</div>
			<hr> <?php
			echo "Invalid Option selected";		
	}
	
	wp_die();	
	
}

function emgt_show_add_field_form()
{
	$type = $_POST['type'];
	
	switch ($type)
	{
		CASE "Textbox" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/textbox.php";			
		break;
		
		CASE "Checkbox" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/checkbox.php";
		break;
		
		CASE "Radio-Buttons" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/radio.php";
		break;
		
		CASE "Textarea" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/textarea.php";
		break;
		
		CASE "Dropdown" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/dropdown.php";
		break;
		
		CASE "section" :
			include_once REMS_PLUGIN_DIR."/admin/fields/field_forms/newsection.php";
		break;
		
		default: ?>
			<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
				  <h4 id="myLargeModalLabel" class="modal-title"><i class="fa fa-cogs"></i>&nbsp; <?php _e('Field Configuration','estate-emgt');?></h4>
			</div>
			<hr> <?php
			echo "Invalid Option selected";		
	}
	
	wp_die();	
}

function emgt_fields_order_save()
{	
	$section_id = $_REQUEST['sec_id'];
	$order = $_REQUEST['order'];
	update_option("field_order_{$section_id}",$order);
	echo $_REQUEST['order'];	
	wp_die();
}

function emgt_edit_field(){
	$e_db = new Emgt_Db();
	$data = $e_db->emgt_get_single_row('emgt_fields','field_id',$_REQUEST['id']);	
	wp_send_json($data);	
	wp_die();
}


function emgt_add_new_section()
{
	$sec_name = $_POST['sec_name'];
	$option_id = $_POST['option_id'];
	
	$db = new Emgt_Db;
	$data = array("name" => $sec_name,
			"option_id"=>$option_id,
			"created_by" => get_current_user_id(),
			"created_date" => date("Y-m-d h:i:s"));
			
	$sec_id = $db->emgt_insert("emgt_sections",$data);	
	add_option("field_order_{$sec_id}");	
	
	$data[] = "<option value='{$sec_id}'>{$sec_name}</option>";
	$data[] = "<tr id='{$sec_id}'><td>{$sec_name}</td><td>
	<a id='{$sec_id}' role='button' class='edit_section'><i class='fa fa-edit'>&nbsp;</i>Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a id='{$sec_id}' class='delete_section' role='button'><i class='fa fa-trash'>&nbsp;</i>Delete</a></td></tr>";
	$data[] = "<li class='rems_menu rems_menu_link' id='{$sec_id}'><a href='?page=emgt_fields&tab=fieldlist&s_id={$sec_id}' class=''>{$sec_name}</a></li>";
	echo json_encode($data);
	wp_die();	
}

function emgt_delete_new_section()
{
	$db = new Emgt_Db;
	$sec_id = $_POST['sec_id'];
	
	$result = $db->emgt_delete_record("emgt_sections","section_id",$sec_id);
	if($result)
	{
		delete_option("field_order_{$sec_id}");
		echo "true";
	}
	else{ echo "false";}	
	wp_die();	
}

