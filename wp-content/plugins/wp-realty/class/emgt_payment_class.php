<?php 
defined( 'ABSPATH' ) or die( 'Access Denied!' );
class Emgt_Payments{
	
	public static function emgt_get_payment_report_by_date($from,$to)
	{
		global $wpdb;
		$tbl = $wpdb->prefix."emgt_payments_history";
		$tbl2 = $wpdb->prefix."users";
		
		$data = $wpdb->get_results("SELECT * FROM {$tbl},{$tbl2} WHERE paid_date >= '{$from}' AND paid_date <= '{$to}' AND {$tbl}.user_id = {$tbl2}.ID ");
		// $data = $wpdb->get_results("SELECT * FROM {$tbl} WHERE paid_date BETWEEN '{$from}' AND '{$to}'");
		return $data;
	}	
}