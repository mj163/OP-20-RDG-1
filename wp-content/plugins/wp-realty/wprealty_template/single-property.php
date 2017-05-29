<?php 
get_header();
do_action('emgt_start_wrap');
do_action('emgt_before_single_property');
$fld_obj = new Emgt_fields;
?> 
 <script>
  $(function() {
    $( ".emgt_accordion" ).accordion({
		collapsible: true
	});
	
	$(".agents_data:not(:first)").children("div.content").slideUp();
		
	 $(".emgt_collapse").click(function(){      
        $(this).parents(".agents_data").children("div.content").slideToggle();
		var cls = $(this).children("i:first-child").attr("class");
		if(cls == "fa fa-plus-square")
		{
			$(this).children("i").removeClass();
			$(this).children("i").addClass("fa  fa-minus-square");
		}else{
			$(this).children("i").removeClass();
			$(this).children("i").addClass("fa fa-plus-square");
		}
    });
  });
  </script>
 <ol class="breadcrumb emgt_breadcrumb">
	<li><a href="<?php echo esc_url(home_url('/'));?>"><?php _e("Home","estate-emgt");?></a></li>
	<li><a href="<?php echo $post->guid;?>"><?php echo $post->post_title;?></a></li>			
</ol>
<div class="rems_container">
<div class="row single_page_container">
<div class="col-sm-8 row-box">

<h3 style="border-bottom:3px solid #dedede;"><?php echo get_the_title().", #".$post->ID; ?>
<?php
	$chk_sold_status = get_post_meta($post->ID,"emgt_sold_status",true);
	if(!empty($chk_sold_status))
	{ ?>
		<span class="detail-sold-status"><?php _e("Sold","estate-emgt");?></span>				
<?php } ?> 
 </h3>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
<?php 
	$gallery = @get_post_meta($post->ID,'4_emgtfld_gallery',true);
	$gallery = $gallery['image_url'];
	$i = 1;
	?>
	<ol class="carousel-indicators">
	<?php
	foreach($gallery as $image)
	{ 
		if(!empty($image)) :	
			$i++;
		endif;
	}
	$i = 1;
	?>
	</ol>
	<div class="carousel-inner" role="listbox">
	<?php
	$gl = array();
	foreach($gallery as $image)
	{ 
		if(!empty($image)) :
	?>
			 <div class="item <?php echo ($i == 1)?'active':'';?>">
			  <a href="<?php echo $image;?>" class="fancybox"  data-fancybox-group="gallery" ><img src="<?php echo $image;?>" class="emgt_ad_image" height="420" width="1060"></a>
			 </div>			
		<?php
		$i++;
		$gl[] = $image;
		endif;
	
	}
?>   
  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only"><?php _e("Previous","estate-emgt");?></span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only"><?php _e("Next","estate-emgt");?></span>
  </a>
</div> <!-- Carousel ends -->
<br>
<div class="gallert_box">
<?php 
	foreach($gl as $img)
	{ ?>
		<a href="<?php echo $img;?>" class="fancybox"  data-fancybox-group="gallery" ><image src="<?php echo $img;?>" height="50px"></a>
<?php }
?>
</div>
<br>
<div class="property_content">
	<div class="agents_data">
	<h3><?php _e("Description","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-minus-square"></i></span></h3>
	<div class="content">		
		<?php 
			if(!empty($post->post_content))
			{
				echo "<p class='ad_desc'>".$post->post_content."</p>";
			}else{
				echo "<span>Data not available!</span>";
			}
		?>
	</div>
	</div>
	
		<?php 		
		$fields = $fld_obj->emgt_get_section_only(1);
		$i = 1;
		$checkboxes = array();
		if(!empty($fields))
		{ 
			foreach($fields as $section=>$fld)
			{ ?>
				<br>
				<div class="agents_data">
				<h3><?php _e("{$section}","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				<div class="content">
				<table class="table table-responsive borderless">
		<?php	foreach($fld as $label=>$value)
				{	
					if(!is_array($value))
					{	
						if($label != "Price" && $label != "Address") :
						if($i % 2)
						{
							echo "<tr><td class='o_td'>{$label}</td><td>{$value}</td>";
						}
						else
						{
							echo "<td class='o_td'>{$label}</td><td>{$value}</td></tr>";
						}
						$i++;
						endif;
					}else{
						$checkboxes[$label] = $value;						
					}
				}?>
				</table>
				</div>
				</div>
	<?php	} 
		}	
	if(!empty($checkboxes))
	{
		foreach($checkboxes as $label=>$key)
		{ 			
			echo '<br><div class="agents_data"><h3>'.$label.'<span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				  <div class="content">';
			foreach($key as $value)
			{
				foreach($value as $val)
				{
					echo "<span class='appliance'><i class='fa fa-check-circle'></i> {$val}</span>";
				}
			}
			echo "<br><br></div></div>";
		}
	}
	###############################################################################
	?>	
		<?php 		
		$fields = $fld_obj->emgt_get_section_only(2);
		$i = 1;
		$checkboxes = array();
		if(!empty($fields))
		{ 
			foreach($fields as $section=>$fld)
			{ ?>
				<br>
				<div class="agents_data">				
				<h3><?php _e("{$section}","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				<div class="content">
				<table class="table table-responsive borderless">
		<?php	foreach($fld as $label=>$value)
				{	
					if(!is_array($value))
					{
						if($label != "Area"):
						if($i % 2)
						{
							echo "<tr><td class='o_td'>{$label}</td><td>{$value}</td>";
						}
						else
						{
							echo "<td class='o_td'>{$label}</td><td>{$value}</td></tr>";
						}
						$i++;
						endif;
					}else{
						$checkboxes[$label] = $value;						
					}
				}?>
				</table>
				</div>
				</div>
	<?php	} 
		}	
	if(!empty($checkboxes))
	{
		foreach($checkboxes as $label=>$key)
		{ 			
			echo '<br><div class="agents_data"><h3>'.$label.'<span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				  <div class="content">';
			foreach($key as $value)
			{
				foreach($value as $val)
				{
					echo "<span class='appliance'><i class='fa fa-check-circle'></i> {$val}</span>";
				}
			}
			echo "<br><br></div></div>";
		}
	}
	###############################################################################
	?>	
	
	<?php 		
		$fields = $fld_obj->emgt_get_section_only(3);
		$i = 1;
		$checkboxes = array();
		if(!empty($fields))
		{
			foreach($fields as $section=>$fld)
			{ ?>
				<br>
				<div class="agents_data">
				<h3><?php _e("{$section}","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				<div class="content">
				<table class="table table-responsive borderless">
		<?php	foreach($fld as $label=>$value)
				{	
					if(!is_array($value))
					{
						if($i % 2)
						{
							echo "<tr><td class='o_td'>{$label}</td><td>{$value}</td>";
						}
						else
						{
							echo "<td class='o_td'>{$label}</td><td>{$value}</td></tr>";
						}
						$i++;
					}else{
						$checkboxes[$label] = $value;							
					}
				}?>
				</table>
				</div>
				</div>
	<?php	} 
		}	
	if(!empty($checkboxes))
	{
		foreach($checkboxes as $label=>$key)
		{ 			
			echo '<br><div class="agents_data"><h3>'.$label.'</h3>
				  <div class="content">';
			foreach($key as $value)
			{
				foreach($value as $val)
				{
					echo "<span class='appliance'><i class='fa fa-check-circle'></i> {$val}</span>";
				}
			}
			echo "<br><br></div></div>";
		}
	}
	###############################################################################
	?>	
	
	<br>
	<?php 
	$map_lng = get_post_meta($post->ID,"1_emgtfld_map_longitude",true);	
	$map_lat = get_post_meta($post->ID,"1_emgtfld_map_latitude",true);	
	$map_address = get_post_meta($post->ID,"emgtfld_pac_input",true);
	if(!empty($map_lng) && !empty($map_lat))
	{
	?>
	<input type="hidden" value="<?php echo $map_lat;?>" id="lat">
	<input type="hidden" value="<?php echo $map_lng;?>" id="lng">
	<input type="hidden" value="<?php echo $map_address;?>" id="add">
	<div class="agents_data">	
	<h3><?php _e("Map Location","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
	<div class="content">
		<div id="map" style="height:400px;width:auto"></div>
	<br><br>
	</div>	
	</div>
	
	<?php 
	}
	?>
	<script>
      var map;
	  var lati = jQuery("#lat").val();
	  var lng = jQuery("#lng").val();	  
      function initMap() {
		
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: parseFloat(lati), lng:parseFloat(lng)},
          zoom: 14
        });
	
        var infowindow = new google.maps.InfoWindow();
		var image = 'https://chart.googleapis.com/chart?chst=d_map_xpin_icon_withshadow&chld=pin|home|52B552|000000';
       
		var marker = new google.maps.Marker({
		  position: {lat: parseFloat(lati), lng:parseFloat(lng)}, //parseFloat(saved_lat) , parseFloat(saved_lng)
          map: map,		  
		  draggable:false,
		  icon: image,
          anchorPoint: new google.maps.Point(0, -29)
        });
      }
   jQuery('#map iframe').css("pointer-events", "none");

  $(function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 99999,
      values: [ 75, 99000 ],
      slide: function( event, ui ) { 
        $( "#amount" ).val(ui.values[ 0 ] + " - " + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "" + $( "#slider-range" ).slider( "values", 0 ) +
      " - " + $( "#slider-range" ).slider( "values", 1 ) );
	  
	$('.fancybox').fancybox();
	$("a.fancybox").attr('rel', 'gallery').fancybox();  
	$("#inq_form").validationEngine();
	  
  });

</script>
<?php 		
		$fields = $fld_obj->emgt_get_section_only(5);
		$i = 1;
		$checkboxes = array();
		if(!empty($fields))
		{
			foreach($fields as $section=>$fld)
			{ ?>
				<br>
				<div class="agents_data">				
				<h3><?php _e("{$section}","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				<div class="content">
				<table class="table table-responsive borderless">
		<?php	foreach($fld as $label=>$value)
				{	
					if(!is_array($value))
					{
						if($i % 2)
						{
							echo "<tr><td class='o_td'>{$label}</td><td>{$value}</td>";
						}
						else
						{
							echo "<td class='o_td'>{$label}</td><td>{$value}</td></tr>";
						}
						$i++;
					}else{
						$checkboxes[$label] = $value;							
					}
				}?>
				</table>
				</div>
				</div>
	<?php	} 
		}	
	if(!empty($checkboxes))
	{
		foreach($checkboxes as $label=>$key)
		{ 			
			echo '<br><div class="agents_data"><h3>'.$label.'<span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>
				  <div class="content">';
			foreach($key as $value)
			{
				foreach($value as $val)
				{
					echo "<span class='appliance'><i class='fa fa-check-circle'></i> {$val}</span>";
				}
			}
			echo "<br><br></div></div>";
		}
	}
	###############################################################################
	?>	
	
	<br>
	<div class="agents_data">
	<h3><?php _e("Floor Plan","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>	
	<div class="content">
	<?php 
		$plan_src = get_post_meta($post->ID,'5_emgtfld_floor_plan');
		if(!function_exists("array_column"))
		{
			$array = $plan_src;
			$column_name="image_url";			
			$plan_src = array_map(function($element) use($column_name){return $element[$column_name];}, $array);		
		}else{
			$plan_src = array_column($plan_src,'image_url');
		}
		
		if(!empty($plan_src[0][0]))
		{
			foreach($plan_src[0] as $src)
			{
				if(!empty($src))
				{
					echo "<img src='{$src}' alt='X Floor plan image'></img><br>";
				}
			}
			
		}
		else{
				echo "<span>Data not available!</span>";
		}	
	?>	
	<br><br>
	</div>
	</div>
	<br>
	<?php
	$a_f = get_the_terms($post->ID,"emgt_features"); 
		if ( $a_f && ! is_wp_error( $a_f ) ) 
		{ ?>
		
		<div class="agents_data">
		<h3><?php _e("Addition Features","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>		
		<div class="content">	
			<?php 
				foreach($a_f as $features)
				{
					echo "<i class='fa fa-check-circle'></i> {$features->name}<br>";
				}
			?>
		<br>
		</div>
		</div>
	<?php	}
	$video = get_post_meta($post->ID,"emgtfld_video",true);
	if(!empty($video))
	{?>
	<br>
	<div class="agents_data">
	<h3 style="border-bottom:3px solid #dedede;"><?php _e("Video","estate-emgt");?><span class="emgt_collapse"><i class="fa fa-plus-square"></i></span></h3>	
	<div class="content">	
	<?php echo $video;?>
	</div>
	</div>
<?php	}	
	?>
	<br>
	
</div>
</div>
<div class="col-sm-3 margin-box">
<div class="agents_data single_page_right_bar">
	<h3><?php _e("Details","estate-emgt");?></h3> 	
	<table class="tabel table-hover tbl-center no-margin">
		<?php 			
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
	<div class="single_page agents_profile">
	<ul id="agent_ul">	
	<?php 	
	$args = array( "role" => 'emgt_role_agent',"include"=>array($post->post_author));
	$agents = get_users($args);
	if(!empty($agents))
	{
	foreach ($agents as $agent)
	{ 
		$photo = (!empty($agent->user_photo)) ? $agent->user_photo : REMS_PLUGIN_URL."/images/default_user_logo.png";
	?>
		<li><a href="<?php echo esc_url(home_url("/"));?>?view_profile=yes&id=<?php echo $agent->ID;?>"><img src="<?php echo $photo; ?>" alt="dp_img" height='80px' width='80px'></img>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $agent->first_name ." ". $agent->last_name; ?></a></li>
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
		    <input type="submit" id="" name="save_inq" value="Send Inquiry" class="btn btn-sm btn-default">			
			<br>
			<br>
		</form>
	</div>
	<br>
	<div  class="agents_data">
<form role="" method="post" id="" action="<?php echo esc_url(home_url( '/' )); ?>">
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
  <?php echo emgt_get_currency_symbol(get_option("emgt_system_currency",true));?>
  <input type="text" name="amount" class="sng-amt" id="amount" readonly style="border:0;font-weight:bold;">
  <div id="slider-range"></div>
</div>
<br>
	<input type="submit" id="searchsubmit" value="Search">
</form>
<br>
	</div>

</div>
</div>
<br><br><br>
</div>

<!--
</div> -->

<?php
do_action('emgt_end_wrap');
get_footer();

?>