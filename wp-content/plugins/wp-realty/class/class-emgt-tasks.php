<?php 
class Emgt_Tasks
{
	public static function emgt_get_task_by_inquiry($id = null)
	{
		if($id != null && is_numeric($id))
		{
			global $wpdb;
			$t1 = $wpdb->prefix . "emgt_tasks";
			$t2 = $wpdb->prefix . "emgt_inquiry";
			$sql = "SELECT a.id,a.assigned_to,a.inquiry_id,a.task_detail,a.schedule_date,a.status,a.created_date,
					b.property_id,b.estate FROM {$t1} a
					INNER JOIN {$t2} b
					ON a.inquiry_id = b.id
					and b.id = {$id}";
			$data = $wpdb->get_results($sql,ARRAY_A);			
			return $data;
		}
		else{
			return false;
		}
	}

	public static function emgt_get_tasks()
	{		
			global $wpdb;
			$t1 = $wpdb->prefix . "emgt_tasks";
			$t2 = $wpdb->prefix . "emgt_inquiry";
			$sql = "SELECT a.id,a.assigned_to,a.inquiry_id,a.task_detail,a.schedule_date,a.status,a.created_date,
					b.property_id,b.estate FROM {$t1} a
					INNER JOIN {$t2} b
					ON a.inquiry_id = b.id";
			$data = $wpdb->get_results($sql,ARRAY_A);			
			return $data;		
	}	
	
	public static function emgt_get_tasks_by_user($id = null)
	{		
		if($id != null  && is_numeric($id))
		{
			global $wpdb;
			$t1 = $wpdb->prefix . "emgt_tasks";
			$t2 = $wpdb->prefix . "emgt_inquiry";
			$sql = "SELECT a.id,a.assigned_to,a.inquiry_id,a.task_detail,a.schedule_date,a.status,a.created_date,
					b.property_id,b.estate FROM {$t1} a
					INNER JOIN {$t2} b
					ON a.inquiry_id = b.id and a.assigned_to = {$id}";
			$data = $wpdb->get_results($sql,ARRAY_A);			
			return $data;
		}
	}

	public static function emgt_get_user_by_property($id = null)
	{
		if($id!=null  && is_numeric($id))
		{
			$property = $id;
			global $wpdb;
			$t1 = $wpdb->prefix . "posts";
			$users = $wpdb->get_row("SELECT * FROM {$t1} WHERE ID = {$id}",ARRAY_A);				
		}
		return $users;
	}	
}