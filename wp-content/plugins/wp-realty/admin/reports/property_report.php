<?php

$args = array( 'post_type' => 'emgt_add_listing', "posts_per_page" => -1);
$loop = new WP_QUERY($args);
$r = $s = $v = 0;
$data_array[] = array("Type","Total Property");
while($loop->have_posts()) : $loop->the_post(); 

	$id = get_the_id();
	$status = get_post_meta($id,"1_emgtfld_for",true);	
	$status = (!empty($status)) ? $status : "null";
	
	switch($status)
	{
		CASE "Rent":
			$r++;
			break;
			
		CASE "Sale":			
			$s++;
			break;
			
		CASE "Vacational Rent":			
			$v++;
			break;	
	}
endwhile;

$data_array[] = array("Rent",$r);
$data_array[] = array("Sale",$s);
$data_array[] = array("Vacational Rent",$v);

$options = Array(
	'title' => __('Property By Type','estate-emgt'),
	'is3D'=>true,	
	'hAxis' => Array(
		'title' => __('Months','estate-emgt'),
		'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
		'textStyle' => Array('color' => '#222','fontSize' => 10)),
	'bar'=> array("groupWidth"=> '50%'),
	'vAxis' => array("title"=>__('Amount Paid','estate-emgt'),'minValue'=> 0,'maxValue'=> 10,'gridlines' => array('count' => 11 ),
					'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
					'format' => '#',
					'textStyle' => Array('color' => '#222','fontSize' => 12)),
	'colors' => array('#22BAA0','#f25656','#990099')
    // 'vAxis'=> array("gridlines" => array("count" => 4))
);

$chart = $GoogleCharts->load( 'pie' , 'chart_div' )->get($data_array,$options);
?>
<div class="row">
		<div class="col-sm-12">
			<div id="chart_div" style="width: 100%; height: 500px;"></div>
		</div>
</div>	
<!-- Javascript -->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	<?php echo $chart;?>
</script>