<?php 
defined( 'ABSPATH' ) or die( 'Access Denied!' );
class Emgt_Db{
	
	public static $wpdb = NULL;
	public $insert_id;
	public static $prefix;
	
	public function __construct()
	{
		if(!isset(self::$wpdb))
		{	
			global $wpdb;
			self::$wpdb = &$wpdb;
			self::$prefix = self::$wpdb->prefix;
		}		
	}
	
	public function emgt_insert($table_name=null,$data=null)
	{		
		if($data == null || empty($table_name))
		{
			return false;
		}else
		{
			$table_name = self::$prefix . $table_name;
			$chk = self::$wpdb->insert($table_name,$data);	
			$this->insert_id = self::$wpdb->insert_id;
		}	
		return $this->insert_id ; 
	}

	public function emgt_db_get($tbl)
	{
		$table_name = self::$prefix . $tbl;
		$data = self::$wpdb->get_results("SELECT * FROM {$table_name}",ARRAY_A);		
		return $data;
	}
	
	public function emgt_db_get_join($tbl,$j_tbl,$t1_fld,$t2_fld)
	{
		$t1 = self::$prefix . $tbl;
		$t2 = self::$prefix . $j_tbl;
		$data = self::$wpdb->get_results("SELECT * FROM {$t1},{$t2} WHERE {$t1}.{$t1_fld} = {$t2}.{$t2_fld}",ARRAY_A);		
		return $data;
	}
	
	public function emgt_delete_record($tbl,$field = null,$value = null)
	{
		if($field === null || $value === null)
		{
			return false;
		}else{
			$table_name = self::$prefix . $tbl;
			$chk = self::$wpdb->delete($table_name,array($field => $value));
		}
		if($chk)
		{			
			return true;
		}
		return false;
		
	}
	
	public function emgt_get_rows($tbl,$field = null,$id = null)
	{
		if($id === null || $field === null)
		{
			return false;
		}else{
			$table_name = self::$prefix . $tbl;
			$data = self::$wpdb->get_results("SELECT * FROM {$table_name} WHERE {$field} = {$id}",ARRAY_A);	
			return $data;
		}		
	}

	public function emgt_get_rows_multiple($tbl,$where)
	{
		if($tbl === null || $where === null)
		{
			return false;
		}else{
			$table_name = self::$prefix . $tbl;
			$data = self::$wpdb->get_results("SELECT * FROM {$table_name} WHERE {$where}",ARRAY_A);	
			return $data;
		}		
	}
	
	public function emgt_db_update($tbl,$data=null,$id=array())
	{
		$table_name = self::$prefix.$tbl;
		$sql_vals = null;
		if($data == null)
		{
			return false;
		}else{			
			self::$wpdb->update($table_name,$data,$id);
			return true;
		}
	}
	
	public function emgt_db_get_fields_by_option_id($option_id)
	{
		$t1 = self::$prefix."emgt_fields";
		$t2 = self::$prefix."emgt_sections";
		$qry = "SELECT {$t1}.*,{$t2}.section_id,{$t2}.option_id FROM {$t1} 
				INNER JOIN {$t2}
				ON {$t1}.section_id = {$t2}.section_id 
				and {$t2}.option_id = {$option_id}";
		$data= self::$wpdb->get_results($qry,ARRAY_A);		
		return $data;
	}
	
	public function emgt_db_get_fields_by_section_id($section_id)
	{
		$table = self::$prefix."emgt_fields";
		$section_id = intval($section_id);
		$data = self::$wpdb->get_results("SELECT * FROM {$table} WHERE section_id = {$section_id}",ARRAY_A);
		return $data;
	}
	
	public static function emgt_check_unique_field($table,$field,$value)
	{
		if(!is_numeric($value))  
		{
			$value = "'{$value}'"; // if not number then make it string or db query fails.
		}
		if($value === null || $field === null)
		{
			return false;
		}else{
			$table_name = self::$prefix . $table;
			$count = self::$wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE {$field} = {$value}");				
			if($count == 0){return true;}else{return false;}	
		}		
	}
	
	public static function emgt_check_unique_field_multiple($table = null,$where = null)
	{
		if($table === null || $where === null)
		{
			return false;
		}else{
			$table_name = self::$prefix . $table;
			$count = self::$wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE {$where}");				
			if($count == 0){return true;}else{return false;}	
		}
	}
	

	public function emgt_delete_usedata($user_id)
	{	
		$table_name = self::$prefix."usermeta";
		$user_id = intval($user_id);
		$result=self::$wpdb->query(self::$wpdb->prepare("DELETE FROM $table_name WHERE user_id= %d",$user_id));
		$retuenval=wp_delete_user( $user_id );
		return $retuenval;
	}
	
	public function emgt_get_count($tbl)
	{
		$t1 = self::$prefix.$tbl;
		$count = self::$wpdb->get_var( "SELECT COUNT(*) FROM {$t1}" );
		return $count;
	}

}