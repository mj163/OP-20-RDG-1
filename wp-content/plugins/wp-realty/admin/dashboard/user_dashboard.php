<?php
$db = new Emgt_Db;
$user = get_current_user_id();
$tasks = $db->emgt_get_rows_multiple("emgt_tasks","assigned_to = {$user} AND status = 'In Progress'");
$user_detail = Emgt_PlanCheck::emgt_get_user_plan_data($user);
$events = array();
if(!empty($tasks))
{
	foreach($tasks as $task)
	{
		$schedule_date=$task['schedule_date'];
		$task_title = $task['task_detail'];
		// $i=1;		
		$events [] = array (
				'title' => $task_title,
				'start' => mysql2date('Y-m-d', $schedule_date ),
				'end' =>  mysql2date('Y-m-d', $schedule_date ),
				'color' => '#30A5FF'
		);	
	}
}
$total_tasks = sizeof($tasks);
$total_tasks = sprintf("%02d", $total_tasks);
?>

<script>	
	 $(document).ready(function() {	
		 $('#calendar').fullCalendar({		
			 header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: <?php echo json_encode($events);?>
		});		
	});
</script>
<?php
$db = new Emgt_Db;
require_once REMS_PLUGIN_DIR. '/lib/chart/GoogleCharts.class.php';
$GoogleCharts = new GoogleCharts;

	 $args = array(
	'posts_per_page'   => -1,					
	'orderby'          => 'date',
	'order'            => 'DESC',					
	'post_type'        => 'emgt_add_listing',					
	'post_status'      => 'publish',
	'suppress_filters' => true,
	'author' => $user
	);

$loop = new WP_QUERY($args);
$r = $s = $v = 0;
$data_array[] = array(__("Type","estate-emgt"),__("Total Property","estate-emgt"));
$id = array();
while($loop->have_posts()) : $loop->the_post(); 
	$id []= get_the_id();
endwhile;

$users = count_users();
$total_agents = $users['avail_roles']['emgt_role_agent'];
$total_owner = $users['avail_roles']['emgt_role_owner'];
if(!empty($id))
{
	$ids = implode(",",$id);
	$total_query = $db->emgt_get_rows_multiple("emgt_inquiry","property_id IN({$ids})");//$db->emgt_get_count("emgt_inquiry");
	$total_query = sizeof($total_query);
	$total_query = sprintf("%02d", $total_query);
}else{
	$total_query = "00";
}

///////////////////////////////////////////////////////////////////////////////////////

	$args = array(
	'posts_per_page'   => -1,					
	'orderby'          => 'date',
	'order'            => 'DESC',					
	'post_type'        => 'emgt_add_listing',					
	'post_status'      => 'publish',
	'suppress_filters' => true
	// 'author' => $user
	);
	$properties = get_posts($args);
	$total_properties = sizeof($properties);
	$total_properties = sprintf("%02d", $total_properties);	
?>
<style>
.page-inner{
	display:none;
}
</style>
<div class="se-pre-con"></div>
<div class="page-inner" style="min-height:1631px !important;background-color:#F1F4F7">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /> <?php echo get_option( 'emgt_system_name' );?></h3>
	</div>	
		<div class="row">
			<div class="col-sm-2 col-md-2 text-center con-box info-box">
				<h4 style="color:#30A5FF"><?php _e("Total Properties","estate-emgt");?></h4>				
				<div class="piecircle">
					<canvas id="myCanvas1" height="130" width="130"></canvas>
					<span class="count" style="color:#30A5FF"><?php echo $total_properties;?></span>
				</div>				
			</div>	
			<div class="col-sm-2 col-md-2 text-center info-box con-box">
				<h4 style="color:#F9243F"><?php _e("Total Inquiries","estate-emgt");?></h4>
				<div class="piecircle">
					<canvas id="myCanvas4" height="130" width="130"></canvas>
					<span class="count" style="color:#F9243F"><?php echo $total_query;?></span>
				</div>
			</div>
			<div class="col-sm-2 col-md-2 text-center info-box con-box">
				<h4 style="color:#B2C831"><?php _e("Total Tasks","estate-emgt");?></h4>
				<div class="piecircle">
					<canvas id="myCanvas5" height="130" width="130"></canvas>
					<span class="count" style="color:#B2C831"><?php echo $total_tasks;?></span>
				</div>
			</div>
			<div class="col-sm-4 col-md-4 text-center info-box con-box fade_e" style="height:201px">
				<h4 style="color:#2CC3B3"><?php _e("Plan Details","estate-emgt");?></h4>
				<div class="col-sm-12 col-md-12 text-left">
					<br>
					<p><span><strong><?php _e("Plan Activation Date","estate-emgt"); ?> : </strong> <?php echo (!empty($user_detail['activated_date']))? $user_detail['activated_date'] : " Not Subscribed"; ?> </span></p>
					<p><span><strong><?php _e("Plan Expire Date","estate-emgt"); ?> : </strong> <?php echo (!empty($user_detail['expire_date']))?$user_detail['expire_date']:"Not Subscribed"; ?> </span></p>
					<p><span><strong><?php _e("Total Ads Used","estate-emgt"); ?> : </strong> <?php echo (isset($user_detail['plan_usage'][0]['used_ads']))?$user_detail['plan_usage'][0]['used_ads']:"Not Subscribed"; ?></span></p>
					<p><span><strong><?php _e("Total Ads Remaining","estate-emgt"); ?> : </strong> <?php echo (isset($user_detail['plan_usage'][0]['remaining_ads']))?$user_detail['plan_usage'][0]['remaining_ads']:"Not Subscribed"; ?></span></p>					
				</div>
			</div>
			<script>
				$(".fade_e").hide();	
				$(".fade_e").fadeIn(1000);
			</script>
		</div>
		
		<div class="row">
			<div class="col-sm-7 col-md-7 con-box">
				<div id="calendar"></div>
			</div>
			<div class="col-sm-4 col-md-4">
				<div class="panel panel-default righ-box-dash">				 
				  <div class="panel-heading"><?php _e("Recently Added Properties","estate-emgt");?></div>
				  <?php 
					 $args = array(
					'posts_per_page'   => 5,					
					'orderby'          => 'date',
					'order'            => 'DESC',					
					'post_type'        => 'emgt_add_listing',					
					'post_status'      => 'publish',
					'suppress_filters' => true
					// 'author' => $user
				);
					$properties = get_posts($args);					
				?>
				  <table class="table">
					<thead>
						<tr>
							<th>#</th><th><?php _e("Property Name","estate-emgt");?></th><th><?php _e("Posted By","estate-emgt");?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$i=1;
					foreach($properties as $property)
					{
						$date = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $property->post_date);
						$user = get_userdata($property->post_author);
						// $name = $user->first_name ." ".$user->last_name;
						$name = $user->display_name;
						echo "<tr>";
						echo "<td><strong>{$i}</strong></td>";					
						echo "<td>{$property->post_name}</td>";
						echo "<td>{$name}</td>";						
						echo "</tr>";
						$i++;
											
					}  ?>					
					</tbody>
				  </table>			
			</div>
			
				<div class="panel panel-default righ-box-dash">				 
				  <div class="panel-heading"><?php _e("Recently Sold Properties","estate-emgt");?></div>
				  <?php
					$sold_properties = $db->emgt_db_get("emgt_sold_properties");					
					$sold_properties = array_reverse($sold_properties);										
				?>
				  <table class="table">
					<thead>
						<tr>
							<th>#</th><th><?php _e("Property Name","estate-emgt");?></th><th><?php _e("Sold Date","estate-emgt");?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					if(!empty($sold_properties))
					{
						$i=1;
						foreach($sold_properties as $property)
						{
							if($i <= 5)
							{
								$sdate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT, $property["sold_date"]);
								$user = get_userdata($property->post_author);
								$pname = get_the_title($property['property_id']);
								echo "<tr>";
								echo "<td><strong>{$i}</strong></td>";					
								echo "<td>{$pname}</td>";
								echo "<td>{$sdate}</td>";						
								echo "</tr>";
							}
							$i++;
												
						}
					}
					else{
						echo "<tr>";
						echo "<td colspan='2'>".__("No Data Available","estate-emgt")."</td>";
						echo "</tr>";
					}?>					
					</tbody>
				  </table>			
			</div>
			
			
			</div>
		</div>		
	
</div>
<script>
(function() {
  var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
                              window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
  window.requestAnimationFrame = requestAnimationFrame;
})();
 var radius = 50;
 var endPercent = 101;//85;
 var curPerc = 0;
 var counterClockwise = false;
 var circ = Math.PI * 2;
 var quart = Math.PI / 2;
var c=document.getElementById("myCanvas1");
var ctx1=c.getContext("2d");
ctx1.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx1.strokeStyle = '#30A5FF';

</script>

<script>
var c=document.getElementById("myCanvas4");
var ctx4=c.getContext("2d");
ctx4.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx4.strokeStyle = '#F9243F';//'#ad2323';  #1DB198

</script>
<script>
var c=document.getElementById("myCanvas5");
var ctx5=c.getContext("2d");
ctx5.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx5.strokeStyle = '#B2C831';//'#ad2323';  #1DB198

</script>
<script>
 function animate(current) {
	 
	 ctx1.clearRect(0, 0, c.width, c.height);
	 ctx1.beginPath();
	 ctx1.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
	 ctx1.stroke();
 
	 ctx4.clearRect(0, 0, c.width, c.height);
	 ctx4.beginPath();
	 ctx4.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
	 ctx4.stroke();
	 
	 ctx5.clearRect(0, 0, c.width, c.height);
	 ctx5.beginPath();
	 ctx5.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
	 ctx5.stroke();
	 
	 curPerc++;
	 if (curPerc < endPercent) {
		 requestAnimationFrame(function () {
			 animate(curPerc / 100)
		 });
	 }	 
 }
 animate();
</script>
<script>
//paste this code under the head tag or in a separate js file.
	// Wait for window load
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut(-200);
		$(".page-inner").css("display","block");
		  $('#calendar').fullCalendar('render');
	});
</script>