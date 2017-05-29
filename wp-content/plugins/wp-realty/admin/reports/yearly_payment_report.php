<?php 
if(isset($_POST['show_report']))
{
	$selected_year = $_POST['year'];
}else{
	$selected_year = 0;
}
?>

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
		<label><?php _e("Select Year","estate-egmt");?></label>
		<select name="year" class="form-control validate[required]">
			<option value=""><?php _e("Select Year","estate-egmt");?></option>
			<?php 
			$c_date = date("Y");
			for($i=11;$i>=1;$i--)
			{
				echo "<option value={$c_date} ".selected($c_date,$selected_year).">{$c_date}</option>";
				$c_date --;
			}
			?>
		</select>
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
	$year = $_POST['year'];	
	$from = $year."/01/01";
	$to = $year."/12/31";
	$month_array[] = array(__("Months","estate-egmt") , __("Total Payments in","estate-egmt")." {$curr}");
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
	'title' => __("Monthly Payments","estate-egmt"),
	// 'is3D'=>true,
	'animation' => array ("startup"=> true,'duration'=> 1000, 'easing' => 'out'), 
	'hAxis' => Array(
		'title' => 'Months',
		'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false)),
		// 'titleTextStyle' => Array('color' => 'red','fontName' =>'open sans')	),
	'bar'=> array("groupWidth"=> '50%'),
'vAxis' => array("title"=>__("Amount Paid in","estate-egmt")." {$curr}",'minValue'=> 0,'maxValue'=> 10,'gridlines' => array('count' => 11 ))
    // 'vAxis'=> array("gridlines" => array("count" => 4))
);
	if(!empty($reports))
	{
		
	$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $month_array , $options );
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
<?php 
   }
   else{
	   echo "No Record Found !!";
   }
}
?>