<script>
$("document").ready(function(){
$('.date_pick').datetimepicker({
		format: 'YYYY/MM/DD'
	});
$("#frm").validationEngine();
jQuery('#example').DataTable({
		"aoColumns":[
	                  {"bSortable": true},					
	                  {"bSortable": true},					
	                  {"bSortable": false},					
					  {"bSortable": false},						
	                  {"bSortable": true}]});
});
</script>

<hr>
<?php 
$db = new Emgt_Db;
$properties =  $db->emgt_db_get("emgt_sold_properties");
 // var_dump($properties);
if(!empty($properties))
	{

?>
<table id="example" class="display" cellspacing="0">
<thead>
<tr>
	<th><?php _e("#","estate-egmt"); ?></th>
	<th><?php _e("Property ID","estate-egmt"); ?></th>
	<th><?php _e("Property Name","estate-egmt"); ?></th>
	<th><?php _e("Author Name","estate-egmt"); ?></th>
	<th><?php _e("Sold Date","estate-egmt"); ?></th>	
</tr>
</thead>
<tbody>
<?php
	$i = 1;
	 foreach($properties as $property)
	{
		$sdate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $property['sold_date']);
		$post_author = get_post_field( 'post_author', $property['property_id'] );
		$pname = get_the_title($property['property_id']);
		$user = get_userdata($post_author);
		$user = $user->first_name ."  ". $user->last_name;		
		echo "<tr><td>{$i}</td>";
		echo "<td>{$property['property_id']}</td>";
		echo "<td>{$pname}</td>
				<td>{$user}</td>		
				<td>{$sdate}</td>	
				</tr>";
		$i++;
	}
?>
</tbody>
</table>
<?php 
   }
   else{
	   echo "No Records Found !!";
   }
// }
?>