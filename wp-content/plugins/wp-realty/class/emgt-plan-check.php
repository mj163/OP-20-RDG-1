<?php 
defined('ABSPATH') or die( 'Access Denied!' );

class Emgt_PlanCheck{
	private $db;
	
	public function __construct(Emgt_Db $db)
	{
		$this->db = $db;
	}
	
	public function emgt_plan_feature_check($feature = null,$plan=null)
	{
		global $wpdb;
		$tbl = $wpdb->prefix . "emgt_plans";
		$data = $wpdb->get_results("SELECT * FROM {$tbl} WHERE id = '{$plan}'",ARRAY_A);
		// $plan = $this->db->emgt_get_rows("emgt_plans","id","1");
		$plan = $data;
		$features = $plan[0]['features'];
		$p_features = explode(",",$features);
		$chk = (in_array($feature,$p_features))? 1:0;
		return $chk;
	}
	
	public static function emgt_plan_status_check($user,$plan)
	{
		global $wpdb;
		$tbl = $wpdb->prefix . "emgt_payments";
		$data = $wpdb->get_row("SELECT * FROM {$tbl} WHERE user_id = {$user} AND plan = '{$plan}'");
		return $data->status;
	}
	
	public static function emgt_get_user_plan_data($id = null)
	{ 
		// also fetch history
		global $wpdb;
		$res = null ;
		$tbl = $wpdb->prefix . "emgt_payments";
		if(is_numeric($id))
		{
			$res = $wpdb->get_row("SELECT * FROM {$tbl} WHERE user_id = {$id}",ARRAY_A);
		}
		if($res != null)
		{
			$data['paid'] = $res['paid'];
			$data['remaining'] = $res['remaining'];
			$data['status'] = $res['status'];
			$data['payment_status'] = $res['payment_status'];
			$adate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT,$res['activated_date']);
			$data['activated_date'] = $adate;
			$edate = mysql2date( WP_DATEFORMAT ." ". WP_TIMEFORMAT,$res['expire_date']);
			$data['expire_date'] = $edate;
			
			$tbl = $wpdb->prefix . "emgt_plans";
			$res1 = $wpdb->get_row("SELECT * FROM {$tbl} WHERE id = {$res['plan']}",ARRAY_A);		
			$data['plan'] = $res1['name'];
			$data['plan_price'] = $res1['price'];
			$data['ads_quantity'] = $res1['quantity'];
			$data['plan_validity'] = $res1['plan_validity'] ." ".$res1['plan_period'];
			$data['plan_ads_validity'] = $res1['ads_validity'] ." ".$res1['ads_period'];
			
			// $tbl = $wpdb->prefix . "posts";
			// $res2 = $wpdb->get_row("SELECT * FROM {$tbl} WHERE post_author = {$id}",ARRAY_A);
			// $data['posted_ads_id'] = $res2;		
			
			$tbl = $wpdb->prefix . "emgt_plan_usage";
			$res3 = $wpdb->get_results("SELECT * FROM {$tbl} WHERE user_id = {$id} AND plan = {$res['plan']}",ARRAY_A);
			$data['plan_usage'] = $res3;		
			
			$tbl = $wpdb->prefix . "emgt_ads";
			$res4 = $wpdb->get_results("SELECT * FROM {$tbl} WHERE user_id = {$id}",ARRAY_A);
			$data['posted_ads_id'] = $res4;
			
			$tbl = $wpdb->prefix . "emgt_payments_history";
			$res5 = $wpdb->get_results("SELECT * FROM {$tbl} WHERE user_id = {$id} AND plan_id = {$res['plan']}",ARRAY_A);
			$data['payment_history'] = $res5;
			
			return $data;
		}
	}
	
	public function emgt_get_user_by_plan($pid)
	{
		global $wpdb;
		$pid = intval($pid);
		$t1 = $wpdb->prefix . "emgt_payments";
		$t2 = $wpdb->prefix . "users";
		$users = $wpdb->get_results("SELECT {$t1}.*,{$t2}.ID FROM {$t1},{$t2} WHERE {$t1}.user_id = {$t2}.ID AND {$t1}.plan = {$pid}",ARRAY_A);
		return $users;
	}
}