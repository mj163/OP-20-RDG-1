<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "contract_list";
$db = new Emgt_Db;
$edit = 0;
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#task_frm").validationEngine();
	jQuery('#cases').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},	
					  {"bSortable": true},					  
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},					  
	                  {"bSortable": false}]});
});
</script>
<br><br><br>
<table id="cases" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th><?php _e("Property","estate-emgt");?></th>
			<th><?php _e("Assigned To","estate-emgt");?></th>
			<th><?php _e("Assigned Date","estate-emgt");?></th>			
			<th><?php _e("Complain","estate-emgt");?></th>	
			<th><?php _e("Complain By","estate-emgt");?></th>	
			<th><?php _e("Status","estate-emgt");?></th>				
			<th><?php _e("Action","estate-emgt");?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e("Property","estate-emgt");?></th>
			<th><?php _e("Assigned To","estate-emgt");?></th>
			<th><?php _e("Assigned Date","estate-emgt");?></th>			
			<th><?php _e("Complain","estate-emgt");?></th>
			<th><?php _e("Complain By","estate-emgt");?></th>				
			<th><?php _e("Status","estate-emgt");?></th>				
			<th><?php _e("Action","estate-emgt");?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		if(REMS_CURRENT_ROLE == "emgt_role_agent" || REMS_CURRENT_ROLE == "emgt_role_owner" )
		{
			$posts = $db->emgt_get_rows("posts","post_author",get_current_user_id());
			foreach($posts as $post)
			{
					$c_data = $db->emgt_get_rows("emgt_cases","property_id",$post['ID']);
					if(!empty($c_data))
					{
						$contracts[] = $c_data[0];
					}
			}
		}
		else{
		$contracts = $db->emgt_db_get("emgt_cases");
		}		
		if(!empty($contracts))
		{
			foreach($contracts as $ct)
			{ 
				$assigned_to = $db->emgt_get_rows("emgt_contracts","id",$ct['assigned_to']) ;
				$assigned_to = $assigned_to[0]['name'];
				
			?>
				<tr>
					<td><?php echo $ct['estate'];?></td>
					<td><?php echo $assigned_to;?></td>
					<td><?php echo $ct['assigned_date'];?></td>				
					<td><?php echo $ct['complain'];?></td>	
					<td><?php echo $ct['complain_by'];?></td>	
					<td><span class="<?php echo ($ct['status']=="Completed")?"bg-success":"bg-danger";?>">&nbsp;&nbsp;<?php echo $ct['status'];?>&nbsp;&nbsp;</span></td>	
					<td>
					<a href='?page=emgt_case&cseid=<?php echo $ct['id'];?>' class='btn btn-info radius' title="Edit"><i class='fa fa-edit'></i></a>&nbsp;
					<a href='?page=emgt_case&csdid=<?php echo $ct['id'];?>' class='btn btn-danger radius' onclick="return confirm('<?php _e("Are you sure you want to delete this record?","estate-emgt");?>');" title="Delete"><i class='fa fa-times-circle'></i></a>
					</td>
				</tr>			
	<?php	}
		}
	?>
	</tbody>
</table>
