	<script>
		$("#page-load-ani").css('display','none');	
		$("#left-sec-menu").css('visibility','visible');
	</script>
<?php 
	$section_id = $_REQUEST['s_id'];
?>
<input type="hidden" id="sec_id" value="<?php echo $section_id;?>">
<table class="table table-striped table-hover field_table">
	<thead class="thead-inverse rems_title">
		<tr>
			<th><?php _e('Field','estate-emgt');?></th>
			<th></th>
			<th colspan="3"><?php _e('Action','estate-emgt');?></th>
		</tr>
	</thead>
	<tbody>	
	<?php
	if($section_id == 4)
	{ 
		$g_check = get_option("gallery_field_show");	
	?>	
		<tr>
			<td><?php _e("Display Gallery field to add new listing page ?","estate-emgt");?></td>
			<td><input type="checkbox" class="gallery" <?php echo ($g_check) ? "checked":"";?>>&nbsp;&nbsp;&nbsp;<?php _e("Show","estate-emgt");?></td>
			<td><a href="javascript:void(0)" id="gallery" class="btn btn-xs btn-primary btn_fld_show"><?php _e("Save","estate-emgt"); ?></a></td>
		</tr>
	<?php
	}?>
	<?php
	if($section_id == 5)
	{ 
		$v_check = get_option("video_field_show");
		$f_check = get_option("floor_plan_field_show");
	?>	
		<tr>
			<td><?php _e("Display Video Attach URL field to add new listing page ?","estate-emgt");?></td>
			<td><input type="checkbox" class="video" <?php echo ($v_check) ? "checked":"";?>>&nbsp;&nbsp;&nbsp;<?php _e("Show","estate-emgt");?></td>
			<td><a href="javascript:void(0)" id="video" class="btn btn-xs btn-primary btn_fld_show"><?php _e("Save","estate-emgt"); ?></a></td>
		</tr>
		<tr>
			<td><?php _e("Display Floor plan attach field to add new listing page ?","estate-emgt");?></td>
			<td><input type="checkbox" class="floor_plan" <?php echo ($f_check) ? "checked":"";?>>&nbsp;&nbsp;&nbsp;<?php _e("Show","estate-emgt");?></td>
			<td><a href="javascript:void(0)" id="floor_plan" class="btn btn-xs btn-primary btn_fld_show"><?php _e("Save","estate-emgt"); ?></a></td>
		</tr>
	<?php
	}?>
	</tbody>			 
 </table>