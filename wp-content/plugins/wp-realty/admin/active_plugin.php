<?php
function wp_realty_activate() {
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$sections =  array();
	$sections[]	= array("id" => 1,"name"=>"Basic Details");
	$sections[]	= array("id" => 2,"name"=>"Advance");
	$sections[]	= array("id" => 3,"name"=>"Optional");
	$sections[]	= array("id" => 4,"name"=>"Media");
	$sections[]	= array("id" => 5,"name"=>"Compliance");
	$sections = serialize($sections);
	update_option("emgt_section_list",$sections,true);
	
	global $wpdb;
	$table = $wpdb->prefix."emgt_sections";
	
	$sql = "CREATE TABLE IF NOT EXISTS {$table}(
				`section_id` int(11) NOT NULL AUTO_INCREMENT,
				`name` text NOT NULL,
				`option_id` int(11) NOT NULL,
				`created_by` int(11) NOT NULL,
				`created_date` datetime NOT NULL,
				PRIMARY KEY (`section_id`)
				)DEFAULT CHARSET=utf8";
				
	dbDelta($sql); //receiving error
	// $wpdb->query($sql);
	$check = false;
	$c_d = $wpdb->get_results("SELECT * FROM {$table}" ,ARRAY_A);

	if(empty($c_d))
	{
		$check = true;
	}
	if($check)
	{		
		$date = date('Y-m-d h:i:s');
		$id = get_current_user_id();
		
		$sql_sec = "INSERT INTO {$table} (`name`, `option_id`, `created_by`, `created_date`) VALUES
				('Basic Details', 1, {$id}, '{$date}'),
				('Features', 2, {$id}, '{$date}'),
				('Distances', 3, {$id}, '{$date}'),				
				('Gallery', 4, {$id}, '{$date}'),				
				('Attachment', 4,{$id}, '{$date}'),
				('Speciality', 5,{$id}, '{$date}')";				
		
		$wpdb->query($sql_sec);
		
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'field_order_%'" );	// delete field_order options if there any
		
		$sections = $wpdb->get_results("SELECT * FROM {$table}",ARRAY_A);
		foreach($sections as $section) // add field order option for default fields.
		{
			if($section['section_id'] != 4 && $section['section_id'] != 5) //before  5 and 6
			{				
				add_option("field_order_{$section['section_id']}");	
			}
			 
		}
	}
		/////////////////////////////////////////////////////////////////////////////////
		
		$table1 = $wpdb->prefix."emgt_fields";
	
	   $sql_fld = "CREATE TABLE IF NOT EXISTS {$table1}(
			  `field_id` int(11) NOT NULL AUTO_INCREMENT,
			  `field_type` text NOT NULL,
			  `section_id` int(11) NOT NULL,
			  `required_field` tinyint(4) NOT NULL,
			  `field_label` text CHARACTER SET latin1 NOT NULL,
			  `field_name` text CHARACTER SET latin1 NOT NULL,
			  `default_value` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
			  `placeholder` tinyint(4) NOT NULL,
			  `options` text,
			  `id` text,
			  `class` text,
			  `disable` tinyint(4) NOT NULL,
			  `lock` tinyint(4) NOT NULL DEFAULT '0',
			   PRIMARY KEY (`field_id`)
			)DEFAULT CHARSET=utf8";
				
	dbDelta($sql_fld); //receiving error
	// $wpdb->query($sql_fld);
	$check = false;
	$c_d = $wpdb->get_results("SELECT * FROM {$table1}" ,ARRAY_A);

	if(empty($c_d))
	{
		$check = true;
	}
	if($check)
	{				
		$sql_fld_data = "INSERT INTO {$table1} (`field_type`, `section_id`, `required_field`, `field_label`, `field_name`, `default_value`, `placeholder`, `options`, `id`, `class`, `disable`, `lock`) VALUES
				('Checkbox', 2, 0, 'Amenities', 'amenities', NULL, 0, 'Jacuzzi|Pool|Garden|Balcony|Elevator|Basement|Gym|Fireplace|Parking', '', '', 0, 1),
				('Checkbox', 2, 0, 'Appliances', 'appliances', NULL, 0, 'Refrigerator|Microwave|Grill|Stove|TV|Dishwasher|Internet|Oven|Washing Machine', '', '', 0, 1),
				('Textbox', 3, 0, 'Beach', 'beach', '', 0, NULL, '', '', 0, 1),
				('Textbox', 3, 0, 'Train station', 'train', '', 0, NULL, '', '', 0, 1),
				('Textbox', 3, 0, 'Metro station', 'metro', '', 0, NULL, '', '', 0, 1),
				('Textbox', 3, 0, 'Pharmacies', 'pharmacies', '', 0, NULL, '', '', 0, 1),
				('Textbox', 3, 0, 'Bakery', 'bakery', '', 0, NULL, '', '', 0, 1),
				('Checkbox', 6, 0, 'Extra', 'speciality', NULL, 0, 'Open House|Foreclosure', '', '', 0, 1),
				('Textbox', 3, 0, 'Night Club', 'night_club', NULL, 0, NULL, '', '', 0, 1),
				('Textbox', 1, 1, 'Price', 'price', '$', 1, NULL, '', '', 0, 1),
				('Textarea', 1, 1, 'Address', 'address', NULL, 0, NULL, '', '', 0, 1),
				('Textbox', 1, 1, 'State', 'state', NULL, 0, NULL, '', '', 0, 1),
				('Textbox', 1, 1, 'City', 'city', NULL, 0, NULL, '', '', 0, 1),
				('Textbox', 1, 1, 'Zipcode', 'zipcode', NULL, 0, NULL, '', '', 0, 1),
				('Textbox', 2, 1, 'Area', 'area', 'in sqft', 1, NULL, '', '', 0, 1),
				('Textbox', 2, 1, 'Bedrooms', 'bedrooms', NULL, 0, NULL, '', '', 0, 1),
				('Textbox', 2, 1, 'Bathrooms', 'bathrooms', NULL, 0, NULL, '', '', 0, 1),
				('Dropdown', 1, 1, 'Property Type', 'type', NULL, 0, 'Home|House|Villa|Building|office|Commercial Property|Land|Apartment', '', '', 0, 1),
				('Dropdown', 1, 1, 'Property For', 'for', NULL, 0, 'Sale|Rent|Vacational Rent', '', '', 0, 1),
				('Dropdown', 1, 1, 'Country', 'country', NULL, 0, 'list', '', '', 0, 1)";		
		
		$wpdb->query($sql_fld_data);
		
		update_option("field_order_1","10,11,12,13,14,18,19,20",true);
		update_option("field_order_2","1,2,15,16,17",true);
		update_option("field_order_3","3,4,5,6,7,9",true);
		update_option("field_order_6","8",true); // 4 and 5 are gallery and attachment	
	}	
	
 add_option("emgt_system_logo",REMS_PLUGIN_URL ."/images/default_emgt_logo.png"); // set default system logo
 add_option("emgt_system_user_logo",REMS_PLUGIN_URL ."/images/default_user_logo.png"); // set default user logo
 add_option("emgt_system_property_approval",1);
 add_option("emgt_system_front_color","#DE551D");
 add_option("emgt_system_front_text_color","#FFFFFF");
 
/*  remove_role( 'emgt_role_agent' ); */
/*  remove_role( 'emgt_role_owner' ); */
 $cap =  array (
      'upload_files' =>  true,
      'edit_posts' =>  true,
      'edit_published_posts' =>  true,
      'publish_posts' =>  true,
      'read' =>  true,
      'level_2' =>  true,
      'level_1' =>  true,
      'level_0' =>  true,
      'delete_posts' =>  true,
      'delete_published_posts' => true
	  );	  
 add_role( "emgt_role_agent", "Agent",$cap);
 add_role( "emgt_role_owner", "Property Owner",$cap); 

	add_option("gallery_field_show","1");
	add_option("video_field_show","1");
	add_option("floor_plan_field_show","1");
	
	add_option("emgt_system_name","Real Estate Plugin for Wordpress");
	// add_option("emgt_system_name","Real Estate Plugin for Wordpress");
	add_option("emgt_system_address","Address");
	add_option("emgt_system_phone","+99999999");
	add_option("emgt_system_country","United States");
	add_option("emgt_system_currency","USD");
	add_option("emgt_system_email","info@domain.com");
	// add_option("emgt_system_logo",""); //added in settings page
	// emgt_install_page_2();
	install_table();
	$users = count_users();
	$total_agents = 0;
	$total_owner = 0;
	$agent_role = array_key_exists("emgt_role_agent",$users['avail_roles']);
	$owner_role = array_key_exists("emgt_role_owner",$users['avail_roles']);
	if($agent_role)
	{
		$total_agents = intval($users['avail_roles']['emgt_role_agent']);
	}
	if($owner_role)
	{
		$total_owner = intval($users['avail_roles']['emgt_role_owner']);
	}
	
	
	if($total_agents == 0)
	{
		emgt_add_agents();
	}
	if($total_owner == 0)
	{
		emgt_add_owner();
	}
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
	if($total_properties == 0)
	{
		emgt_add_property();
	}
}


function install_table()
{	
	global $wpdb;

	$tbl = $wpdb->prefix . 'emgt_ads';//register attendence table	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` tinyint(4) NOT NULL,
			  `post_id` int(11) NOT NULL,
			  `activated_date` datetime NOT NULL,
			  `expiry_date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";
		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_cases';//register attendence table	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `property_id` int(11) NOT NULL,
			  `estate` text NOT NULL,
			  `assigned_to` int(11) NOT NULL,
			  `assigned_date` datetime NOT NULL,
			  `complain_by` text NOT NULL,
			  `complain` text NOT NULL,
			  `phone` text NOT NULL,
			  `status` text NOT NULL,
			  `created_date` datetime NOT NULL,
			  `created_by` tinyint(4) NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_contracts';//register attendence table	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` text NOT NULL,
			  `address` text NOT NULL,
			  `email` text NOT NULL,
			  `phone` int(11) NOT NULL,
			  `type` text NOT NULL,
			  `services` text NOT NULL,
			  `duration` text NOT NULL,
			  `period` text NOT NULL,
			  `status` text NOT NULL,
			  `price` text NOT NULL,
			  `photo` text NOT NULL,
			  `created_date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_featured_property';
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			  `property_id` int(11) NOT NULL,
			  `status` tinyint(4) NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_inquiry';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` text NOT NULL,
			  `name` text NOT NULL,
			  `email` text NOT NULL,
			  `phone` text NOT NULL,
			  `message` text NOT NULL,
			  `property_id` int(11) NOT NULL,
			  `estate` text NOT NULL,
			  `date` date NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_notes';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			  `inquiry_id` int(11) NOT NULL,
			  `task_id` int(11) DEFAULT NULL,
			  `title` text NOT NULL,
			  `description` text NOT NULL,
			  `created_date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_payments';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `plan` int(11) NOT NULL,
			  `plan_price` double NOT NULL,
			  `paid` double NOT NULL,
			  `remaining` double NOT NULL,
			  `payment_status` tinyint(4) NOT NULL,
			  `paid_via` text NOT NULL,
			  `status` tinyint(4) NOT NULL,
			  `created_date` datetime NOT NULL,
			  `expire_date` datetime DEFAULT NULL,
			  `activated_date` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);

	$tbl = $wpdb->prefix . 'emgt_payments_history';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `plan_id` int(11) NOT NULL,
			  `paid_amount` float NOT NULL,
			  `paid_via` text NOT NULL,
			  `paid_date` date NOT NULL,
			  `created_by` int(11) NOT NULL,
			  `transaction_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_plans';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			   `id` int(11) NOT NULL AUTO_INCREMENT,
				`name` text NOT NULL,
				`price` double NOT NULL,
				`quantity` int(11) NOT NULL,
				`features` text NOT NULL,
				`plan_validity` text NOT NULL,
				`plan_period` text NOT NULL,
				`ads_validity` text NOT NULL,
				`ads_period` text NOT NULL,
				`created_date` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);
	
	$tbl = $wpdb->prefix . 'emgt_plan_usage';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			   `id` int(11) NOT NULL AUTO_INCREMENT,
				`user_id` tinyint(4) NOT NULL,
				`plan` int(11) NOT NULL,
				`used_ads` int(11) NOT NULL,
				`remaining_ads` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);	
	
	$tbl = $wpdb->prefix . 'emgt_tasks';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `assigned_to` int(11) NOT NULL,
			  `inquiry_id` int(11) NOT NULL,
			  `task_detail` text NOT NULL,
			  `schedule_date` datetime NOT NULL,
			  `status` text NOT NULL,
			  `created_date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);	
	
	$tbl = $wpdb->prefix . 'emgt_sold_properties';	
	$sql = "CREATE TABLE IF NOT EXISTS {$tbl} (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `property_id` int(11) NOT NULL,
			  `sold_date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8";		
	dbDelta($sql);	
	
	
	$tbl = $wpdb->prefix . 'emgt_plans';
	$ck_plan= $wpdb->get_results("SELECT * FROM {$tbl}" ,ARRAY_A);
	if(empty($ck_plan))
	{	
		$p_date = date("Y-m-d H:i:s");
		$sql = "INSERT INTO {$tbl} (`name`, `price`, `quantity`, `features`, `plan_validity`, `plan_period`, `ads_validity`, `ads_period`, `created_date`) VALUES
				('Basic', 20, 1, 'visibility', '1', 'month', '', 'month', '{$p_date}'),
				('Silver', 50, 2, 'video,visibility,map', '1', 'month', '1', 'month', '{$p_date}'),
				('Platinum', 100, 5, 'video,visibility,map', '1', 'year', '6', 'month', '{$p_date}')";
		$insert = $wpdb->query($sql);
	}
}


function emgt_install_page_2()
{ 
	if ( !get_option('emgt_property_list_page') ) {
	$curr_page = array(
	'post_title' => __('Property List Page', 'estate-emgt'),
	'post_content' => '',
	'post_status' => 'publish',
	'post_type' => 'page',
	'comment_status' => 'closed',
	'ping_status' => 'closed',
	'post_category' => array(1),
	'post_parent' => 0 );

	$curr_created = wp_insert_post( $curr_page );	
	update_option( 'emgt_property_list_page', $curr_created );
	} 
	if(!get_option("emgt_register_page"))
	{ 
		$reg_page = array(
			'post_title' => __('Register Page', 'estate-emgt'),
			'post_content' => '[emgt_registration_form]',
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_category' => array(1),
			'post_parent' => 0 );

		$curr_created = wp_insert_post( $reg_page );		
		update_option("emgt_register_page","1");
	}
	if(!get_option("emgt_login_page"))
	{ 
		$reg_page = array(
			'post_title' => __('Register Page', 'estate-emgt'),
			'post_content' => '[emgt_login_form]',
			'post_status' => 'publish',
			'post_type' => 'page',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_category' => array(1),
			'post_parent' => 0 );

		$curr_created = wp_insert_post( $reg_page );		
		update_option("emgt_login_page","1");
	}
}

function emgt_add_agents()
{	
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	if(!email_exists("mark@mark.com"))
	{
		$download = download_url(REMS_PLUGIN_URL ."/images/default_images/mark.png");
		
		$size = filesize($download);		
		$type = filetype($download);	
		
		$uploadedfile = array("name"=>"mark.png","tmp_name"=>$download,"type"=>"image/png","size"=>$size,"error"=>0);		
		$upload_overrides = array( 'test_form' => false,'test_size' => true,'test_upload' => true );
		$image = wp_handle_sideload( $uploadedfile ,$upload_overrides);
		
		$user_id = wp_create_user("mark","mark","mark@mark.com");
		$data = array(
					"first_name"=>"Mark",
					"middle_name"=>"J",
					"last_name"=>"Barny",				
					"gender"=>"male",
					"address"=>"London",
					"phone"=>"987654321",
					"wp_capabilities"=>array("emgt_role_agent"=>true),
					"user_photo" => $image['url']
					);
		foreach($data as $key=>$value)
		{
			$chk = update_user_meta($user_id,$key,$value);
		}
	}
	######################
	if(!email_exists("jane@jane.com"))
	{
		
		$download = download_url(REMS_PLUGIN_URL ."/images/default_images/jane.png");
		$size = filesize($download);
		$type = filetype($download);
		$uploadedfile = array("name"=>"jane.png","tmp_name"=>$download,"type"=>"image/png","size"=>$size,"error"=>0);
		
		$upload_overrides = array( 'test_form' => false,'test_size' => true,'test_upload' => true );
		$image = wp_handle_sideload( $uploadedfile,$upload_overrides);
		
		$user_id = wp_create_user("jane","jane","jane@jane.com");
		$data = array(
					"first_name"=>"Jane",
					"middle_name"=>"M",
					"last_name"=>"Parker",				
					"gender"=>"female",
					"address"=>"New York",
					"phone"=>"152678554",
					"wp_capabilities"=>array("emgt_role_agent"=>true),
					"user_photo" => $image['url']
					);
		foreach($data as $key=>$value)
		{
			$chk = update_user_meta($user_id,$key,$value);
		}
	}
}

function emgt_add_owner()
{
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	if(!email_exists("alex@alex.com"))
	{
		$download = download_url(REMS_PLUGIN_URL ."/images/default_images/alex.png");
		
		$size = filesize($download);		
		$type = filetype($download);	
		
		$uploadedfile = array("name"=>"alex.png","tmp_name"=>$download,"type"=>"image/png","size"=>$size,"error"=>0);		
		$upload_overrides = array( 'test_form' => false,'test_size' => true,'test_upload' => true );
		$image = wp_handle_sideload( $uploadedfile ,$upload_overrides);
		
		$user_id = wp_create_user("alex","alex","alex@alex.com");
		$data = array(
					"first_name"=>"Alex",
					"middle_name"=>"J",
					"last_name"=>"O'connor",				
					"gender"=>"male",
					"address"=>"123 Street,New York",
					"phone"=>"655895648",
					"wp_capabilities"=>array("emgt_role_owner"=>true),
					"user_photo" => $image['url']
					);
		foreach($data as $key=>$value)
		{
			$chk = update_user_meta($user_id,$key,$value);
		}
	}
}

function emgt_add_property()
{
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$my_posts[0] = array(
	'post_title'    => "Villa Park",
	'post_content'  => "Onec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis Diam.Onec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis DiamOnec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis Diam",
	'post_status'   => 'publish',
	'post_author'   => 1,
	'post_type' => 'emgt_add_listing'  
    );
	
	$my_posts[1] = array(
	'post_title'    => "Retro House",
	'post_content'  => "Onec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis Diam.Onec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis DiamOnec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis Diam",
	'post_status'   => 'publish',
	'post_author'   => 1,
	'post_type' => 'emgt_add_listing'  
    );
	
	$my_posts[2] = array(
	'post_title'    => "Office Building",
	'post_content'  => "Onec Lobortis, Metus Dictum Dictum Vehicula, Massa Nisl Tincidunt Lectus, Id Iaculis Diam Dolor At Eros. Sed Dapibus Purus Quam, A Blandit Odio Euismod Eu. Maecenas Vel Mattis Diam, A Eleifend Erat. Nam Ornare Ultrices Est, Ac Dapibus Odio Tincidunt Nec. Donec Mattis At Ipsum In Facilisis. Curabitur Ac Dolor Vitae Purus Pellentesque Pretium. Donec In Massa Eget Magna Vulputate Malesuada Ultricies Id Nulla. Nullam Laoreet Arcu Neque. Nam Interdum Ex In Enim Luctus, Vel Sagittis Justo Sollicitudin. In Imperdiet, Purus A Sollicitudin Egestas, Augue Est Accumsan Libero, Vel Rhoncus Leo Diam Et Enim. Nunc Cursus Bibendum Enim, Eu Finibus Lacus Efficitur A. Phasellus Porttitor, Odio Quis Volutpat Malesuada, Ex Augue Facilisis Mauris, At Imperdiet Sem Leo Vel Enim. Quisque Id Imperdiet Magna, Ac Euismod Elit. Nam A Malesuada Libero. Proin Efficitur Dolor Id Magna Congue Consequat. In Eget Augue Maximus, Vulputate Mi Sit Amet, Iaculis Dolor.",
	'post_status'   => 'publish',
	'post_author'   => 1,
	'post_type' => 'emgt_add_listing'  
    );
	$imgfile= array();
	$imgfile[0] = REMS_PLUGIN_URL .'/images/default_images/villa.jpg';
	$imgfile[1] = REMS_PLUGIN_URL .'/images/default_images/retro.jpg';
	$imgfile[2] = REMS_PLUGIN_URL .'/images/default_images/office.jpg';
	$i = 0;
   
	$metadata = get_property_meta_data();   
   
	foreach($my_posts as $my_post)
	{	
		$post_id = wp_insert_post($my_post);
		$image_url  = $imgfile[$i]; // Define the image URL here			
			
		$upload_dir = wp_upload_dir(); // Set upload folder
		$image_data = file_get_contents($image_url); // Get image data
		$filename   = basename($image_url); // Create image file name 

		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
		$file = $upload_dir['path'] . '/' . $filename;
		} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
		} 

		// Create the image  file on the server
		file_put_contents( $file, $image_data );

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit'
		);

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		// Include image.php
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
		save_property_meta($post_id,$metadata[$i]);		
		$i++;
	}
}
	
function get_property_meta_data()
{
	// $wp_upload_dir = wp_upload_dir();
	$meta  = array();
	// $imgurl = $wp_upload_dir["baseurl"]."/default_images/";	
	$imgurl = REMS_PLUGIN_URL ."/images/default_images/";	
					
	$meta[0]["6_emgtfld_extra"] = '';
	$meta[0]["5_emgtfld_floor_plan"] = array("image_url" =>array($imgurl ."floor.jpg"));
	$meta[0]["emgtfld_video"] = "";
	$meta[0]["4_emgtfld_gallery"] = array("image_url" => array($imgurl ."v_1.jpg",$imgurl."v_2.jpg"));
	$meta[0]["3_emgtfld_night_club"] = '<iframe width="560" height="315" src="https://www.youtube.com/embed/xcJtL7QggTI" frameborder="0" allowfullscreen></iframe>';
	$meta[0]["3_emgtfld_bakery"] = "200m";
	$meta[0]["3_emgtfld_pharmacies"] = "35m";
	$meta[0]["3_emgtfld_metro"] = "250m";
	$meta[0]["3_emgtfld_train"] = "180m";
	$meta[0]["3_emgtfld_beach"] = "150m";
	$meta[0]["2_emgtfld_bathrooms"] = "2";
	$meta[0]["2_emgtfld_bedrooms"] = "2";
	$meta[0]["2_emgtfld_area"] = "135sqft";
	$meta[0]["2_emgtfld_appliances"] =  array("Refrigerator","Microwave","TV","Internet");
	$meta[0]["2_emgtfld_amenities"] = array("Balcony","Garden","Basement","Parking");
	$meta[0]["1_emgtfld_country"] = "au";
	$meta[0]["1_emgtfld_for"] = "Sale";
	$meta[0]["1_emgtfld_type"] = "Villa";
	$meta[0]["1_emgtfld_zipcode"] = "456858";
	$meta[0]["1_emgtfld_city"] = "Coolabah";
	$meta[0]["1_emgtfld_state"] = "New South wales";
	$meta[0]["1_emgtfld_address"] = "Old Street";
	$meta[0]["1_emgtfld_price"] = "70000";
	$meta[0]["1_emgtfld_map_latitude"] = "-31.2532183";
	$meta[0]["1_emgtfld_map_longitude"] = "146.92109900000003";
	
	######################################################################################
					
	$meta[1]["6_emgtfld_extra"] = '';
	$meta[1]["5_emgtfld_floor_plan"] = array("image_url" =>array($imgurl ."floor.jpg"));
	$meta[1]["emgtfld_video"] = "";
	$meta[1]["4_emgtfld_gallery"] = array("image_url" => array($imgurl ."r_1.jpg",$imgurl."r_2.jpg"));
	$meta[1]["3_emgtfld_night_club"] = "245m";
	$meta[1]["3_emgtfld_bakery"] = "300m";
	$meta[1]["3_emgtfld_pharmacies"] = "800m";
	$meta[1]["3_emgtfld_metro"] = "1000m";
	$meta[1]["3_emgtfld_train"] = "2000m";
	$meta[1]["3_emgtfld_beach"] = "8000m";
	$meta[1]["2_emgtfld_bathrooms"] = "2";
	$meta[1]["2_emgtfld_bedrooms"] = "3";
	$meta[1]["2_emgtfld_area"] = "143sqft";
	$meta[1]["2_emgtfld_appliances"] = array("Refrigerator","Washing Machine","TV","Grill");
	$meta[1]["2_emgtfld_amenities"] = array("Jacuzzi","Garden","Pool","Parking","Gym");
	$meta[1]["1_emgtfld_country"] = "nz";
	$meta[1]["1_emgtfld_for"] = "Rent";
	$meta[1]["1_emgtfld_type"] = "House";
	$meta[1]["1_emgtfld_zipcode"] = "658542";
	$meta[1]["1_emgtfld_city"] = "Wellington";
	$meta[1]["1_emgtfld_state"] = "Lower Hutt";
	$meta[1]["1_emgtfld_address"] = "12th Street Wellington";
	$meta[1]["1_emgtfld_price"] = "55000";
	$meta[1]["1_emgtfld_map_latitude"] = "-41.2864603";
	$meta[1]["1_emgtfld_map_longitude"] = "174.77623600000004";
	
	######################################################################################	
					
	$meta[2]["6_emgtfld_extra"] = '';
	$meta[2]["5_emgtfld_floor_plan"] = array("image_url" =>array($imgurl ."floor.jpg"));
	$meta[2]["emgtfld_video"] = "";
	$meta[2]["4_emgtfld_gallery"] = array("image_url" => array($imgurl ."o_1.jpg",$imgurl."o_2.jpg",$imgurl."o_3.jpg"));
	$meta[2]["3_emgtfld_night_club"] = "";
	$meta[2]["3_emgtfld_bakery"] = "";
	$meta[2]["3_emgtfld_pharmacies"] = "";
	$meta[2]["3_emgtfld_metro"] = "";
	$meta[2]["3_emgtfld_train"] = "";
	$meta[2]["3_emgtfld_beach"] = "";
	$meta[2]["2_emgtfld_bathrooms"] = "3";
	$meta[2]["2_emgtfld_bedrooms"] = "1";
	$meta[2]["2_emgtfld_area"] = "500sqft";
	$meta[2]["2_emgtfld_appliances"] = array("Refrigerator","Microwave","TV","Internet");
	$meta[2]["2_emgtfld_amenities"] = array("Balcony","Garden","Basement","Parking");
	$meta[2]["1_emgtfld_country"] = "us";
	$meta[2]["1_emgtfld_for"] = "Rent";
	$meta[2]["1_emgtfld_type"] = "Home";
	$meta[2]["1_emgtfld_zipcode"] = "456858";
	$meta[2]["1_emgtfld_city"] = "Chicago";
	$meta[2]["1_emgtfld_state"] = "Illinois";
	$meta[2]["1_emgtfld_address"] = "13rd Street";
	$meta[2]["1_emgtfld_price"] = "66000";
	$meta[2]["1_emgtfld_map_latitude"] = "41.8781136";
	$meta[2]["1_emgtfld_map_longitude"] = "-87.62979819999998";
	
	return $meta;
}
	
	
function save_property_meta($post_id,$metadata)
{
	foreach($metadata as $key=>$value)
	{
		update_post_meta($post_id,$key,$value);
	}
}

?>