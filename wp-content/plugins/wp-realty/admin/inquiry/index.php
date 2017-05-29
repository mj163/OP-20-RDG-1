<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );

$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "inquiry_list";
$db = new Emgt_Db;
$edit = 0;$view = 0;

if(isset($_POST['add_inquiry']))
{	
	$estate = "";
	$property = get_post(intval($_POST['property_id'])); 
	$estate = $property->post_title;
	
	$data = array("title" => sanitize_text_field($_POST['title']),
				"name" => sanitize_text_field($_POST['name']),
				"email" => sanitize_email($_POST['email']),
				"phone" => intval($_POST['phone']),				
				"message" => sanitize_text_field($_POST['message']),				
				"property_id" => intval($_POST['property_id']),
				"estate" => $estate,
				"date" => date("Y-m-d H:i:s")
				);
	if($_POST['add_inquiry'] == "Save Inquiry")
	{
		$chk = $db->emgt_insert("emgt_inquiry",$data);
		if($chk)
		{
			$success = 1;
		}else{
			$success = 0;
		}
	}
	else if($_POST['add_inquiry'] == "Update Inquiry")
	{
		$chk = $db->emgt_db_update("emgt_inquiry",$data,array('id' => intval($_POST['id'])));
		if($chk)
		{
			$success = 1;
		}else{
			$success = 0;
		}
	}	
}

if(isset($_GET['eid']))
{
	$edit_id = $_GET['eid'];
	$tab = "add_inquiry";
	$edit = 1;
}

if(isset($_GET['did']))
{
	$del_id = $_GET['did'];
	$chk = $db->emgt_delete_record("emgt_inquiry","id",$del_id);
	if($chk)
		{
			$success = 1;
		}else{
			$success = 0;
		}
}

if(isset($_GET['vid']))
{
	$edit_id = $_GET['vid'];
	$view = 1;$edit = 1;
	$tab = $_GET['tab'];
}

if(isset($_POST['add_task']))
{
	$data = array("task_detail"=>sanitize_text_field($_POST['task']),
				"assigned_to"=>$_POST['user'],
				"inquiry_id"=>intval($_POST['inq_id']),
				"schedule_date"=>$_POST['task_date'],
				"status"=>"In Progress",
				"created_date" =>date("Y-m-d H:i:s") );
	$chk = $db->emgt_insert("emgt_tasks",$data);
	if(isset($_POST['email']))
	{
		if($_POST['email'] == "yes")
		{
			$user = get_userdata(sanitize_text_field($_POST['user']));
			$to = $user->user_email;
			$header = 'Content-Type: text/html; charset=UTF-8';			
			$subject = "Task Notification. From :". get_option('emgt_system_name');
			$body = "<p>Hello,</p>";			
			$body .= "<p><strong>Task Detail:</strong>".sanitize_text_field($_POST['task'])."</p>";
			$body .= "<p><strong>Schedule Date:</strong> {$_POST['task_date']}</p>";
			wp_mail($to,$subject,$body,$header);
		}
	}
	($chk) ? $success = 1 : $success = 0;
}

if(isset($_GET['tdid']))
{
	$tdid = intval($_GET['tdid']);
	$chk = $db->emgt_delete_record("emgt_tasks","id",$tdid);
	($chk) ? $success = 1 : $success = 0;
}

if(isset($_GET['ndid']))
{
	$ndid = intval($_GET['ndid']);
	$chk = $db->emgt_delete_record("emgt_notes","id",$ndid);
	($chk) ? $success = 1 : $success = 0;
}

if(isset($_POST['add_note']))
{
	$data = array(
				"inquiry_id"=>$_POST['inq_id'],
				"title"=>$_POST['title'],
				"description"=>$_POST['note'],							
				"created_date" =>date("Y-m-d H:i:s") );
	$chk = $db->emgt_insert("emgt_notes",$data);
	($chk) ? $success = 1 : $success = 0;
}
?>
<script type="text/javascript">
$(document).ready(function() {
	jQuery('#example').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true,"width" : "35px"},	
					  {"bSortable": true},
	                  {"bSortable": true},					
					  {"bSortable": true},	
					  {"bSortable": true,"width" : "20px"},
	                  {"bSortable": true,"width" : "40px"},
	                  {"bSortable": false}]});
} );
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
		  <li class="nav-item <?php echo ($tab=='inquiry_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_inquiries&tab=inquiry_list" role="tab"><i class="fa fa-bars"></i> <?php _e('Inquiry List','estate-emgt');?></a>
		  </li>	
		  <li class="nav-item  <?php echo ($tab=='add_inquiry')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_inquiries&tab=add_inquiry<?php echo ($view) ? "&vid={$edit_id}":''; ?>" role="tab"><i class="fa fa-plus-square"></i> <?php ($edit) ? _e('Edit Inquiry','estate-emgt') : _e('Add Inquiry','estate-emgt');?></a>
		  </li>		
		</ul>
	</h3>	
	<div class="tab-content"> 
		<?php if($view)
	{ ?>
	<div class="row">
		<div class="col-sm-3 border-top-bottom img-rounded">
			<ul class="nav nav-pills" id="topmenu" role="tablist">
			  <li class="<?php echo ($tab=='add_task')? 'active':'';?>">
				<a class="" href="?page=emgt_inquiries&tab=add_task&vid=<?php echo $edit_id;?>" role="tab"><i class="fa fa-plus-square"></i> <?php _e('Add Task','estate-emgt');?></a>
			  </li>
			  <li class="<?php echo ($tab=='add_note')? 'active':'';?>">
				<a class="" href="?page=emgt_inquiries&tab=add_note&vid=<?php echo $edit_id;?>" role="tab"><i class="fa fa-plus-square"></i> <?php _e('Add Note','estate-emgt');?></a>
			  </li>		 
			</ul>	
		</div>
	</div>
<?php } ?>
	<?php if($tab == "inquiry_list") 
		 {?>
		
			 <br>
			 <br>
					<table id="example" class="display" cellspacing="0" width="100%">
						<thead>
							<th><?php _e("Inquiry Date","estate-emgt");?></th>
							<th><?php _e("Inquiry Title","estate-emgt");?></th>
							<th><?php _e("Sender Name","estate-emgt");?></th>
							<th><?php _e("Phone","estate-emgt");?></th>
							<th><?php _e("Email","estate-emgt");?></th>
							<th><?php _e("Message","estate-emgt");?></th>						
							<th><?php _e("Property ID","estate-emgt");?></th>
							<th><?php _e("Estate","estate-emgt");?></th>
							<th><?php _e("Action","estate-emgt");?></th>
						</thead>
						<tfoot>
							<th><?php _e("Inquiry Date","estate-emgt");?></th>
							<th><?php _e("Inquiry Title","estate-emgt");?></th>
							<th><?php _e("Sender Name","estate-emgt");?></th>
							<th><?php _e("Phone","estate-emgt");?></th>
							<th><?php _e("Email","estate-emgt");?></th>
							<th><?php _e("Message","estate-emgt");?></th>						
							<th><?php _e("Property ID","estate-emgt");?></th>
							<th><?php _e("Estate","estate-emgt");?></th>
							<th><?php _e("Action","estate-emgt");?></th>
						</tfoot>
						<tbody>
					<?php		
						if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
						{
							$posts = $db->emgt_get_rows("posts","post_author",get_current_user_id());	
							$inquiries = array();
							foreach($posts as $post)
							{							
								$inq = $db->emgt_get_rows("emgt_inquiry","property_id",$post['ID']);									
								if(!empty($inq))
								{  
									foreach($inq as $in)
									{
										$inquiries[] = $in;
									}									
								}
							}							 
						}else{
							$inquiries = $db->emgt_db_get("emgt_inquiry");
						}
						if(!empty($inquiries))
						{
							foreach($inquiries as $inq)
							{	
								$date = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $inq['date']);
								echo "<tr>";
							echo "<td>{$date}</td><td>{$inq['title']}</td><td>{$inq['name']}</td><td>{$inq['phone']}</td><td>{$inq['email']}</td><td><a href='?page=emgt_inquiries&vid={$inq['id']}&tab=add_inquiry'>{$inq['message']}</a></td><td>{$inq['property_id']}</td><td><a href='".get_permalink($inq['property_id'])."' target='_blank'>{$inq['estate']}</a></td>
								<td>
								<a href='?page=emgt_inquiries&vid={$inq['id']}&tab=add_inquiry' class='btn btn-primary radius' title='View'><i class='fa fa-eye'></i></a>&nbsp;
								<a href='?page=emgt_inquiries&eid={$inq['id']}' class='btn btn-info radius' title='Edit'><i class='fa fa-edit'></i></a>&nbsp;
								<a href='?page=emgt_inquiries&did={$inq['id']}' title='Delete' class='btn btn-danger radius' onclick=\"return confirm('Are you sure you want to delete this record?');\"><i class='fa fa-remove'></i></a>&nbsp;
								</td>";
							echo "</tr>";
							}
						}
					?>	
						
						</tbody>
					</table>
			 
  <?php } 
  if($tab == "add_inquiry") 
		 { ?>  	 
		<br><br>
		<?php include_once REMS_PLUGIN_DIR."/admin/inquiry/add_inquiry.php";?>	 
 <?php } ?>
  <?php  
  if($tab == "add_task") 
		 { ?>  	
		<br><br>
		<?php include_once REMS_PLUGIN_DIR."/admin/inquiry/add_task.php";?>	
  <?php } 
  if($tab == "add_note") 
		 { ?>  	
		<br><br>
		<?php include_once REMS_PLUGIN_DIR."/admin/inquiry/add_note.php";?>	
  <?php } ?>
 
 
	</div>	
	</div> 
	</div> 
	</div> 
</div> <!-- end of page-inner -->
