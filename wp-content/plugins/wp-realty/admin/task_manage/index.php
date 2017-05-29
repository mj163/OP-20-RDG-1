<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "task_list";
$db = new Emgt_Db;
$edit = 0; 
$view = 0;
?>
<?php
if(isset($_GET['eid']))
{
	$edit_id = intval($_GET['eid']);
	$tab = "add_task";
	$edit = 1;
}

if(isset($_GET['vid']))
{
	$edit_id = intval($_GET['vid']);
	$view = 1;$edit = 1;
	$tab = $_GET['tab'];
}

if(isset($_POST['add_note']))
{
	$data = array(
				"task_id"=>intval($_POST['task_id']),
				"title"=>sanitize_text_field($_POST['title']),
				"description"=>sanitize_text_field($_POST['note']),							
				"created_date" =>date("Y-m-d H:i:s") );
	$chk = $db->emgt_insert("emgt_notes",$data);
	($chk) ? $success = 1 : $success = 0;
}

if(isset($_POST['add_task']))
{ 
	$data = array("task_detail"=>sanitize_text_field($_POST['task']),
				"assigned_to"=>intval($_POST['user']),
				"inquiry_id"=>intval($_POST['inq_id']),
				"schedule_date"=>$_POST['task_date'],
				"status"=>"In Progress"
				);
	if($_POST['add_task'] == "Save Task")
	{
		$data["created_date"] = date("Y-m-d H:i:s");
		$chk = $db->emgt_insert("emgt_tasks",$data);		
	}
	if($_POST['add_task'] == "Update Task")
	{
		$data["status"] = sanitize_text_field($_POST['status']);
		$chk = $db->emgt_db_update("emgt_tasks",$data,array("id"=>$_POST['tid']));
	}
	if(isset($_POST['email']))
	{
		if($_POST['email'] == "yes")
		{
			$user = get_userdata(intval($_POST['user']));
			$to = $user->user_email;
			$header = 'Content-Type: text/html; charset=UTF-8';			
			$subject = "Task Notification. From :". get_option('emgt_system_name');
			$body = "<p>Hello,</p>";			
			$body .= "<p><strong>Task Detail:</strong> ".sanitize_text_field($_POST['task'])."</p>";
			$body .= "<p><strong>Schedule Date:</strong> {$_POST['task_date']}</p>";
			wp_mail($to,$subject,$body,$header);
		}
	}
	($chk) ? $success = 1 : $success = 0;
}
if(isset($_GET['did']))
{
	$did = intval($_GET['did']);
	$chk = $db->emgt_delete_record("emgt_tasks","id",$did);
	($chk) ? $success = 1 : $success = 0;
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#task_frm").validationEngine();
	jQuery('#tasks').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},	
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
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
		  <li class="nav-item <?php echo ($tab=='task_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_task&tab=task_list" role="tab"><i class="fa fa-bars"></i> <?php _e('Task List','estate-emgt');?></a>
		  </li>	
		  <li class="nav-item  <?php echo ($tab=='add_task')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_task&tab=add_task<?php echo ($view) ? "&vid={$edit_id}":''; ?>" role="tab"><i class="fa fa-plus-square"></i> <?php ($edit) ? _e('Edit Task','estate-emgt') : _e('Add Task','estate-emgt');?></a>
		  </li>	
		</ul>
	</h3>	
	<div class="tab-content"> 
<?php if($view)
	{ ?>
	<div class="row">
		<div class="col-sm-2 border-top-bottom img-rounded">
			<ul class="nav nav-pills center-block" id="topmenu" role="tablist">
			  <li class="<?php echo ($tab=='add_note')? 'active':'';?>">
				<a class="" href="?page=emgt_task&tab=add_note&vid=<?php echo $edit_id;?>" role="tab"><i class="fa fa-plus-square"></i> <?php _e('Add Note','estate-emgt');?></a>
			  </li>		 
			</ul>	
		</div>
	</div>	
<?php } ?>
<br><br>
	<?php
	if($tab == "task_list")
	{ ?>
	<form class="form-horizontal" method="post" id="task_frm">
		<div class="form-group">
			<label class="control-label col-sm-2"><?php _e("Select Inquiry","estate-emgt");?></label>
			<div class="col-sm-2">			
			<?php
				if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
				{
					$posts = $db->emgt_get_rows("posts","post_author",get_current_user_id());
					foreach($posts as $post)
					{
							$inq_data = $db->emgt_get_rows("emgt_inquiry","property_id",$post['ID']);
							if(!empty($inq_data))
							{
								$inqs[] = $inq_data[0];
							}
					}
				}else{
					$inqs = $db->emgt_db_get("emgt_inquiry");
				}
			?>
				<select name="inq" class="form-control validate[required]">
					<option value="all"><?php _e("All","estate-emgt");?></option>
					<?php
						foreach($inqs as $inq)
						{
							echo "<option value={$inq['id']} ". selected($inq['id'],$_POST['inq']) .">{$inq['title']}</option>";
						}
					?>
				</select>
			</div>
			<input type="submit" value="<?php _e("Show","estate-emgt");?>" name="show_task" class="btn btn-primary">
		</div>				
	</form>
	<hr />	
<?php }
if($tab == "task_list")
{
	if(isset($_POST['show_task']))
		{
			if($_POST['inq'] == "all")
			{
				if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
				{
					$tasks = Emgt_Tasks::emgt_get_tasks_by_user(get_current_user_id());
				}else{
					$tasks = Emgt_Tasks::emgt_get_tasks();
				}
				
			}else{
				$inq = intval($_POST['inq']);				
				$tasks = Emgt_Tasks::emgt_get_task_by_inquiry($inq);
			}
			
		}
		else{
			if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
			{
				$tasks = Emgt_Tasks::emgt_get_tasks_by_user(get_current_user_id());
			}
			else{
				$tasks = Emgt_Tasks::emgt_get_tasks();	
			}
				
		}
	?>
	<table id="tasks" class="display" cellspacing="0" width="100%">
		<thead>
			<th><?php _e("Schedule Date","estate-emgt");?></th>
			<th><?php _e("Assigned To","estate-emgt");?></th>
			<th><?php _e("Status","estate-emgt");?></th>
			<th><?php _e("Property ID","estate-emgt");?></th>
			<th><?php _e("Estate","estate-emgt");?></th>
			<th><?php _e("Action","estate-emgt");?></th>
		</thead>
		<tfoot>
			<th><?php _e("Schedule Date","estate-emgt");?></th>
			<th><?php _e("Assigned To","estate-emgt");?></th>
			<th><?php _e("Status","estate-emgt");?></th>
			<th><?php _e("Property ID","estate-emgt");?></th>
			<th><?php _e("Estate","estate-emgt");?></th>
			<th><?php _e("Action","estate-emgt");?></th>
		</tfoot>
		<tbody>
			<?php
				if($tasks)
				{
					foreach($tasks as $task)
					{
						$date = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $task['schedule_date'] );
						$user = get_userdata($task['assigned_to']);
						$user = $user->display_name;
						echo "<tr>";
						echo "<td>{$date}</td><td>{$user}</td>
						<td><span class='".(($task['status']=='In Progress')?'bg-danger':'bg-success')."'>&nbsp;&nbsp;{$task['status']}&nbsp;&nbsp;</span></td>
						<td>{$task['property_id']}</td><td>{$task['estate']}</td>";
						echo "<td>
						<a href='?page=emgt_task&tab=add_task&vid={$task['id']}' class='btn btn-primary radius' title='View'><i class='fa fa-eye'></i></a>&nbsp;
						<a href='?page=emgt_task&eid={$task['id']}' class='btn btn-success radius' title='Edit'><i class='fa fa-edit'></i></a>&nbsp;
						<a href='?page=emgt_task&did={$task['id']}' class='btn btn-danger radius' title='Delete' onclick=\"return confirm('Are you sure you want to delete this record?');\" ><i class='fa fa-remove'></i></a>
						</td>";
						echo "</tr>";
					}
				}
			?>
		</tbody>
	</table>
<?php }
if($tab == "add_task")
{
	include_once REMS_PLUGIN_DIR."/admin/task_manage/task.php";
} 

if($tab == "add_note") 
 { ?> 	
	<?php include_once REMS_PLUGIN_DIR."/admin/task_manage/note.php";
 } ?>


</div>
</div>
</div>
</div>