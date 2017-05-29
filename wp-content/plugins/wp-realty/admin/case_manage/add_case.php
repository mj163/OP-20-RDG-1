<?php
if($edit)
{
	$cases = $db->emgt_get_rows("emgt_cases","id",$edit_id);
	$case = $cases[0]; //getting only single row
}
?>
<script>
$("document").ready(function(){
	$("#frm2").validationEngine();
});
</script>
<br>
<form method="post" action="?page=emgt_case" class="form-horizontal" id="frm2">
	<input type="hidden" name="ct_mail" value="" id="ct_email">
	<fieldset>
		<legend><?php _e('Case Details','estate-emgt');?></legend>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="name"><?php _e('Contractor Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<?php 
				$cts = $db->emgt_db_get("emgt_contracts");
			?>
				<select name="name" class="validate[required]" id="sel_cont" onchange="ct_change()">
					<option value="" email=""><?php _e("Select Contractor","estate-emgt");?></option>
					<?php
					foreach($cts as $ct)
					{
						echo "<option value='{$ct['id']}' ".selected($ct['id'],$case['assigned_to'])." email='{$ct['email']}'>{$ct['name']}</option>";
					}
					?>
				</select>
			</div>
		</div>	
		<!-- <div class="form-group">
			<label class="col-sm-2 control-label text-right" for="property_id"><?php _e('Property ID','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" autocomplete="off" name="property_id" id="property_id" class="validate[required,custom[onlyNumberSp]] form-control" value="<?php echo ($edit)?$case['property_id']:'';?>" />
			</div>
		</div> -->
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="property_id"><?php _e('Property ID','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select class="validate[required]" name="property_id" id="property_id">
				<option value=""><?php _e("Select Property","estate-emgt"); ?></option>
		<?php 
		
		
		switch(REMS_CURRENT_ROLE)
		{
			CASE "emgt_role_agent" :
				$args = array(
							'post_type'=>'emgt_add_listing',
							'post_status'=>'publish',
							'author' => get_current_user_id()
							); 
				$properties = get_posts($args); 
			break;
			
			CASE "administrator" :	
				$users1 = get_users(array("role"=>"emgt_role_agent","fields" => "ID")); 
				$users2 = get_users(array("role"=>"emgt_role_owner","fields" => "ID")); 
				$u_ids = array_merge($users1,$users2);
				$users3[] = get_current_user_id();
				$u_ids[] = get_current_user_id();
				$args = array(
							'post_type'=>'emgt_add_listing',
							'post_status'=>'publish',
							'author' => implode(',', $u_ids)
							); 
				$properties = get_posts($args);
			break;
		}
		
	
		$p_list = array();
		foreach($properties as $property)
		{
			$p_list[$property->ID] = $property->post_title;
		}
		
		foreach($p_list as $id => $name )
		{
			echo "<option value={$id}>{$id} : {$name}</option>";
		}
		?>
			</select>
		</div>
		</div>	
		<input type="hidden" name="estate" id="estate" class="validate[required] form-control" value="<?php echo ($edit)?$case['estate']:'';?>" />
		
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="complain"><?php _e('Complain Detail','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<textarea type="text" name="complain" id="complain" class="validate[required] form-control" /><?php echo ($edit)?$case['complain']:'';?></textarea>
			</div>
		</div>	
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="by"><?php _e('Complain By','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<input type="text" name="by" id="by" class="validate[required] form-control" value='<?php echo ($edit)?$case['complain_by']:'';?>'>
			</div>
		</div>
		<?php 
	if($edit)
	{ ?>
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Change Status','estate-emgt');?></label>
		<div class="col-sm-4">
			<select name="status">
				<option value="Pending" <?php selected("Pending",$case['status']); ?>><?php _e("Pending","estate-emgt");?></option>
				<option value="Completed" <?php selected("Completed",$case['status']); ?>><?php _e("Completed","estate-emgt");?></option>
			</select>
		</div>
	</div>	
<?php } ?>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="phone"><?php _e('Phone','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-3">
			<div class="input-group">
				<span class="input-group-addon">+</span>
				<input type="text" name="phone" id="phone" class="validate[required,custom[phone]] form-control" value="<?php echo ($edit)?$case['phone']:''; ?>" />
			</div>			
			</div>
		</div>		
		<input type="hidden" name="csid" value="<?php echo ($edit) ? $_GET['cseid'] : '' ; ?>">
		<br><br>
		<div class="col-sm-offset-2 col-sm-8">        	
        	<input type="submit" value="<?php echo ($edit)? _e('Update Case','estate-emgt') : _e('Add Case', 'estate-emgt' ); ?>" name="add_case" class="btn btn-success"/>
        </div>
	</fieldset>
<form>
<br><br><br><br>
<script>
function ct_change()
{
	var status = $("#sel_cont option:selected").attr("email");
	$("#ct_email").val(status);	
}

// $("#property_id").change(function(){
	// var pid = $("#property_id option:selected").val();	
	// $("#estate").val(pid);
// });
	
</script>