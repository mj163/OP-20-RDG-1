<?php
$db = new Emgt_Db;
$tasks = $db->emgt_get_rows_multiple("emgt_tasks","status = 'In Progress'");
$events = array();
foreach($tasks as $task)
{
	$schedule_date=$task['schedule_date'];
	$task_title = $task['task_detail'];
	// $i=1;		
	$events [] = array (
			'title' => $task_title,
			'start' => mysql2date('Y-m-d', $schedule_date ),
			'end' =>  mysql2date('Y-m-d', $schedule_date ),//date('Y-m-d',strtotime($schedule_date.' +'.$i.' days')),
			'color' => '#30A5FF'
	);	
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

$r = $s = $v = 0;
$data_array[] = array("Type","Total Property");

$users = count_users();
$total_agents = $users['avail_roles']['emgt_role_agent'];
$total_owner = $users['avail_roles']['emgt_role_owner'];
$total_query = $db->emgt_get_count("emgt_inquiry");
$total_agents = sprintf("%02d", $total_agents);
$total_owner = sprintf("%02d", $total_owner);
$total_query = sprintf("%02d", $total_query);
///////////////////////////////////////////////////////////////////////////////////////
$year = date("Y");	
$from = $year."/01/01";
$to = $year."/12/31";
$month_array[] = array(__("Months","estate-emgt") , __("Total Payments","estate-emgt"));
$reports = Emgt_Payments::emgt_get_payment_report_by_date($from,$to);	
$start = $month = strtotime($from);	
$end = strtotime($to);
while($month < $end)
{		
	$paid = 0;
	foreach($reports as $report)
	{
		$cm = date("m",$month);
		$m = date_parse_from_format("Y-m-d", $report->paid_date);
		$m = $m['month'];
		if($cm == $m)
		{
			$paid += $report->paid_amount;
		}
	}
	 $month_array[] = array(date('M y', $month),$paid);
	 $month = strtotime("+1 month", $month);
}

$options = Array(
	'title' => __("Current Year Payments","estate-emgt"),	
	'prefix' => '$',
	'animation' => array ("startup"=> true,'duration'=> 2000, 'easing' => 'inAndOut'), 
	'hAxis' => Array(
		'title' => __("Months","estate-emgt"),
		'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false)),		
	'bar'=> array("groupWidth"=> '50%'),
	'vAxis' => array("title"=>__("Amount Paid in","estate-emgt").'-'.get_option("emgt_system_currency").'','minValue'=> 0,'maxValue'=> 10,'gridlines' => array('count' => 11 ))
);

$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $month_array , $options );
//////////////////////////////////////////////////////

	 $args = array(
	'posts_per_page'   => -1,					
	'orderby'          => 'date',
	'order'            => 'DESC',					
	'post_type'        => 'emgt_add_listing',					
	'post_status'      => 'publish',
	'suppress_filters' => true 
	);
	$properties = get_posts($args);
	$total_properties = sprintf("%02d",sizeof($properties));
	
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
				<h4 style="color:#FFB53E"><?php _e("Total Owners","estate-emgt");?></h4>
				<div class="piecircle">
					<canvas id="myCanvas2" height="130" width="130"></canvas>
					<span class="count" style="color:#FFB53E"><?php echo $total_owner;?></span>
				</div>				
			</div>
			<div class="col-sm-2 col-md-2 text-center info-box con-box">
				<h4 style="color:#2CC3B3"><?php _e("Total Agents","estate-emgt");?></h4>
				<div class="piecircle">
					<canvas id="myCanvas3" height="130" width="130"></canvas>
					<span class="count" style="color:#2CC3B3"><?php echo $total_agents;?></span>
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
					<canvas id="myCanvas5" height="130" width="130">123</canvas>
					<span class="count" style="color:#B2C831"><?php echo $total_tasks;?></span>
				</div>
			</div>
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
					}
					?>	
					
					</tbody>
				  </table>			
			</div>
			
			
			</div>
		</div>
		<hr>			
		<div class="row">			
			<div class="col-sm-11 col-md-11 con-box">
				<div id="chart_div" style="height: 500px;">
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
						<?php echo $chart;?>
					</script>
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
 var counterClockwise = true;
 var circ = Math.PI * 2;
 var quart = Math.PI / 2;
var c=document.getElementById("myCanvas1");
var ctx1=c.getContext("2d");
ctx1.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx1.strokeStyle = '#30A5FF';
// ctx1.beginPath();
// ctx1.arc(x,y,55,0,2*Math.PI);
// ctx1.stroke();
</script>
<script>
var c=document.getElementById("myCanvas2");
var ctx2=c.getContext("2d");
ctx2.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx2.strokeStyle = '#FFB53E';
// ctx2.beginPath();
// ctx2.arc(x,y,55,0,2*Math.PI);
// ctx2.stroke();
</script>
<script>
var c=document.getElementById("myCanvas3");
var ctx3=c.getContext("2d");
ctx3.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx3.strokeStyle = '#2CC3B3';//'#ad2323';  #1DB198 #6a5fac #30A5FF #FFB53E
// ctx3.beginPath();
// ctx3.arc(x,y,55,0,2*Math.PI);
// ctx3.stroke();
</script>
<script>
var c=document.getElementById("myCanvas4");
var ctx4=c.getContext("2d");
ctx4.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx4.strokeStyle = '#F9243F';//'#ad2323';  #1DB198
// ctx4.beginPath();
// ctx4.arc(x,y,55,0,2*Math.PI);
// ctx4.stroke();
</script>
<script>
var c=document.getElementById("myCanvas5");
var ctx5=c.getContext("2d");
ctx5.lineWidth = 20;
var x = c.width / 2;
var y = c.height / 2;
ctx5.strokeStyle = '#B2C831';//'#ad2323';  #1DB198
// ctx5.beginPath();
// ctx5.arc(x,y,55,0,2*Math.PI);
// ctx5.stroke();
</script>
<script>
 function animate(current) {
	 
	 ctx1.clearRect(0, 0, c.width, c.height);
	 ctx1.beginPath();
	 ctx1.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
	 ctx1.stroke();
	 
	 ctx2.clearRect(0, 0, c.width, c.height);
	 ctx2.beginPath();
	 ctx2.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
	 ctx2.stroke();
	 
	 ctx3.clearRect(0, 0, c.width, c.height);
	 ctx3.beginPath();
	 ctx3.arc(x, y, radius, -(quart), ((circ) * current) - quart, false);
	 ctx3.stroke();
	 
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