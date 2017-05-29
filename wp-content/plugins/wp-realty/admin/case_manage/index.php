<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
if (REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner")
{
	$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "case_list";
}
else{
	$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "contract_list";	
}

$db = new Emgt_Db;
$edit = 0;
?>
<?php 
if(isset($_POST['add_contract']))
{
	$data = array(
			"name" => $_POST['name'],
			"address" => $_POST['address'],
			"email" => $_POST['email'],
			"phone" => $_POST['phone'],
			"type" => $_POST['type'],
			"services" => $_POST['services'],
		/*	"duration" => $_POST['duration'],
			"period" => $_POST['period'], 
			"status" => "Active", */
			"price" => $_POST['price'],
			"photo" => $_POST['photo']		
		);
	
	if($_POST['add_contract'] == "Add Contract")
	{
		$data["created_date"] = date("Y-m-d H:i:s");		
		$chk = $db->emgt_insert("emgt_contracts",$data);		
	}
	if($_POST['add_contract'] == "Update Contract")
	{
	//	$data["status"] =$_POST['status'];
		$chk = $db->emgt_db_update("emgt_contracts",$data,array("id"=>$_POST['cid']));		
	}
	($chk) ? $success = 1 : $success = 0;
	$tab = "contract_list";
}
if(isset($_GET['eid']))
{
	$edit_id = intval($_GET['eid']);
	$edit=1;
	$tab = "add_contract";
}
if(isset($_GET['did']))
{
	$did = intval($_GET['did']);
	$chk = $db->emgt_delete_record("emgt_contracts","id",$did);
	($chk) ? $success = 1 : $success = 0;	
}

if(isset($_POST['add_case']))
{
	$data = array(
		"property_id" => intval($_POST['property_id']),
		"estate" => sanitize_text_field($_POST['estate']),
		"assigned_to" => sanitize_text_field($_POST['name']),				
		"complain" => sanitize_text_field($_POST['complain']),
		"complain_by" => sanitize_text_field($_POST['by']),
		"phone" => intval($_POST['phone']),
		"status" => "Pending"		
	);
	if($_POST['add_case'] == "Add Case")
	{
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['assigned_date'] = date("Y-m-d H:i:s");
		$data['created_by'] = get_current_user_id();
		$chk = $db->emgt_insert("emgt_cases",$data);
		$sname = get_option("emgt_system_name");
		$semail = get_option("emgt_system_email");
		$header[] = "Content-Type: text/html; charset=UTF-8";
		$header[] = "From : {$sname} <{$semail}>";
		$to = sanitize_email($_POST['ct_mail']);
		$subject = "New Case Has Been Created in {$sname}";
		$body = "<p>Hi,</p>";
		$body .= "<p>New case(ticket) has been created in {$sname} for you. Details are below.</p>";
		$body .= "<p>Case for property : ".sanitize_text_field($_POST['estate'])."</p>";
		$body .= "<p>Complaint : ".sanitize_text_field($_POST['complain'])."</p>";
		$body .= "<p>Complain by : ".sanitize_text_field($_POST['by'])."</p>";
		$body .= "<p>Work status : 'Pending' </p>";
		$body .= "<p>Please look into this matter.</p>";
		$body .= "<p><br>Thank You.</p>";
		wp_mail($to,$subject,$body,$header);		
	}
	if($_POST['add_case'] == "Update Case")
	{
		$data['status'] = $_POST['status'];
		$chk = $db->emgt_db_update("emgt_cases",$data,array("id"=>$_POST['csid']));
	}
	($chk) ? $success = 1 : $success = 0;	
	$tab = "case_list";
}
if(isset($_GET['cseid']))
{
	$edit_id = intval($_GET['cseid']);
	$edit=1;
	$tab = "add_case";
}
if(isset($_GET['csdid']))
{
	$cs_did = intval($_GET['csdid']);
	$chk = $db->emgt_delete_record("emgt_cases","id",$cs_did);
	($chk) ? $success = 1 : $success = 0;
	$tab="case_list";
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#task_frm").validationEngine();
	jQuery('#contracts').DataTable({
		"aoColumns":[
	                  {"bSortable": true,"width":"40px"},
	                  {"bSortable": true},	
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},					  
	                  {"bSortable": false}]});
});
</script>  
<div class="page-inner" style="min-height:1631px !important">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /> <?php echo get_option( 'emgt_system_name' );?></h3>
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
		}
		endif;
	?>	
	
	<div class="panel panel-white">	
	<div class="panel-body">
	<h3> 
		<ul class="nav nav-tabs" id="topmenu" role="tablist">	
<?php	if(REMS_CURRENT_ROLE == "administrator") : ?> 
		  <li class="nav-item <?php echo ($tab=='contract_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_case&tab=contract_list" role="tab"><i class="fa fa-user-secret"></i> <?php _e('Contracts','estate-emgt');?></a>
		  </li>	
		  <li class="nav-item  <?php echo ($tab=='add_contract')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_case&tab=add_contract" role="tab"><i class="fa fa-plus-square"></i> <?php ($edit) ? _e('Edit Contract','estate-emgt') : _e('Add Contract','estate-emgt');?></a>
		  </li>
<?php  endif; ?>
		  <li class="nav-item <?php echo ($tab=='case_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_case&tab=case_list" role="tab"><i class="fa fa-wrench"></i> <?php _e('Cases','estate-emgt');?></a>
		  </li>	
		  <li class="nav-item  <?php echo ($tab=='add_case')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_case&tab=add_case" role="tab"><i class="fa fa-plus-square"></i> <?php ($edit) ? _e('Edit Case','estate-emgt') : _e('Create Case','estate-emgt');?></a>
		  </li>	
		</ul>
	</h3>	
	<div class="tab-content"> 
	
	<?php
	if($tab == "contract_list" && REMS_CURRENT_ROLE == "administrator")
	{ ?><br><br><br>
		<table id="contracts" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php _e("Photo","estate-emgt");?></th>
					<th><?php _e("Contractor","estate-emgt");?></th>
					<th><?php _e("Type","estate-emgt");?></th>
					<th><?php _e("Service","estate-emgt");?></th>
					<th><?php _e("Contact No.","estate-emgt");?></th>						
			<!--	<th><?php _e("Status","estate-emgt");?></th> -->				
					<th><?php _e("Action","estate-emgt");?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e("Photo","estate-emgt");?></th>
					<th><?php _e("Contractor","estate-emgt");?></th>
					<th><?php _e("Type","estate-emgt");?></th>
					<th><?php _e("Service","estate-emgt");?></th>
					<th><?php _e("Contact No.","estate-emgt");?></th>						
			<!--	<th><?php _e("Status","estate-emgt");?></th> -->				
					<th><?php _e("Action","estate-emgt");?></th>
				</tr>
			</tfoot>
			<tbody>
			<?php
				$contracts = $db->emgt_db_get("emgt_contracts");
				foreach($contracts as $ct)
				{ ?>
					<tr>
						<td><?php echo "<img src='{$ct['photo']}'  height='50px' width='50px' class='img-circle'>";?></td>
						<td><?php echo $ct['name'];?></td>
						<td><?php echo $ct['type'];?></td>
						<td><?php echo $ct['services'];?></td>
						<td><?php echo $ct['phone'];?></td>	
				<!--	<td><span class="<?php /* echo ($ct['status']=="Active")?"bg-success":"bg-danger";?>">&nbsp;&nbsp;<?php echo $ct['status']; */?>&nbsp;&nbsp;</span></td>	-->
						<td>
						<a href='?page=emgt_case&eid=<?php echo $ct['id'];?>' class='btn btn-info radius' title="Edit"><i class='fa fa-edit'></i></a>&nbsp;
						<a href='?page=emgt_case&did=<?php echo $ct['id'];?>' class='btn btn-danger radius' onclick="return confirm('Are you sure you want to delete this record?');" title="Delete"><i class='fa fa-times-circle'></i></a>
						</td>
					</tr>			
		<?php	}
			?>
			</tbody>
		</table>
<?php }
	if($tab == "add_contract" && REMS_CURRENT_ROLE == "administrator")
	{ 
		include_once REMS_PLUGIN_DIR."/admin/case_manage/add_contract.php";
	}
	if($tab == "case_list")
	{ 
		include_once REMS_PLUGIN_DIR."/admin/case_manage/case_manage.php";
	}
	if($tab == "add_case")
	{ 
		include_once REMS_PLUGIN_DIR."/admin/case_manage/add_case.php";
	}	
	?>	
	</div>
	</div>
	</div>
</div>