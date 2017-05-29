<?php
if($edit)
{
	$user = get_userdata($edit_id);
	$upload = wp_upload_dir();
}
?>
<script>
$("document").ready(function(){
	$("#frm").validationEngine();
});
</script>
<form method="post" class="form-horizontal" id="frm">
	<fieldset>
		<legend><?php _e('User Details','estate-emgt');?></legend>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="first_name"><?php _e('First Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="first_name" id="first_name" class="validate[required] form-control" value="<?php echo ($edit)?$user->first_name:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="middle_name"><?php _e('Middle Name','estate-emgt');?></label>
			<div class="col-sm-8">
			<input type="text" name="middle_name" id="middle_name" class="form-control" value="<?php echo ($edit)?$user->middle_name:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="last_name"><?php _e('Last Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="last_name" id="last_name" class="validate[required] form-control" value="<?php echo ($edit)?$user->last_name:'';?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="name"><?php _e('Gender','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="radio" name="gender" id="name" class="validate[required] form-control" value="male" <?php if($edit) checked("male",$user->gender);?> checked /> <?php _e("Male","estate-emgt");?>&nbsp;&nbsp;&nbsp;
			<input type="radio" name="gender" id="name" class="validate[required] form-control" value="female" <?php if($edit) checked("female",$user->gender);?> /> <?php _e("Female","estate-emgt");?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="email"><?php _e('Email','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<div class="input-group">
				<span class="input-group-addon">@</span>
				<input type="text" name="email" id="email" class="validate[required,custom[email]] form-control" value="<?php echo ($edit)?$user->user_email:'';  ?>" />
			</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="address"><?php _e('Address','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<textarea type="text" name="address" id="address" class="validate[required] form-control"><?php echo ($edit)?$user->address:'';  ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="phone"><?php _e('Mobile Number','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<div class="input-group">
				<span class="input-group-addon">+</span>
				<input type="text" name="phone" id="phone" class="validate[required,custom[phone]] form-control" value="<?php echo ($edit)?$user->phone:''; ?>" />
			</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="username"><?php _e('Username','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="username" id="username" class="validate[required] form-control" value="<?php echo ($edit)?$user->user_login:'';?>" <?php echo ($edit) ? "readonly" : "";?>/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="password"><?php _e('Password','estate-emgt');?><?php if(!$edit) { ?><span class="require-field">*</span><?php } ?></label>
			<div class="col-sm-8">
			<input type="password" name="password" id="password" class="<?php echo (!$edit) ? "validate[required]" : "";?> form-control" value=""/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="property_id"><?php _e('Role','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select name="role" id="type" class="validate[required]">
					<option value=""><?php _e("Select Role","estate-emgt");?></option>
					<option value="emgt_role_agent" <?php if($edit) selected("emgt_role_agent",$user->roles[0]);?>><?php _e("Agent","estate-emgt");?></option>
					<option value="emgt_role_owner" <?php if($edit) selected("emgt_role_owner",$user->roles[0]);?>><?php _e("Property Owner","estate-emgt");?></option>		
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="upload_user_avatar_button"><?php _e('Photo','estate-emgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" id="emgt_user_avatar_url" name="user_photo" class="validate[required]" value="<?php  echo ($edit && $user->user_photo != '') ? $user->user_photo : get_option( 'emgt_system_user_logo' ); ?>" />
       				 <input id="upload_user_avatar_button" type="button" class="button" value="<?php  _e( 'Upload image', 'estate-emgt' ); ?>" />
       				 <span class="description"><?php _e('Upload image.', 'estate-emgt' ); ?></span>                     
                     <div id="upload_user_avatar_preview" style="min-height: 100px;">
			<img style="max-width:100%;" src="<?php echo ($edit && $user->user_photo != '') ?  $user->user_photo : get_option('emgt_system_user_logo'); ?>" />			
			</div>
		</div>
		</div>	
		<input type="hidden" name="user_id" value="<?php echo ($edit)?$user->ID:'';?>">
		<div class="col-sm-offset-2 col-sm-8">        	
        	<input type="submit" value="<?php echo ($edit)? _e('Update User','estate-emgt') : _e('Add User', 'estate-emgt' ); ?>" name="add_user" class="btn btn-success"/>
        </div>
	</fieldset>
</form>