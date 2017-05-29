<?php 
	if(REMS_CURRENT_ROLE != "administrator")
	{
		$data = Emgt_PlanCheck::emgt_get_user_plan_data(get_current_user_id());
		$user = get_userdata(get_current_user_id());		
	}else{
		$data = Emgt_PlanCheck::emgt_get_user_plan_data($user);
		$user = get_userdata($user);
	}	
?>
<script>
$("document").ready(function(){
	$("#frm").validationEngine();
	$(".panel-body").css({"box-shadow":"none","min-height":"auto"});	
	$(".panel-default").attr("style","border : 1px solid #dedede !important");
});
</script>
<div class="row">
	<div class="col-sm-6">
		<span><?php echo "<strong>". __("Name : ","estate-emgt")."</strong>".$user->first_name . " " .$user->last_name;?></span>
	</div>
	<div class="col-sm-6 text-right">
		<button id="view_bill" user="<?php echo $user->ID;?>" class="btn btn-primary"><?php _e("View as Bill","estate-emgt");?></button>
	</div>
</div>
<br>
<div id="accordion" class="panel-group" aria-multiselectable="true" role="tablist">
<div class="panel panel-default">
	<div id="heading_0" class="panel-heading" role="tab">
		<h4 class="panel-title">
		<a class="collapsed" aria-controls="collapse_0" aria-expanded="false" href="#collapse_0" data-parent="#accordion" data-toggle="collapse">
		<i class="fa fa-user"></i> <?php _e("User Details","estate-emgt");?> </a>

		</h4>
	</div>
	<div id="collapse_0" class="panel-collapse collapse" aria-labelledby="heading_0" role="tabpanel" aria-expanded="false" style="height: 0px;">
		<div class="panel-body">
		<table class="table table-bordered">
		<thead>
			<tr style="background-color:#818A91;color:#ffffff">
				<th><?php _e("Username","estate-emgt");?></th>
				<th><?php _e("Plan Subscribed","estate-emgt");?></th>
				<th><?php _e("Plan Activated Date","estate-emgt");?></th>
				<th><?php _e("Plan Expiry Date","estate-emgt");?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $user->first_name . " " .$user->last_name;?></td>
				<td><?php echo $data['plan'];?></td>
				<td><?php echo $data['activated_date'];?></td>
				<td><?php echo $data['expire_date'];?></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>
</div>
<div class="panel panel-default">
	<div id="heading_0" class="panel-heading" role="tab">
		<h4 class="panel-title">
		<a class="collapsed" aria-controls="collapse_1" aria-expanded="false" href="#collapse_1" data-parent="#accordion" data-toggle="collapse">
		<i class="fa fa-clipboard"></i> <?php _e("Subscribed Plan Details","estate-emgt");?> </a>
		</h4>
	</div>
	<div id="collapse_1" class="panel-collapse collapse" aria-labelledby="heading_0" role="tabpanel" aria-expanded="false" style="height: 0px;">
		<div class="panel-body">
		<table class="table table-bordered">
		<thead>
			<tr style="background-color:#818A91;color:#ffffff">
				<th><?php _e("Plan","estate-emgt");?></th>
				<th><?php _e("Plan Price","estate-emgt");?></th>
				<th><?php _e("Plan Validity","estate-emgt");?></th>
				<th><?php _e("Ads Quantity","estate-emgt");?></th>
				<th><?php _e("Paid Amount","estate-emgt");?></th>
				<th><?php _e("Due Amount","estate-emgt");?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $data['plan'];?></td>
				<td><?php echo $currency." ".$data['plan_price'];?></td>
				<td><?php echo $data['plan_validity'];?></td>
				<td><?php echo $data['ads_quantity'];?></td>
				<td><?php echo $currency." ".$data['paid'];?></td>
				<td><?php echo $currency." ".$data['remaining'];?></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>
</div>

<div class="panel panel-default">
	<div id="heading_0" class="panel-heading" role="tab">
		<h4 class="panel-title">
		<a class="collapsed" aria-controls="collapse_2" aria-expanded="false" href="#collapse_2" data-parent="#accordion" data-toggle="collapse">
		<i class="fa fa-list-alt"></i> <?php _e("Plan Usage Details","estate-emgt");?>  </a>

		</h4>
	</div>
	<div id="collapse_2" class="panel-collapse collapse" aria-labelledby="heading_0" role="tabpanel" aria-expanded="false" style="height: 0px;">
		<div class="panel-body">
		<table class="table table-bordered">
		<thead>
			<tr style="background-color:#818A91;color:#ffffff">
				<th><?php _e("Available Ads Quantity","estate-emgt");?></th>
				<th><?php _e("Used Ads","estate-emgt");?></th>
				<th><?php _e("Remaining Ads","estate-emgt");?></th>
				<th><?php _e("Single Ad Validity Period","estate-emgt");?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo $data['ads_quantity'];?></td>
				<td><?php echo (isset($data['plan_usage'][0]['used_ads']))?$data['plan_usage'][0]['used_ads']:"0";?></td>
				<td><?php echo (isset($data['plan_usage'][0]['remaining_ads']))?$data['plan_usage'][0]['remaining_ads']: $data['ads_quantity'];?></td>
				<td><?php echo (isset($data['plan_ads_validity']))?$data['plan_ads_validity']:"";?></td>
			</tr>
		</tbody>
		</table>
	</div>
</div>
</div>

<div class="panel panel-default">
	<div id="heading_0" class="panel-heading" role="tab">
		<h4 class="panel-title">
		<a class="collapsed" aria-controls="collapse_3" aria-expanded="false" href="#collapse_3" data-parent="#accordion" data-toggle="collapse">
		<i class="fa fa-bank"></i> <?php _e("Payment Details","estate-emgt");?> </a>

		</h4>
	</div>
	<div id="collapse_3" class="panel-collapse collapse" aria-labelledby="heading_0" role="tabpanel" aria-expanded="false" style="height: 0px;">
		<div class="panel-body">
		<table class="table table-bordered">
		<thead>
			<tr style="background-color:#818A91;color:#ffffff">
				<th><?php _e("Payment Date","estate-emgt");?></th>
				<th><?php _e("Paid Amount ","estate-emgt");?></th>
				<th><?php _e("Payment Method ","estate-emgt");?></th>				
			</tr>
		</thead>
		<tbody>			
			
			<?php  
			if(!empty($data['payment_history']))
			{
				foreach($data['payment_history'] as $history)
				{
					$pdate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT,$history['paid_date'] );
					echo "<tr>
					<td>{$pdate}</td>
					<td>{$currency} {$history['paid_amount']}</td>
					<td>{$history['paid_via']}</td>
					</tr>";
				} 
			}else{
				echo "<tr><td colspan='3' align='center'>No Records found!!</td></tr>";
			}
			?>
		</tbody>
		</table>
	</div>
</div>
</div>
</div>
