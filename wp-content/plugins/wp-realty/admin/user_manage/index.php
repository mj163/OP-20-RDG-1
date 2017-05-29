<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "user_list";
$db = new Emgt_Db;
$edit = 0;
if(isset($_POST['add_user']))
{ 
	/* $user = explode("uploads",$_POST["user_photo"]);	
	 $user_photo = $user[1]; */
	
	if($_POST['add_user'] == "Add User")
	{	
		if(username_exists($_POST['username']) == false && email_exists($_POST['email'])== false)
		{
			$user_id = wp_create_user($_POST['username'],$_POST['password'],$_POST['email']);
		}else{
			$success = 4;
			$user_id = 0;
		}
	}
	else{
		$user_id = $_POST['user_id'];
		if(!empty($_POST['password']))
		{
			wp_update_user( array( 'ID' => $user_id,'user_pass'=>$_POST['password'])); 	
		}
		wp_update_user( array( 'ID' => $user_id,"user_email"=>$_POST['email'],"display_name"=>$_POST['first_name']." ".$_POST['last_name']));	
	}
	
	if($user_id != 0)
	{
		$data = array(
					"first_name"=>$_POST['first_name'],
					"middle_name"=>$_POST['middle_name'],
					"last_name"=>$_POST['last_name'],
					// "display_name"=> $_POST['first_name']." ".$_POST['last_name'],
					"gender"=>$_POST['gender'],
					"address"=>$_POST['address'],
					"phone"=>$_POST['phone'],
					"wp_capabilities"=>array($_POST['role']=>true),
					"user_photo" => $_POST["user_photo"] /*$user_photo*/
					);
		foreach($data as $key=>$value)
		{
			$chk = update_user_meta($user_id,$key,$value);
			if($chk == true)
			{
				$success = 1;
			}		
		}
		update_user_meta($user_id,"hash","deactivated");
		$tab = "user_list";
	}
}


if(isset($_GET['eid']))
{
	$edit_id = $_GET['eid'];
	$tab = "add_user";
	$edit = 1;
}

if(isset($_GET['did']))
{
	$del_id = $_GET['did'];
	$chk = $db->emgt_delete_usedata($del_id);
	if($chk)
		{
			$success = 1;
		}else{
			$success = 2;
		}
}

if(isset($_GET['act_id']))
{
	 if(delete_user_meta($_GET['act_id'],"emgt_hash"))
	 {
		$success = 3;
		$user = get_userdata($_GET['act_id']);
		$to = $user->user_email;
		$sname = get_option("emgt_system_name");
		$semail = get_option("emgt_system_email");
		$header[] = 'Content-Type: text/html; charset=UTF-8';
		$header[] = "From : {$sname} <{$semail}>";		
		$subject = get_option('emgt_system_name') .": Account Activated.";
		$body = "<p>Hello, {$user->display_name}</p>";			
		$body .= "<p>Your Account is activated successfully.";
		$body .= "<p>Now, You can login to <a href='".esc_url(home_url('/'))."'>{$sname}</a>.</p>";
		$body .= "<p>Thank you for joining us.</p>";
		wp_mail($to,$subject,$body,$header);
	 }else{
		 $success = 2;
	 }	
}	
?>

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
			CASE 3: ?>
					<div class="alert alert-success alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-check-circle"></i>&nbsp;<?php _e('Success !','estate-emgt');?></strong> <?php _e('Member is activated successfully!','estate-emgt');?>
				</div>	
			<?php
			break;	
			CASE 4:?>
					<div class="alert alert-danger alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-warning"></i>&nbsp;<?php _e('Error !','estate-emgt');?></strong> <?php _e('Username or Email-id already exists!','estate-emgt');?>
					</div>		
				</div>					
			<?php	
		}
		endif;
	?>	
	<div class="panel panel-white">	
	<div class="panel-body">
	<h3> 
		<ul class="nav nav-tabs" id="topmenu" role="tablist">	 
		  <li class="nav-item <?php echo ($tab=='user_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_users&tab=user_list" role="tab"><i class="fa fa-users"></i> <?php _e('Users List','estate-emgt');?></a>
		  </li>	
		  <li class="nav-item  <?php echo ($tab=='add_user')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_users&tab=add_user" role="tab"><?php echo ($edit) ? "<i class='fa fa-user'></i> ". __('Update User','estate-emgt') : "<i class='fa fa-user-plus'></i> " . __('Add User','estate-emgt');?></a>
		  </li>
		</ul>
	</h3>	
	<div class="tab-content"> 
	<?php 
	if($tab == "user_list")
	{?>
	
	<br><br>
	<script type="text/javascript">
		$(document).ready(function() {
			jQuery('#example').DataTable({
				"aoColumns":[
							  {"bSortable": true},
							  {"bSortable": true},	
							  {"bSortable": true},
							  {"bSortable": true},
							  {"bSortable": true},							  
							  {"bSortable": false}]});
		} );
	</script>  
	<table id="example" class="display" cellspacing="0" width="100%">
		<thead>
			<th><?php _e("Photo","estate-emgt");?></th>
			<th><?php _e("Name","estate-emgt");?></th>
			<th><?php _e("Phone","estate-emgt");?></th>
			<th><?php _e("Type","estate-emgt");?></th>
			<th><?php _e("Email","estate-emgt");?></th>
			<th><?php _e("Action ID","estate-emgt");?></th>							
		</thead> 
		<tfoot>
			<th><?php _e("Photo","estate-emgt");?></th>
			<th><?php _e("Name","estate-emgt");?></th>
			<th><?php _e("Phone","estate-emgt");?></th>
			<th><?php _e("Type","estate-emgt");?></th>
			<th><?php _e("Email","estate-emgt");?></th>
			<th><?php _e("Action ID","estate-emgt");?></th>							
		</tfoot>
		<tbody>
		<?php
		$role_names = wp_roles()->get_names();
		$roles = array("emgt_role_agent","emgt_role_owner");
		$users = array();		
		foreach($roles as $role)
		{
			$results = get_users(array('role' => $role));			
			if($results)
			{
				$users = array_merge($users,$results);
			}
		}		
		$upload = wp_upload_dir();
		foreach($users as $user)
		{ ?>
			<tr>
				<td><?php echo "<img src='".get_user_meta($user->ID,'user_photo',true)."'  height='60px' width='65px' class='img-circle'>";?></td>
				<td><?php echo $user->first_name ." ". $user->last_name;?></td>
				<td><?php echo $user->phone;?></td>
				<td><?php echo $role_names[$user->roles[0]];?></td>
				<td><?php echo $user->user_email;?></td>				
				<td>
				<a href='?page=emgt_users&eid=<?php echo $user->ID;?>' class='btn btn-info radius' title="Edit"><i class='fa fa-edit'></i></a>&nbsp;
				<a href='?page=emgt_users&did=<?php echo $user->ID;?>' class='btn btn-danger radius' onclick="return confirm('Are you sure you want to delete this record?');" title="Delete"><i class='fa fa-times-circle'></i></a>
				<?php 
				if(get_user_meta($user->ID,"emgt_hash"))
				{?>
					<a href='?page=emgt_users&act_id=<?php echo $user->ID;?>' class="btn btn-warning radius" onclick="return confirm('Are you sure you want activate this account?');"><i class="fa fa-check"></i> <?php _e("Activate Now","estate-emgt")?></a>
		   <?php }
				else{?>
					<span style="padding:6px 12px;" class="bg bg-success">&nbsp;&nbsp;<?php _e("Activated","estate-emgt");?>!&nbsp;&nbsp;</span>
		<?php	}
				?>
				</td>
			</tr>		
<?php	}
		?>		
		</tbody>
		
		</table>
	
	<?php }
	if($tab == "add_user")
	{
		include_once REMS_PLUGIN_DIR."/admin/user_manage/add_user.php";
	} ?>
	</div> 
	
	
	</div> 
	</div> 
	</div> 
</div> <!-- end of page-inner -->