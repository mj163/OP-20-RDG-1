<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "plan_list";
$db = new Emgt_Db;
$currency = emgt_get_currency_symbol(get_option("emgt_system_currency"));
$edit = 0;
?>
<?php
if(isset($_POST['save_plan']))
{	
	$ftr = "";
	if(isset($_POST["feature"]))
	{
		$features = $_POST["feature"];	
		foreach($features as $feature)
		{			
			$ftr .= $feature.",";
		}	
		$ftr = trim($ftr,",");
	}else{ $ftr = "";}
	
	if($_POST['ads_validity'] == "")
	{
		$_POST['ads_validity'] = $_POST['plan_validity'];
	}
	
	
	$data = array(
		  'name' => $_POST['name'],
		  'price' => $_POST['price'],
		  'quantity' => $_POST['quantity'],
		  'features' => $ftr,
		  'plan_validity' => $_POST['plan_validity'],
		  'plan_period' => $_POST['plan_period'],
		  'ads_validity' => $_POST['ads_validity'],
		  'ads_period' => $_POST['ads_period']		  		  
	);		
				
	if($_POST['save_plan'] == "Add Plan")
	{
		$data["created_date"] = date('Y-m-d H:i:s')	;
		$chk = $db->emgt_insert("emgt_plans",$data);
		($chk) ? $success = 1 : $success = 0;
		$tab = "plan_list";	
	}	
	
	if($_POST['save_plan'] == "Update Plan")
	{
		$uid = $_POST['uid'];
		$chk = $db->emgt_db_update("emgt_plans",$data,array("id" => $uid));
		($chk) ? $success = 1 : $success = 0;
		$tab = "plan_list";	
	}
}

if(isset($_GET['eid']))
{
	$eid = $_GET['eid'];
	$edit = 1;
	$tab = "add_plan";	
}
if(isset($_GET['did']))
{
	$did = $_GET['did'];
	$chk = $db->emgt_delete_record("emgt_plans","id",$did);
	($chk) ? $success = 1 : $success = 0;
}
?>
<script type="text/javascript">
$(document).ready(function() {
	jQuery('#example').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},	
					  {"bSortable": true},											 
	                  {"bSortable": false}]});
} );
</script>   
<div class="page-inner" style="min-height:1631px !important">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo get_option( 'emgt_system_name' );?></h3>
	</div>
	<div id="main-wrapper" class="class_list">
	<?php if(isset($success)) : 
		switch($success)
		{
			CASE 1: ?>
					<div class="alert alert-success alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-check-circle"></i>&nbsp;<?php _e('Success !','estate-emgt');?></strong> <?php _e('Operation completed successfully!','estate-emgt');?>
				</div>	
			<?php
			break;			
			CASE 2:?>
					<div class="alert alert-danger alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-warning"></i>&nbsp;<?php _e('Error !','estate-emgt');?></strong> <?php _e('Operation failed. Pleas try again later!','estate-emgt');?>
					</div>		
				</div>					
			<?php	
			break;
		}
		endif;
	?>	
	<div class="panel panel-white">		
	<div class="panel-body">
	<h3> 
		<ul class="nav nav-tabs" id="topmenu" role="tablist">	 
		  <li class="nav-item <?php echo ($tab=='plan_list')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_plan&tab=plan_list" role="tab"><i class="fa fa-archive"></i> <?php _e('Plans','estate-emgt');?></a>
		  </li>	
		  <li class="nav-item  <?php echo ($tab=='add_plan')? 'active':'';?>">
			<a class="nav-link" href="?page=emgt_plan&tab=add_plan" role="tab"><i class="fa fa-plus"></i> <?php ($edit) ? _e('Edit Plan','estate-emgt') : _e('Add Plan','estate-emgt');?></a>
		  </li>
		</ul>
	</h3>
<div class="tab-content"> 
<?php 
	if($tab == "plan_list")
	{?>
		 <br>
			 <br>
					<table id="example" class="display" cellspacing="0" width="100%">
						<thead>
							<th><?php _e("Plan Name","estate-emgt");?></th>
							<th><?php _e("Price","estate-emgt");?></th>
							<th><?php _e("Created Date","estate-emgt");?></th>
							<th><?php _e("Action","estate-emgt");?></th>							
						</thead>
						<tfoot>							
							<th><?php _e("Plan Name","estate-emgt");?></th>
							<th><?php _e("Price","estate-emgt");?></th>
							<th><?php _e("Created Date","estate-emgt");?></th>
							<th><?php _e("Action","estate-emgt");?></th>
						</tfoot>
						<tbody>
	<?php
	$plans = $db->emgt_db_get("emgt_plans");	
	foreach($plans as $plan)
	{
		$date = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $plan['created_date']);		
		echo "<tr><td>{$plan['name']}</td>";
		echo "<td>{$currency} {$plan['price']}</td>";
		echo "<td>{$date}</td>";
		echo "<td>
		<a href='?page=emgt_plan&eid=".$plan['id']."' class='btn btn-info radius' title='Edit'><i class='fa fa-edit '></i></a>&nbsp;
		<a href='?page=emgt_plan&did=".$plan['id']."'' class='btn btn-danger radius' title='Delete' onclick=\"return confirm('Are you sure you want to delete record ?')\"><i class='fa fa-remove'></i></a>
		</td></tr>";
	}
	?>
						</tbody>
					</table>
	<?php } 
	if($tab == "add_plan")
	{		
	   include_once __DIR__ . "\add_plan.php";
	} ?>

</div>
	
	
</div>
</div>
</div>
</div>