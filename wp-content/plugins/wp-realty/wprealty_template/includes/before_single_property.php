<?php
if(isset($_POST['save_inq']))
{
	$estate = "";
	$property = intval($_POST['p_id']);
	$estate = sanitize_text_field($_POST['title']);
	$data = array("title" => $estate,
				"name" => sanitize_text_field($_POST['name']),
				"email" => sanitize_email($_POST['email']),
				"phone" => intval($_POST['phone']),				
				"message" => sanitize_text_field($_POST['message']),				
				"property_id" => $property,
				"estate" => $estate,
				"date" => date("Y-m-d H:i:s")
				);
	$db= new Emgt_Db;
	$chk = $db->emgt_insert("emgt_inquiry",$data);
	if($chk)
	{
		$success = 1;
	}else{
		$success = 0;
	}
	
}
?>