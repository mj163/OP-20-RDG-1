<?php
if($edit)
{
	$task = $db->emgt_get_rows("emgt_tasks","id",$edit_id);
	$task = $task[0]; //getting only single row	
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#task_frm").validationEngine();
	$('#task_date').datetimepicker({
		format: 'YYYY/MM/DD H:mm:ss'
	});	
});
</script>
<form method="post" id="task_frm" action="?page=emgt_task" class="form-horizontal">	
<fieldset>
	<legend><?php _e("Task Information","estate-emgt");?></legend>
		<div class="form-group">
			<label class="control-label col-sm-2 text-right"><?php _e("Select Inquiry","estate-emgt");?></label>
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
				<select name="inq_id" class="form-control validate[required]" id="select_inquiry">
					<option value=""><?php _e("Select Inquiry","estate-emgt");?></option>
					<?php
						foreach($inqs as $inq)
						{
							echo "<option property='{$inq['property_id']}' value={$inq['id']} ".(($edit) ?selected($inq['id'],$task['inquiry_id']):'') .">{$inq['title']}</option>";
						}
					?>
				</select>
			</div>			
		</div>	
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="user_type"><?php _e('Assign Task To','estate-emgt');?> <span class="require-field">*</span></label>
		<div class="col-sm-8">
		<?php 
			$users = array();
			if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
			{
				$users[] = get_userdata(get_current_user_id());
			}else{				
				$roles = array("emgt_role_agent","emgt_role_owner");			
				foreach($roles as $role)
				{
					$rs = get_users(array("role"=>$role));
					if($rs)
					{
						$users = array_merge($users,$rs);
					}
				}
			}
		?>
			<select name="user" id="user_type" class="validate[required]">
				<option value=""><?php _e("Select User","estate-emgt");?></option>
				<?php 
				foreach($users as $user)
				{
					echo "<option value='{$user->ID}' ".selected($user->ID,$task['assigned_to']).">{$user->first_name} {$user->last_name}</option>";
				}					
				?>
			</select>
		</div>
	</div>	
	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="task"><?php _e('Task Details','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<textarea name="task" id="task" class="form-control validate[required]" ><?php echo ($edit) ? $task['task_detail'] : '';?></textarea>
			</div>
	</div>	
	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Task Schedule Date','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
		<!--<input type="text" name="task_date" id="task_date" class="form-control validate[required]" value="" /> -->
				<div class='input-group date' id='task_date'>
                    <input type='text' class="form-control validate[required]" name="task_date" value="<?php echo ($edit) ? $task['schedule_date']: ''; ?>"/>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
			</div>
	</div>	
	<?php 
	if($edit)
	{ ?>
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Change Status','estate-emgt');?></label>
		<div class="col-sm-4">
			<select name="status">
				<option value="In Progress" <?php selected("In Progress",$task['status']); ?>><?php _e("In Progress","estate-emgt");?></option>
				<option value="Completed" <?php selected("Completed",$task['status']); ?>><?php _e("Completed","estate-emgt");?></option>
			</select>
		</div>
	</div>	
<?php } ?>
	
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Sent Notification Email','estate-emgt');?></label>
		<div class="col-sm-4">
			<label class="radio-inline">
				<input type="radio" name="email" class="form-control" value="yes" /> <?php _e("Yes","estate-emgt");?>&nbsp;&nbsp;&nbsp;
			</label>
			<label class="radio-inline">
				<input type="radio" name="email" class="form-control" value="no" /> <?php _e("No","estate-emgt");?>&nbsp;&nbsp;&nbsp;
			</label>
		</div>
	</div>		
	</fieldset>	
	<input type="hidden" name="tid" value="<?php echo ($edit) ? $edit_id : ''; ?>">
	<div class="col-sm-offset-2 col-sm-8">        	
		<input type="submit" value="<?php echo ($edit) ? _e('Update Task', 'estate-emgt' ) : _e('Save Task', 'estate-emgt' ); ?>" name="add_task" class="btn btn-success"/>
	</div>
</form>
