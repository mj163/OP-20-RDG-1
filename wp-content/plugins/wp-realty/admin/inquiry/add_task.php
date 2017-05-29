<script type="text/javascript">
$(document).ready(function() {
	$("#task_frm").validationEngine();
	$('#task_date').datetimepicker({
		format: 'YYYY/MM/DD H:mm:ss'
	});
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


<form method="post" id="task_frm" class="form-horizontal">	
<fieldset>
	<legend><?php _e("Task Information","estate-emgt");?></legend>
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="user_type"><?php _e('Assign Task To','estate-emgt');?> <span class="require-field">*</span></label>
		<div class="col-sm-8">
		<?php 
			$roles = array("emgt_role_agent","emgt_role_owner");
			$users = array();			
			if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
			{
				$users[] = get_userdata(get_current_user_id());
				$member = 1;
			}else{
				$inq_id = $_GET['vid'];
				$prop = $db->emgt_get_rows("emgt_inquiry","id",$inq_id);
				$users[] = Emgt_Tasks::emgt_get_user_by_property($prop[0]['property_id']);
				$member = 0;				
			}			
		?>
			<select name="user" id="user_type" class="validate[required]">
				<option value=""><?php _e("Select User","estate-emgt");?></option>
				<?php 
				if($member == 0)
				{
					 foreach($users as $user)
					{
						$u = get_userdata($user['post_author']);
						echo "<option value={$u->ID}>{$u->display_name}</option>";	
					}
				}
				else if($member)
				{
					 foreach($users as $user)
					{
						echo "<option value={$user->ID}>{$user->display_name}</option>";	
					}
				}
				?>
			</select>
		</div>
	</div>	
	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="task"><?php _e('Task Details','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<textarea name="task" id="task" class="form-control validate[required]" value=""></textarea>
			</div>
	</div>	
	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Task Schedule Date','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
		<!--<input type="text" name="task_date" id="task_date" class="form-control validate[required]" value="" /> -->
				<div class='input-group date' id='task_date'>
                    <input type='text' class="form-control validate[required]" name="task_date" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
			</div>
	</div>	
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Sent Notification Email','estate-emgt');?></label>
		<div class="col-sm-4">
			<label class="radio-inline">
				<input type="radio" name="email" class="form-control" value="yes" checked /> <?php _e("Yes","estate-emgt");?>&nbsp;&nbsp;&nbsp;
			</label>
			<label class="radio-inline">
				<input type="radio" name="email" class="form-control" value="no" /> <?php _e("No","estate-emgt");?>&nbsp;&nbsp;&nbsp;
			</label>
		</div>
	</div>		
	</fieldset>
	<input type="hidden" name="inq_id" value="<?php echo $_GET['vid']; ?>">
	<div class="col-sm-offset-2 col-sm-8">        	
		<input type="submit" value="<?php _e('Save Task', 'estate-emgt' ); ?>" name="add_task" class="btn btn-success"/>
	</div>
</form>
<br><br><br><br>
<legend><?php _e("Assigned Tasks","estate-emgt"); ?></legend>
<script type="text/javascript">
$(document).ready(function() {
	jQuery('#tasks').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},	
					  {"bSortable": true},
					  {"bSortable": true},
	                  {"bSortable": false}]});
} );
</script>   
<table id="tasks" class="display" cellspacing="0" width="100%">
	<thead>
		<th><?php _e("Created Date","estate-emgt");?></th>
		<th><?php _e("Assigned To","estate-emgt");?></th>		
		<th><?php _e("Task Description","estate-emgt");?></th>
		<th><?php _e("Status","estate-emgt");?></th>
		<th><?php _e("Action","estate-emgt");?></th>
	</thead>
	<tfoot>
		<th><?php _e("Created Date","estate-emgt");?></th>
		<th><?php _e("Assigned To","estate-emgt");?></th>
		<th><?php _e("Task Description","estate-emgt");?></th>
		<th><?php _e("Status","estate-emgt");?></th>		
		<th><?php _e("Action","estate-emgt");?></th>
	</tfoot>
	<tbody>
	<?php 
		$tasks = $db->emgt_get_rows("emgt_tasks","inquiry_id",$edit_id);	
		foreach($tasks as $task)
		{ 
			$name = get_userdata($task['assigned_to']);
			$name = $name->first_name ." ". $name->last_name;
			echo "<tr>";
			echo "<td>{$task['created_date']}</td><td>{$name}</td><td>{$task['task_detail']}</td><td>{$task['status']}</td>";
			echo "<td>
				<button type='button' class='btn btn-primary' t_id='{$task['id']}' id='view_task'><i class='fa fa-eye'></i></button>&nbsp;
				<a href='?page=emgt_inquiries&tab=add_task&vid={$edit_id}&tdid={$task['id']}' class='btn btn-danger' onclick=\"return confirm('Are you sure you want to delete this record ?');\"><i class='fa fa-remove'></i></a>
				</td>";
			echo "</tr>";
		}
	?>
	</tbody>
</table>
