<?php
if($edit)
{
	$inq = $db->emgt_get_rows("emgt_inquiry","id",$edit_id);
	$inq = $inq[0]; //getting only single row
}else{
	$inq['property_id'] = "";
}
?>
<script>
$("document").ready(function(){
	$("#frm").validationEngine();
	$('#task_date').datepicker({
			  changeMonth: true,
			  changeYear: true,	
			  dateFormat: "yy-mm-dd",
			  yearRange:'-65:+0',
			  onChangeMonthYear: function(year, month, inst) {
					$(this).val(month + "/" + year);
			  }                    
         }); 
});
</script>
<form method="post" action="?page=emgt_inquiries" class="form-horizontal" id="frm">
	<fieldset>
		<legend><?php _e("Inquiry Information","estate-emgt"); ?></legend>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="title"><?php _e('Title','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="title" id="title" class="validate[required] form-control" value="<?php echo ($edit)?$inq['title']:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="name"><?php _e('Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="name" id="name" class="validate[required] form-control" value="<?php echo ($edit)?$inq['name']:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="email"><?php _e('Email','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="email" id="email" class="validate[required,custom[email]] form-control" value="<?php echo ($edit)?$inq['email']:'';  ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="phone"><?php _e('Phone','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="phone" id="phone" class="validate[required,custom[phone]] form-control" value="<?php echo ($edit)?$inq['phone']:''; ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="message"><?php _e('Message','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<textarea name="message" id="message" class="validate[required] form-control" value=""><?php echo ($edit)?$inq['message']:'';?></textarea>
			</div>
		</div>		
	<!--	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="property_id"><?php //_e('Property ID','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="property_id" id="property_id" class="validate[required,custom[onlyNumberSp]] form-control" value="<?php //echo ($edit)?$inq['property_id']:'';?>" />
			</div>
		</div>
	-->
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
			CASE "emgt_role_owner" :
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
			echo "<option value={$id} ".selected($inq['property_id'],$id).">{$id} : {$name}</option>";
		}
		?>
			</select>
		</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="estate"><?php _e('Estate','estate-emgt');?></label>
			<div class="col-sm-5">
			<input type="text" name="estate" id="estate" class="validate[required] form-control" value="<?php echo ($edit)?$inq['estate']:'';?>" readonly />
			</div>
			<div class="cols-sm-1 t_search">
				<img src="<?php echo REMS_PLUGIN_URL; ?>/images/search.gif" style="height: 34px;width: 35px;">
			</div>
		</div>		
		
		<input type="hidden" name="id" value="<?php echo ($edit)?$inq['id']:'';?>">
		<div class="col-sm-offset-2 col-sm-8">        	
        	<input type="submit" value="<?php echo ($edit)? _e('Update Inquiry','estate-emgt') : _e('Save Inquiry', 'estate-emgt' ); ?>" name="add_inquiry" class="btn btn-success"/>
        </div>
	</fieldset>
</form>