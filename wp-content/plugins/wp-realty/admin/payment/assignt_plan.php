<?php
$sel_plan[0]['id'] = "";
if($edit)
{
	$pay = $db->emgt_get_rows("emgt_payments","user_id",$eid);
	$pay = $pay[0]; //getting only single row
	$user = get_userdata($pay['user_id']);
	$role = $user->roles[0];
	$name = $user->first_name ." ". $user->last_name;	
	$sel_plan = $db->emgt_get_rows("emgt_plans","id",$pay['plan']);	
}

?>
<script>
$("document").ready(function(){
	$("#frm").validationEngine();
	$('.ae_date').datetimepicker({
		format: 'YYYY/MM/DD H:mm:ss'
	});
});
</script>
<br><br>
<form method="post" action="?page=emgt_payment" class="form-horizontal" id="frm" <?php if($edit): ?> onsubmit="return confirm('Are you sure you want to save record?');" <?php endif;?>>
	<input type="hidden" id="sel_plan" value="<?php if($edit) echo $pay['plan'];?>">
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="user_type"><?php _e('Select Type','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select name="user_type" id="user_type" class="validate[required]" <?php echo ($edit) ? "disabled" : "";?>>
					<option value=""><?php _e("Select Type","estate-emgt");?></option>
					<option value="emgt_role_agent" <?php if($edit) selected("emgt_role_agent",$role);?>><?php _e("Agent","estate-emgt");?></option>
					<option value="emgt_role_owner" <?php if($edit) selected("emgt_role_owner",$role);?>><?php _e("Property Owner","estate-emgt");?></option>		
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="user"><?php _e('Select User','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select name="user" id="user" class="validate[required]"  <?php echo ($edit) ? "disabled" : "";?>>
					<option value=""><?php _e("Select User","estate-emgt");?></option>
					<?php
						if($edit)
						{
							echo "<option value='{$pay['user_id']}' selected>{$name}</option>";
						}
					?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="name"><?php _e('Gender','estate-emgt');?></label>
			<div class="col-sm-8">
			<label class="radio-inline">
				<input type="radio" name="gender" id="male" class="validate[required] form-control" value="male" <?php if($edit) checked("male",$user->gender);?> disabled /> <?php _e("Male","estate-emgt");?>&nbsp;&nbsp;&nbsp;
			</label>
			<label class="radio-inline">
				<input type="radio" name="gender" id="female" class="validate[required] form-control" value="female" <?php if($edit) checked("female",$user->gender);?> disabled /> <?php _e("Female","estate-emgt");?>
			</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="address"><?php _e('Address','estate-emgt');?></label>
			<div class="col-sm-5">
			<textarea type="text" name="address" id="address" class="validate[required] form-control" disabled><?php echo ($edit)? $user->address : '' ;  ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right	" for="email"><?php _e('Email','estate-emgt');?></label>
			<div class="col-sm-4">	
				<div class="input-group">
				<span class="input-group-addon">@</span>
					<input type="text" name="email" id="email" class="fomr-control validate[required,custom[email]] form-control" value="<?php echo ($edit)?$user->user_email:''; ?>" disabled />
				</div>
			</div>
		</div>		
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="phone"><?php _e('Phone','estate-emgt');?></label>
			<div class="col-sm-4">
				<div class="input-group">
					<span class="input-group-addon">+</span>
					<input type="text" name="phone" id="phone" class="validate[required,custom[phone]] form-control" value="<?php echo ($edit)?$user->phone:''; ?>" disabled />
				</div>
			</div>
		</div>	
		<?php if (REMS_CURRENT_ROLE == "administrator") :?>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="plan"><?php _e('Select Plan','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
				<select name="plan" id="plan" class="validate[required]">
					<option value=""><?php _e("Select Plan","estate-emgt");?></option>
					<?php
						$plans = $db->emgt_db_get("emgt_plans");
						foreach($plans as $plan)
						{
							echo "<option value='{$plan['id']}' ". selected($plan['id'],$sel_plan[0]['id']) ." p='{$plan['price']}'>{$plan['name']}</option>";
						}
					?>
				</select>
			</div>
		</div>	
		<?php endif;
		if (REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner") : 
		?>		
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="plan"><?php _e('Plan','estate-emgt');?></label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?php echo ($edit) ? $sel_plan[0]['name'] : '' ;?>" readonly />
			</div>
		</div>	
		<?php endif;?>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="price"><?php _e('Plan Price','estate-emgt');?></label>
			<div class="col-sm-2">
			<div class="input-group">
				<div class="input-group-addon"><?php echo $currency;?></div>
				<input type="text" class="form-control validate[required]" name="price" id="price" value="<?php echo ($edit) ? $sel_plan[0]['price'] : '' ;?>" readonly />
			</div>
			</div>
		</div>
		<?php 
		if (REMS_CURRENT_ROLE == "administrator" && $edit): 			
			if($pay['activated_date'] != null) :
		?>		
		<div class="form-group edit_plan">
			<label class="col-sm-2 control-label text-right" for="ac_date"><?php _e('Activation date','estate-emgt');?> <span class="require-field"></span></label>
			<div class="col-sm-3">
			<div class='input-group date ae_date'>
				<input type="text" name="ac_date" id="ac_date" class="form-control validate[required]" value="<?php echo ($edit) ? $pay['activated_date'] : '' ;?>" disabled >
				<span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
			</div>
			</div>
		</div>	
		<div class="form-group edit_plan">
			<label class="col-sm-2 control-label text-right" for="ex_date"><?php _e('Expire date','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-3">
			<div class='input-group date ae_date'>
				<input type="text" name="ex_date" id="ex_date" class="form-control validate[required]" value="<?php echo ($edit) ? $pay['expire_date'] : '' ;?>" >
				<span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
			</div>
			</div>
		</div>
		<?php endif;
				endif;?>
		<input type="hidden" name="uid" value="<?php echo ($edit)? $pay['user_id'] :'';?>">
		<br>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-8">   
			<?php if (REMS_CURRENT_ROLE == "administrator") : ?>
				<input type="submit" value="<?php echo ($edit)? _e('Update Plan','estate-emgt') : _e('Assign Plan', 'estate-emgt' ); ?>" name="add_payment" class="btn btn-success" />
			<?php endif;
			if (REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner") : 
			?>
				<a href="?page=emgt_payment" class='btn btn-success'><?php _e('Go Back','estate-emgt');?></a>
			<?php endif; ?>
			</div>
		</div>
</form>
<script>
	$("#plan").change(function(){
		var price = $("option:selected",this).attr("p");
		$("#price").val(price);
	});
	$("#plan").change(function(){		
		var val =  $(this).val();
		if(val == $("#sel_plan").val())
		{
			$(".edit_plan").css("display","block");
			$("#ac_date").removeAttr("disabled");
			$("#ex_date").removeAttr("disabled");
		}else{
			$(".edit_plan").css("display","none");
			$("#ac_date").attr("disabled","disabled");
			$("#ex_date").attr("disabled","disabled");
		}
		
	});
</script>