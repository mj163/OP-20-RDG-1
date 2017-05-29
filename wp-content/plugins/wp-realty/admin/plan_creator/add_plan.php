<?php 
$features = array();
$plan = array();
if($edit)
{
	$plan = $db->emgt_get_rows("emgt_plans","id",$eid);	
	$plan = $plan[0];	
	$features = explode(",",$plan['features']);
}
?>
<script>
$("document").ready(function(){
	$("#frm").validationEngine();
});
</script>
<br><br>
<form method="post" action="?page=emgt_plan" class="form-horizontal" id="frm">
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="name"><?php _e('Plan Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
				<input type="text" name="name" id="name" class="form-control validate[required]" value="<?php echo ($edit) ? $plan['name'] : '' ;?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="plan_type"><?php _e('Plan Price','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-2">
			<div class="input-group">
				<div class="input-group-addon"><?php echo $currency;?></div>
				<input type="text" class="form-control validate[required]" name="price" id="price" value="<?php echo ($edit) ? $plan['price'] : '' ;?>"  />
			</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="plan_type"><?php _e('Ads Quantity','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
				<input type="text" name="quantity" id="quantity" class="form-control validate[required,min[1]],custom[integer]" value="<?php echo ($edit) ? $plan['quantity'] : '' ;?>"  />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="features"><?php _e('Set Features','estate-emgt');?></label>
			<div class="col-sm-8">
				<div class="checkbox">
				  <label>
					<input type="checkbox" name="feature[]" value="video" <?php echo (in_array("video",$features)) ? 'checked' : '' ;?> ><?php _e('Can Add Video','estate-emgt');?>					
				  </label>
				</div>
				<div class="checkbox">
				  <label>
					<input type="checkbox" name="feature[]" value="visibility" <?php echo (in_array("visibility",$features)) ? 'checked' : '' ;?> ><?php _e('Preferred Visibility','estate-emgt');?>						
				  </label>
				</div>
				<div class="checkbox">
				  <label>
					<input type="checkbox" name="feature[]" value="map" <?php echo (in_array("map",$features)) ? 'checked' : '' ;?> ><?php _e('Map Location','estate-emgt');?>						
				  </label>
				</div>
			</div>
		</div>
		<br>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="plan_validity"><?php _e('Plan Validity Period','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-1">
				<input type="text" class="form-control" name="plan_validity" id="plan_validity" value="<?php echo ($edit) ? $plan['plan_validity'] : '' ;?>"  />
			</div>
			<div class="col-sm-2">
				<select name="plan_period" id="period" class="validate[required]">
					<option value="month" <?php if ($edit)selected("month",$plan['plan_period']);?>><?php _e('Month','estate-emgt');?></option>
					<option value="year" <?php if ($edit)selected("year",$plan['plan_period']);?>><?php _e('Year','estate-emgt');?></option>
					<option value="day" <?php if ($edit)selected("day",$plan['plan_period']);?>><?php _e('Day','estate-emgt');?></option>
					<option value="week" <?php if ($edit) selected("week",$plan['plan_period']);?>><?php _e('Week','estate-emgt');?></option>
				</select>
			</div>
		</div>		
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="ads_period"><?php _e('Single Ad Listing Period','estate-emgt');?> <span class="require-field">*</span></label>
			<div id="ads_validity">			
			<div class="col-sm-1">
				<input type="text" class="form-control" name="ads_validity" id="ads_period" value="<?php echo ($edit) ? $plan['ads_validity'] : '' ;?>"  />				
			</div>
			<div class="col-sm-5">
				
				<select name="ads_period" id="ads_period" class="validate[required]">
					<option value="month" <?php if ($edit) selected("month",$plan['ads_period']);?>><?php _e('Month','estate-emgt');?></option>
					<option value="year" <?php if ($edit)selected("year",$plan['ads_period']);?>><?php _e('Year','estate-emgt');?></option>
					<option value="day" <?php if ($edit)selected("day",$plan['ads_period']);?>><?php _e('Day','estate-emgt');?></option>
					<option value="week" <?php if ($edit) selected("week",$plan['ads_period']);?>><?php _e('Week','estate-emgt');?></option>
				</select>
			</div>
			</div>
			<div class="col-sm-4" id="info" style="display:none;">
				<p>
					<label class="control-label text-danger"><?php _e("Period will be same as plan period !");?></label>
				</p>
			</div>
		</div>		
		<br>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-8">        	
				<input type="submit" value="<?php echo ($edit)? _e('Update Plan','estate-emgt') : _e('Add Plan', 'estate-emgt' ); ?>" name="save_plan" class="btn btn-success"/>
			</div>
		</div>
		<input type="hidden" name="uid" value="<?php echo ($edit)? $plan['id'] : '';?>">
</form>
<script>
$("body").on("keyup","#quantity",function(){
	var quantity = $(this).val();
	if(quantity > 1)
	{
		$("#ads_validity").show();
		$("#info").css("display","none");
	}else{
		$("#ads_validity").hide();
		$("#info").css("display","block");
	}	
});
$(document).ready(function(){
	var ads_p = $("#quantity").val();
	if(ads_p == "1")
	{
		$("#ads_validity").hide();
		$("#info").css("display","block");
	}
});
</script>
