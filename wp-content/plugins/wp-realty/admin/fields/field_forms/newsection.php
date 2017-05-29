<?php 
$db = new Emgt_Db;
?>
<div class="modal-header"> <a href="#" class="close-btn badge badge-success pull-right">X</a>
  <h4 id="myLargeModalLabel" class="modal-title"><?php _e("Field Configuration","estate-emgt");?></h4>
</div>
<hr>
<div class="panel panel-white">
<div class="row">
	<div class="col-sm-4 col-sm-offset-1">						
		<label for="field_name" class="text-right bold"><?php _e('Name','estate-emgt');?> :</label>
		<input type="text" name="field_name" id="field_name" placeholder="<?php _e('Section Name','estate-emgt');?>">
	</div>
	<div class="col-sm-3" id="add_new_section_div">	
	<select name="option_id" id="option" class="col-sm-10">
		<option value=""><?php _e("Group with","estate-emgt");?></option>
			<?php	
				$sections = get_option("emgt_section_list");					
				$sections = unserialize($sections);
				foreach($sections as $section)
					{ 
						if($section['id'] != 4)
						{ ?>
							<option value=<?php echo $section['id'];?>><?php echo $section['name'];?></option>
				<?php	}
					}?>
		</select>				
	</div>
	<div class="col-sm-3" id="add_new_section_div">	
		<button class="btn btn-sm btn-success" id="add_new_section"><?php _e('Add Section','estate-emgt');?></button>
	</div>
</div>
	 <div id="section_edit_box">
		<table class="table table-striped" id="section_list_table">
			<thead>
			<tr>
				<th><?php _e("Section Name","estate-emgt");?></th>
				<th><?php _e("Action","estate-emgt");?></th>
			<tr>
			</thead>
			<tbody>
				<?php 
					$sections = get_option("emgt_section_list");
					if(!empty($sections))
					{
						$sections = unserialize($sections);						
						$lists= $db->emgt_db_get("emgt_sections");
						foreach($lists as $list)
						{
							if($list['section_id'] > 6) //section 1 to 6 are fixed and should not be deleted.
							{
								echo "<tr id='{$list['section_id']}'><td>{$list['name']}</td><td>
								<a id='{$list['section_id']}' role='button' class='edit_section'><i class='fa fa-edit'>&nbsp;</i>Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a id='{$list['section_id']}' role='button' class='delete_section'><i class='fa fa-trash'>&nbsp;</i>Delete</a>
								</td></tr></option>";
							}
						}
					}
				?>
			</tbody>
		</table>
		<hr />
	  </div>	
</div>