<?php
defined( 'ABSPATH' ) or die( 'Access Denied!' );
$tab = (isset($_GET['tab'])) ? $_GET['tab'] : "payment_reports";
$db = new Emgt_Db;
$plan_obj = new Emgt_PlanCheck($db);
$currency = emgt_get_currency_symbol(get_option("emgt_system_currency"));
$curr = get_option("emgt_system_currency");
$edit = 0;
require_once REMS_PLUGIN_DIR. '/lib/chart/GoogleCharts.class.php';
$GoogleCharts = new GoogleCharts;
?>

<div class="page-inner" style="min-height:1631px !important">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo get_option( 'emgt_system_name' );?></h3>
	</div>
	<div id="main-wrapper" class="class_list">
	<div class="panel panel-white">		
	<div class="panel-body">
	<h3> 
		<ul class="nav nav-tabs" id="topmenu" role="tablist">	 
		  <li class="nav-item <?php echo ($tab=='payment_reports')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_reports&tab=payment_reports" role="tab"><i class="fa fa-file-text"></i> <?php _e('Payment Report','estate-emgt');?></a>
		  </li>
		   <li class="nav-item <?php echo ($tab=='y_payment_reports')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_reports&tab=y_payment_reports" role="tab"><i class="fa fa-file-text"></i> <?php _e('Yearly Payment Report','estate-emgt');?></a>
		  </li>
		  <li class="nav-item <?php echo ($tab=='plan_reports')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_reports&tab=plan_reports" role="tab"><i class="fa fa-file-text"></i> <?php _e('Plan Report','estate-emgt');?></a>
		  </li>
		  <li class="nav-item <?php echo ($tab=='property_reports')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_reports&tab=property_reports" role="tab"><i class="fa fa-file-text"></i> <?php _e('Property Type Report','estate-emgt');?></a>
		  </li> 
		  <li class="nav-item <?php echo ($tab=='property_sold')? 'active':'';?>">
			<a class="nav-link "  href="?page=emgt_reports&tab=property_sold" role="tab"><i class="fa fa-file-text"></i> <?php _e('Sold Property Report','estate-emgt');?></a>
		  </li>
		</ul>
	</h3>
<div class="tab-content"> 
<?php
	if($tab == "payment_reports")
	{ 
		include_once "payment_report.php";
	}
	else if($tab == "y_payment_reports")
	{ 
		include_once "yearly_payment_report.php";
	}
	else if($tab == "plan_reports")
	{ 
		include_once "plan_report.php";
	}
	else if($tab == "property_reports")
	{ 
		include_once "property_report.php";
	}	
	else if($tab == "property_sold")
	{
		include_once "property_sold.php";
	}
?>	
</div>
</div>
</div>
</div>
</div>