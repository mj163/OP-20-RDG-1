<?php 
$db = new Emgt_Db;

if(isset($edit) && $edit == 1)
{
	$data = $data[0];
}
else{
	$edit = 0;	
}
?>
<script>
	jQuery(document).ready(function() {
		$('#add_field_form').validationEngine();	
	});
</script>
<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
  <h4 id="myLargeModalLabel" class="modal-title"><i class="fa fa-cogs"></i>&nbsp; <?php _e('Field Configuration','estate-emgt');?></h4>
</div>
<hr>
<div class="panel panel-white">
<form method="post" id="add_field_form">
			<input type="hidden" name="field_id" id="field_id" value="<?php if($edit) echo $data['field_id'];?>">
			<div class="form-group row">
				<label for="f_type" class="col-sm-3 text-right bold"><?php _e('Field Type','estate-emgt');?> :</label>
				<input type="text" name="f_type" id="f_type" class="col-sm-4" value="Dropdown" readonly>		
			</div>
			<div class="form-group row" id="name_div">						
				<label for="field_name" class="col-sm-3 text-right bold"><?php _e('Field Name','estate-emgt');?> :</label>
				<input type="text" name="field_name" id="field_name" class="col-sm-4 validate[required]" placeholder="<?php _e('Enter Unique Name','estate-emgt');?>" value="<?php if($edit) echo $data['field_name']; ?>"  <?php echo ($edit) ? "readonly" : ""; ?> >
			</div>	
			<div class="form-group row" id="section_div">
			<?php if($edit==1)  //$edit defined in function file.function emgt_custom_field_edit
			{
				echo "<input name='section_id' type='hidden' value={$data['section_id']}>";
			}?>
				<label for="section_list" class="col-sm-3 text-right bold"><?php _e('Section','estate-emgt');?> :</label>
				<select id="section_select_list" name="section_id"  <?php echo ($edit==1)?'disabled':'';?> class="validate[required]">
					<option value=""><?php _e('Choose Section','estate-emgt');?></option>
					<?php 
					$sections = get_option("emgt_section_list");
					if(!empty($sections))
					{
						$sections = unserialize($sections);
						foreach($sections as $section)
						{							
							echo "<optgroup label='{$section['name']}'>";
							$lists = $db->emgt_get_rows("emgt_sections","option_id",$section['id']);
							foreach($lists as $list)
							{
								echo "<option option='{$section['id']}' value='{$list['section_id']}' '".selected($list['section_id'],$data['section_id'])."'>{$list['name']}</option>";
							}
							echo "</optgroup>";
						}
					}
				?>
				</select>
			</div>
			
			<div class="form-group row" id="req_div_box">
				<label for="req_type" class="col-sm-3 text-right bold"><?php _e('Require','estate-emgt');?> :</label>
				<input type="checkbox" name="req_type" id="req_type" value="1" class="col-sm-offset-4 text-right" <?php if($edit) echo ($data['required_field']) ?'checked':'';?>>&nbsp;<span><?php _e('(Will make field required)','estate-emgt');?></span>
			</div>
			<div class="form-group row" id="disable_div">
				<label for="dis_type" class="col-sm-3 text-right bold"><?php _e('Disable','estate-emgt');?> :</label>
				<input type="checkbox" name="dis_type" id="dis_type" value="1" class="col-sm-offset-4 text-right" <?php if($edit) echo ($data['disable']) ?'checked':'';?>>&nbsp;<span><?php _e('(Hide field)','estate-emgt');?></span>
			</div>
			<div class="form-group row">						
				<label for="field_label" class="col-sm-3 text-right bold"><?php _e('Label','estate-emgt');?> :</label>
				<input type="text" name="field_label" id="field_label" class="col-sm-4 validate[required]" placeholder="<?php _e('Enter Label','estate-emgt');?>" value="<?php if($edit) echo $data['field_label']; ?>">
			</div>				
			<div class="form-group row"> 						
				<label for="options" class="col-sm-3 text-right bold"><?php _e('Options','estate-emgt');?> :</label>
				<textarea name="options" id="options" class="col-sm-4 validate[required]" rows="3" placeholder="<?php _e('insert options','estate-emgt');?>"><?php if($edit) echo $data['options']; ?></textarea>	
				&nbsp;&nbsp;&nbsp;<label class="bg-primary form-control-label img-rounded"><?php _e('separate each option by','estate-emgt');?> '|' <br> <?php _e("example: 'option 1 | option 2'","estate-emgt"); ?> &nbsp;</label>			
			</div>
			<div class="form-group row">
				<label for="f_id" class="col-sm-3 text-right bold"><?php _e('ID Attribute','estate-emgt');?> :</label>
				<input type="text" name="f_id" id="f_id" class="col-sm-4" placeholder="<?php _e('Tag ID','estate-emgt');?>" value="<?php if($edit) echo $data['id']; ?>">		
			</div>
			<div class="form-group row">
				<label for="f_class" class="col-sm-3 text-right bold"><?php _e('Class Attribute','estate-emgt');?> :</label>
				<input type="text" name="f_class" id="f_class" class="col-sm-4" placeholder="<?php _e('Tag Class','estate-emgt');?>" value="<?php if($edit) echo $data['class']; ?>">		
			</div>	
		<hr/>
	  <div class="form-group col-sm-3 col-sm-offset-3">		
			<input type="submit" class="btn btn-primary" id="save_field" name="<?php echo ($edit == 1)?'update_field':'save_field';?>" value="<?php _e('Save','estate-emgt');?>">
	  </div>
	   </form>
	</div>