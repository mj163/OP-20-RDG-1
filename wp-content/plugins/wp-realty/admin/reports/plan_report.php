<?php
$plans = $db->emgt_db_get("emgt_plans");
$plan_array[] = array('Plans', 'Number of Users');
foreach($plans as $plan)
{	
	// $user[$plan['name']] = sizeof($db->emgt_get_rows("emgt_payments","plan",$plan['id']));
	// $user = sizeof($db->emgt_get_rows("emgt_payments","plan",$plan['id']));	
	$user = sizeof($plan_obj->emgt_get_user_by_plan($plan['id']));	
	// $users = $db->emgt_check_unique_field("emgt_payments")	
	$plan_array[] = array($plan['name'],(int)$user);	
}

$options = Array(
	'title' => 'Plan Subscribe Details',
	'is3D'=>true,
	'animation' => array ("startup"=> true,'duration'=> 1000, 'easing' => 'out'), 
	'hAxis' => Array(
		'title' => 'Plan Names',
		'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans')),
		// 'titleTextStyle' => Array('color' => 'red')	),
	'bar'=> array("groupWidth"=> '50%'),
	'vAxis' => array("title"=>'Total Users','minValue'=> 0,'maxValue'=> 10,'gridlines' => array('count' => 11 ))
    // 'vAxis'=> array("gridlines" => array("count" => 4))
);

$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get($plan_array, $options );

?>
	<div class="row">
		<div class="col-sm-12">
			<div id="chart_div" style="width: 900px; height: 500px;"></div>
		</div>
	</div>
		<!-- Javascript -->
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			<?php echo $chart;?>
		</script>
