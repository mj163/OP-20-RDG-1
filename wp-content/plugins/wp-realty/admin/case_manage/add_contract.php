<?php
if($edit)
{
	$contracts = $db->emgt_get_rows("emgt_contracts","id",$edit_id);
	$ct = $contracts[0]; //getting only single row
}
?>
<script>
$("document").ready(function(){
	$("#frm2").validationEngine();
});
</script>
<br>
<form method="post" action="?page=emgt_case" class="form-horizontal" id="frm2">
	<fieldset>
		<legend><?php _e('Contract Details','estate-emgt');?></legend>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="name"><?php _e('Contractor Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="name" id="name" class="validate[required] form-control" value="<?php echo ($edit)?$ct['name']:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="address"><?php _e('Address','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<textarea type="text" name="address" id="address" class="validate[required] form-control" /><?php echo ($edit)?$ct['address']:'';?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="email"><?php _e('Email','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="email" id="email" class="validate[required,custom[email]] form-control" value="<?php echo ($edit)?$ct['email']:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="phone"><?php _e('Phone','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<div class="input-group">
				<span class="input-group-addon">+</span>
				<input type="text" name="phone" id="phone" class="validate[required,custom[phone]] form-control" value="<?php echo ($edit)?$ct['phone']:''; ?>" />
			</div>			
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="type"><?php _e('Type','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select name="type" id="type">
					<option value="agency" <?php if ($edit) selected("agency",$ct['type']);?>><?php _e('Agency','estate-emgt');?></option>
					<option value="individual" <?php if ($edit) selected("individual",$ct['type']);?>><?php _e('Individual','estate-emgt');?></option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="services"><?php _e('Services','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="services" id="services" class="validate[required] form-control" value="<?php echo ($edit)?$ct['services']:'';?>" />
			</div>
		</div>		
<!--  <div class="form-group">
			<label class="col-sm-2 control-label text-right" for="duration"><?php _e('Contract Duration','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-1">
				<input type="text" class="form-control" name="duration" id="duration" value="<?php echo ($edit) ? $ct['duration'] : '' ;?>"  />
			</div>
			<div class="col-sm-2">
				<select name="period" id="period" class="validate[required]">
					<option value="month" <?php /*if ($edit)selected("month",$ct['period']);?>><?php _e('Month','estate-emgt');?></option>
					<option value="year" <?php if ($edit)selected("year",$ct['period']);?>><?php _e('Year','estate-emgt');?></option>
					<option value="day" <?php if ($edit)selected("day",$ct['period']);?>><?php _e('Day','estate-emgt');?></option>
					<option value="week" <?php if ($edit) selected("week",$ct['period']);?>><?php _e('Week','estate-emgt'); */?></option>
				</select>
			</div>
		</div>	-->	
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="price"><?php _e('Contract Price','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-2">
			<div class="input-group">
				<div class="input-group-addon"><?php echo emgt_get_currency_symbol(get_option("emgt_system_currency"));?></div>
				<input type="text" class="form-control validate[required]" name="price" id="price" value="<?php echo ($edit) ? $ct['price'] : '' ;?>"  />
			</div>
			</div>
		</div>
		<?php 
/*	if($edit)
	{ ?>
	<div class="form-group">
		<label class="col-sm-2 control-label text-right" for="task_date"><?php _e('Change Status','estate-emgt');?></label>
		<div class="col-sm-4">
			<select name="status">
				<option value="Active" <?php selected("Active",$ct['status']); ?>><?php _e("Active","estate-emgt");?></option>
				<option value="Expired" <?php selected("Expired",$ct['status']); ?>><?php _e("Expired","estate-emgt");?></option>
			</select>
		</div>
	</div>	
<?php } */ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="emgt_user_avatar_url"><?php _e('Photo','estate-emgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" id="emgt_user_avatar_url" name="photo" class="validate[required]" value="<?php  echo ($edit) ? $ct['photo'] : get_option( 'emgt_system_user_logo' ); ?>" />
       				 <input id="upload_user_avatar_button" type="button" class="button" value="<?php  _e( 'Upload image', 'estate-emgt' ); ?>" />
       				 <span class="description"><?php _e('Upload image.', 'estate-emgt' ); ?></span>                     
                     <div id="upload_user_avatar_preview" style="min-height: 100px;">
			<img style="max-width:100%;" src="<?php echo ($edit) ? $ct['photo'] : get_option('emgt_system_user_logo'); ?>" />			
			</div>
		</div>
		</div>	
		<input type="hidden" name="cid" value="<?php echo ($edit) ? $_GET['eid'] : '' ; ?>">
		<br><br>
		<div class="col-sm-offset-2 col-sm-8">        	
        	<input type="submit" value="<?php echo ($edit)? _e('Update Contract','estate-emgt') : _e('Add Contract', 'estate-emgt' ); ?>" name="add_contract" class="btn btn-success"/>
        </div>
	</fieldset>
<form>
<br><br><br><br>