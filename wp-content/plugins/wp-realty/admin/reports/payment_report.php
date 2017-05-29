<script>
$("document").ready(function(){
$('.date_pick').datetimepicker({
		format: 'YYYY/MM/DD'
	});
$("#frm").validationEngine();
jQuery('#example').DataTable({
		"aoColumns":[
					  {"bSortable": true,"width":"20px"},
	                  {"bSortable": true},	                 
					  {"bSortable": true},
	                  {"bSortable": true},					
					  {"bSortable": true},						
	                  {"bSortable": false}]});
});
</script>
<div class="row">
<form method="post" id="frm" name="show_report">
	<div class="form-group col-md-3">
		<label><?php _e("From Date","estate-egmt");?></label>
		<div class='input-group date date_pick'>
			<input type='text' class="form-control validate[required]" name="from_date" placeholder="select date" autocomplete="off" value="<?php echo(isset($_POST['show_report']))?$_POST['from_date']:''; ?>"/>
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
			</span>
       </div>
	</div>
	<div class="form-group col-md-3">
		<label><?php _e("To Date","estate-egmt");?></label>
		<div class='input-group date date_pick'>
			<input type='text' class="form-control validate[required]" name="to_date" placeholder="select date" autocomplete="off" value="<?php echo(isset($_POST['show_report']))?$_POST['to_date']:''; ?>" />
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
			</span>
       </div>
	</div>
	<div class="form-group col-md-1">		
		<label>&nbsp;</label>
		<input type="submit" name="show_report" value="<?php _e("Show","estate-egmt");?>" class="btn btn-primary">
	</div>
</form>
</div>
<hr>
<?php
if(isset($_POST['show_report']))
{
	$from = $_POST['from_date'];
	$to = $_POST['to_date'];
	$reports = Emgt_Payments::emgt_get_payment_report_by_date($from,$to);	
	if(!empty($reports))
	{
?>
<table id="example" class="display" cellspacing="0">
<thead>
<tr>
	<th><?php _e("Photo","estate-egmt"); ?></th>
	<th><?php _e("User","estate-egmt"); ?></th>
	<th><?php _e("Plan","estate-egmt"); ?></th>
	<th><?php _e("Paid Amount","estate-egmt"); ?></th>
	<th><?php _e("Paid Via","estate-egmt"); ?></th>
	<th><?php _e("Payment Date","estate-egmt"); ?></th>
</tr>
</thead>
<tbody>
<?php
	foreach($reports as $report)
	{
		$user = get_userdata($report->user_id);
		$username = $user->first_name ." ". $user->last_name;
		$plan = $db->emgt_get_rows("emgt_plans","id",$report->plan_id);
		$plan = $plan[0]['name'];
		echo "<tr>";
		echo "<td><img src='".get_user_meta($report->user_id,'user_photo',true)."'  height='60px' width='65px' class='img-circle'></td>";
		echo "<td>{$username}</td><td>{$plan}</td><td>{$currency} {$report->paid_amount}</td><td>{$report->paid_via}</td><td>{$report->paid_date}</td>";
		echo "</tr>";
		
	}
?>
</tbody>
</table>
<?php 
   }
   else{
	   echo "No Record Found !!";
   }
}
?>