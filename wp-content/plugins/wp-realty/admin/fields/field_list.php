<script>
	$("#page-load-ani").css('display','none');	
	$("#left-sec-menu").css('visibility','visible');
</script>
<?php 
	$section_id = $_REQUEST['s_id'];
	if(isset($_REQUEST['o_id']) && $_REQUEST['o_id'] == 4)
	{
		require_once REMS_PLUGIN_DIR ."/admin/fields/media.php"; //static page for media section page.	
	}
	else
	{
?>
<input type="hidden" id="sec_id" value="<?php echo $section_id;?>">
<table class="table table-striped table-hover field_table">
		<thead class="thead-inverse rems_title">
			<tr>
				<th><?php _e('Name','estate-emgt');?></th>
				<th><?php _e('Type','estate-emgt');?></th>
				<th colspan="3"><?php _e('Action','estate-emgt');?></th>
			</tr>
		</thead>
		<tbody id="fields_dropper">
	<?php	
	$field_order = get_option("field_order_{$section_id}");
	if(empty($field_order))
	{
		echo "<tr><td colspan='3' align='center'>No record found!!</td></tr>";
	}
	else{	
		$f_order = explode(',',$field_order);	
		$data = $db->emgt_db_get_fields_by_section_id($section_id);		
		$found_key = "";
		$new_data = array();
		
		foreach($f_order as $index=>$value)
		{
			foreach($data as $key=>$field)
			{
				if($field['field_id'] === $value)
				{
					$found_key = $key;
				}
			}			
				$new_data[$index] = $data[$found_key];			
		}	
		
		foreach($new_data as $fields)
		{	
			echo "<tr id='{$fields['field_id']}'><td>{$fields['field_label']}</td>
			<td>{$fields['field_type']}</td>
			<td><a href='#' id='{$fields['field_id']}' class='field_edit' fld_type='{$fields['field_type']}' title='Edit'><i class='fa fa-pencil-square-o'></i></a></td>
			<td>";
			if($fields['lock'] == 0)
			{
			?>
			<a href='?page=emgt_fields&s_id=<?php echo $fields['section_id'];?>&did=<?php echo $fields['field_id']; ?>' title='Delete' onclick="return confirm('Are you sure,You want to delete this field?')"><i class='fa fa-trash-o'></i></a></td>
			<?php
			}
			else{
				echo "<i class='fa fa-lock' title='Can not delete'></i>"; /* fa-ban  fa-expeditedssl*/
			}
			echo "</td><td><i class='fa fa-arrows'></i></td></tr>";
		}	 
	}
 ?>  
	 </tbody>			 
  </table>
<?php } 