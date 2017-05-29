<div class="col-md-3 margin-box" style="width:auto;">
<div class="agents_data">
	<h3><?php _e("Details","estate-emgt");?></h3> 	
	<table class="tabel table-hover tbl-center no-margin">
		<?php 		
			global $post;
			
			$fld_obj = new Emgt_fields;
			if(!$fld_obj->emgt_is_field_disable("price"))
			{ ?>
				<tr><td><strong><?php _e("Price:","estate-emgt");?></strong></td><td class="tbl-right"><span class="item-detail success-label"><?php echo emgt_get_currency_symbol(get_option("emgt_system_currency"))." ".get_post_meta($post->ID,"1_emgtfld_price",true); ?></span></td></tr>
	<?php   }
	
			if(!$fld_obj->emgt_is_field_disable("address"))
			{ ?>		
				<tr><td><strong><?php _e("Address:","estate-emgt");?></strong></td><td class="tbl-right"><span class="item-detail"><?php echo get_post_meta($post->ID,"1_emgtfld_address",true); ?></span></td></tr>
	<?php   }
		
			if(!$fld_obj->emgt_is_field_disable("area"))
			{
			?>
				<tr><td><strong><?php _e("Area:","estate-emgt");?></strong></td><td class="tbl-right"><?php echo get_post_meta($post->ID,"2_emgtfld_area",true); ?> Sqft</td></tr>
	<?php   } ?>
	</table>	
</div>
<br>
	<div class="agents_data">
	<h3 class="h_border"><?php _e("Agent","estate-emgt");?></h3>	
	<div class="agents_profile">
	<ul id="agent_ul">	
	<?php 	
	// $args = array( "role" => 'emgt_role_agent',"include"=>array($post->post_author));
	$args = array( "role" => 'emgt_role_agent',"number"=>5);
	$agents = get_users($args);
	if(!empty($agents))
	{
	foreach ($agents as $agent)
	{ 
		$photo = (!empty($agent->user_photo)) ? $agent->user_photo : REMS_PLUGIN_URL."/images/default_user_logo.png";
	?>
		<li><a href="<?php echo esc_url(home_url("/"));?>?view_profile=yes&id=<?php echo $agent->ID;?>"><img src="<?php echo $photo; ?>" alt="dp_img" height='80px' width='80px'></img>&nbsp;&nbsp;&nbsp;<?php echo $agent->first_name ." ". $agent->last_name; ?></a></li>
<?php }
	}
	else{
		echo "<li>No agents available !</li>";
	} ?>
	</ul>
		</div>
	</div>	
	<br>
	<div class="inq_frm agents_data">
		<form role="form" method="post" action="<?php echo the_permalink();?>" id="inq_form">			
			<h3><?php _e("Inquiry From","estate-emgt");?></h3>
			<input type="hidden" value="<?php echo $post->ID;?>" name="p_id">
			<input type="hidden" value="<?php echo $post->title;?>" name="title">
			<br>
		  <div class="form-group">
			<label for="name"><?php _e("Name","estate-emgt");?>:</label><span class="require-field">*</span>
			<input type="text" class="form-control validate[required]" id="name" name="name">
		  </div>
		  <div class="form-group">
			<label for="email"><?php _e("Email","estate-emgt");?>:</label><span class="require-field">*</span>
			<input type="email" class="form-control validate[required]" id="email" name="email">
		  </div>
		  <div class="form-group">
			<label for="phone"><?php _e("Phone","estate-emgt");?>:</label><span class="require-field">*</span>
			<input type="text" class="form-control validate[required]" id="phone" name="phone">
		  </div>
		  <div class="form-group">
			<label for="title"><?php _e("Subject","estate-emgt");?>:</label><span class="require-field">*</span>
			<input type="text" class="form-control validate[required]" id="title" name="title">
		  </div>
		  <div class="form-group">
			<label for="message"><?php _e("Message","estate-emgt");?>:</label><span class="require-field">*</span>
			<textarea class="form-control validate[required]" id="message" name="message"></textarea>
		  </div>
		    <input type="submit" id="" name="save_inq" value="Send Inquiry" >
			<br>
			<br>
		</form>
	</div>
	<br>
	<div  class="agents_data">
<form role="" method="get" id="" action="<?php echo esc_url(home_url( '/' )); ?>">
<h3 class="h_border"><?php _e("Search Property","estate-emgt");?></h3>
<br>
<input type="hidden" name="s" id="s" value=""/>
<input type="hidden" name="emgt_search" value="yes"/>
<input type="hidden" name="post_type" value="<?php echo get_query_var("post_type");?>"/>
<div class="form-group">
<?php 
			$url = REMS_PLUGIN_URL.'/lib/countrylist.xml';			
			if(get_remote_file($url))
			{
				$xml =simplexml_load_string(get_remote_file($url));				
			}
			else 
			{ die("Error: Cannot create object");	}
			
		?>
			<select style="width:100%" name="country" id="country">
				<option value=""><?php _e(" -- Select Location -- ","estate-emgt");?></option>
				<?php
					foreach($xml as $country)
					{ ?>
					 <option value="<?php echo $country->code;?>" value="<?php echo $country->name;?>" ><?php echo $country->name;?></option>
				<?php } ?>				
			</select>
</div>
<div class="form-group">
	<input type="text" name="state" id="state" placeholder="Enter State"/><br />
</div>
<div class="form-group">
	<select style="width:100%" name="type">
		<option value=""><?php _e(" -- Select Type -- ","estate-emgt");?></option>
		<option value="Building"><?php _e("Building","estate-emgt");?></option>
		<option value="home"><?php _e("Home","estate-emgt");?></option>
		<option value="House"><?php _e("House","estate-emgt");?></option>
		<option value="office"><?php _e("office","estate-emgt");?></option>
		<option value="Land"><?php _e("Land","estate-emgt");?></option>
		<option value="Apartment"><?php _e("Apartment","estate-emgt");?></option>
		<option value="Villa"><?php _e("Villa","estate-emgt");?></option>
		<option value="Commercial Property"><?php _e("Commercial Properties","estate-emgt");?></option>
	</select>
</div>
<div class="form-group">
<input type="radio" name="status" id="status" value="sale" /> <?php _e("Sale","estate-emgt");?> <br>
<input type="radio" name="status" id="status" value="rent" /> <?php _e("Rent","estate-emgt");?><br>
<input type="radio" name="status" id="status" value="vacational_rent" /> <?php _e("Vacation Rent","estate-emgt");?>
</div>
<div class="form-group">
<input type="number" name="beds" id="beds" placeholder="Bedrooms" /><br />
</div>

<div class="form-group">
  <label for="amount"><?php _e("Price range","estate-emgt");?>:</label>
  <?php echo emgt_get_currency_symbol(get_option("emgt_system_currency",true));?><input type="text" name="amount" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
  <div id="slider-range"></div>
</div>
<br>
	<input type="submit" id="searchsubmit" value="Search">
</form>
<br>
	</div>

</div>