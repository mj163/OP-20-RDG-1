<?php 
get_header();
wp_enqueue_style('fa-min-style',REMS_PLUGIN_URL.'/css/font-awesome.min.css');
wp_enqueue_style('grid-12',REMS_PLUGIN_URL.'/css/bs3.5.6/grid12.css');	
wp_enqueue_style('btp-css',REMS_PLUGIN_URL .'/css/bs3.5.6/bootstrap.min.css');
wp_enqueue_script('jq-lib',REMS_PLUGIN_URL.'/js/jquery-1.11.1.js');
wp_enqueue_script('bootstrap-min-js-3.2.2',REMS_PLUGIN_URL.'/js/bs3.2.2/bootstrap.min.js',array('jquery'));
wp_enqueue_script('sortable-js',REMS_PLUGIN_URL.'/js/sortable/jquery-ui.js');
wp_enqueue_style('sortable-ui',REMS_PLUGIN_URL.'/js/sortable/jquery-ui.css');
wp_enqueue_script('addrule',REMS_PLUGIN_URL.'/js/jquery.addrule.js',array('jquery'));
$template = get_option('template');
if($template=="twentyfifteen")
{
	wp_enqueue_style('fifteen-custom',REMS_PLUGIN_URL.'/css/twentyfifteen.css');
}
if($template=="twentyfourteen")
{
	wp_enqueue_style('fourteen-custom',REMS_PLUGIN_URL.'/css/twentyfourteen.css');
}
if($template=="twentysixteen")
{
	wp_enqueue_style('sixteen-custom',REMS_PLUGIN_URL.'/css/twentysixteen.css');
}
do_action('emgt_start_wrap');
include_once REMS_PLUGIN_DIR ."/template/search_filteration.php";
$db = new Emgt_Db;

$bg_color = get_option("emgt_system_front_color");
$bg_text_color = get_option("emgt_system_front_text_color");	
?>
<script>
jQuery(document).ready(function(){		
		jQuery('.search_for').css("background-color",'<?php echo $bg_color; ?> !important');
		jQuery('.search_for').css("color",'<?php echo $bg_text_color; ?>');
		jQuery('.sel_for').css("background-color",'<?php echo $bg_color; ?>');		
		jQuery('.sel_for').css("color",'<?php echo $bg_text_color; ?>');
		addRule(".for_active:before", "color: <?php echo $bg_color; ?>");			
		addRule(".icon_marker:before", "color: <?php echo $bg_color; ?>");			
		addRule(".icon_type:before", "color: <?php echo $bg_color; ?>");			
		addRule(".icon_beds:before", "color: <?php echo $bg_color; ?>");			
		addRule(".for_active", "background-color: rgba(0, 0, 0, 0.66) !important");
		addRule(".for_active", "color: #FFF");			
		$('.ad_list_item').hover(function(e){
			$(this).css("outline",e.type === "mouseenter"?"1px solid <?php echo $bg_color; ?>":"transparent");
		});
		jQuery('.body-box').css("border-right",'2px solid <?php echo $bg_color; ?>');
		jQuery('.fa').css("color",'<?php echo $bg_color; ?>');
		jQuery('.emgt_price').css("color",'<?php echo $bg_color; ?>');
		jQuery('.view_btn').css("background-color",'<?php echo $bg_color; ?>');
		jQuery('.view_btn').css("color",'<?php echo $bg_text_color; ?>');
		jQuery('.listing_right').css("border-left",'1px solid <?php echo $bg_color; ?>');
});		
</script>

<script>
  jQuery(function(){
	 jQuery( ".icon , .fa" ).tooltip();
    jQuery( "#slider-range" ).slider({
      range: true,
      min: 0,
      max: 99999,
      values: [ 75, 99999 ],
	   create:function( event, ui ) {
		  $('.ui-slider-range').css('background','<?php echo $bg_color;?>');
	  },
      slide: function( event, ui ) {       
        jQuery( "#amount" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
      }
    });
    jQuery( "#amount" ).val( "" + jQuery( "#slider-range" ).slider( "values", 0 ) +
      " - " + jQuery( "#slider-range" ).slider( "values", 1 ) );
	 
	$(".sel_for").click(function(){
		$(".sel_for").attr("class","search_for sel_for");
		$(this).addClass("for_active");
		var value = $(this).attr("val");
		$(".status").val(value);
	});  
  });
</script>
<ol class="breadcrumb emgt_breadcrumb">
	<li><a href="<?php echo esc_url(home_url('/'));?>"><?php _e("Home","estate-emgt");?></a></li>
	<li><a href="<?php echo @$post->guid;?>"><?php _e("Search List","estate-emgt");?></a></li>			
</ol>
<div class="rems_container emgt_search_page">

<div class="head_box">
<div class="container-fluid">
<div class="row search_box">
	<div class="col-sm-3 text-center search_image">
		<img src="<?php echo REMS_PLUGIN_URL."/images/home_img.png";?>" alt="image">
	</div>
	<div class="col-sm-4">
		<form role="form" id="searchform" method="post" action="<?php echo home_url("/");?>">
		<input type="hidden" name="s" id="s" value=""/>
		<input type="hidden" name="emgt_search" value="yes"/>
		<input type="hidden" name="post_type" value="emgt_add_listing"/>
		<input type="hidden" name="status" class="status" value="sale"/>
		<p class="for_box">
		<span class="for_active sel_for" val="Sale"><?php _e("Sale","estate-emgt");?></span> &nbsp; 
		<span class="search_for sel_for" val="Rent"><?php _e("Rent","estate-emgt");?></span> &nbsp; 
		<span class="search_for sel_for" val="vacational rent"><?php _e("Vacational Rent","estate-emgt");?></span>
		</p>
		<p class='src_label search-box-space'>	
		<?php _e("Location","estate-emgt");?>
		<?php 
			$url = REMS_PLUGIN_URL.'/lib/countrylist.xml';
			if(get_remote_file($url))
			{
				$xml =simplexml_load_string(get_remote_file($url));				
			}
			else 
			{ die("Error: Cannot create object");	}
			
		?>
			<div class="search_icon icon_marker">
			<select name="country">
				<option value=""> -- <?php _e("Select Location","estate-emgt");?> -- </option>
				<?php
					foreach($xml as $country)
					{ ?>
					 <option value="<?php echo $country->code;?>" value="<?php echo $country->name;?>" ><?php echo $country->name;?></option>
				<?php } ?>				
			</select>
			</div>
		</p>
		
		<p class="price_in_img">
		  <label for="amount"><?php _e("Price range","estate-emgt");?>: <?php echo emgt_get_currency_symbol(get_option("emgt_system_currency"));?></label>
		  <input type="text" id="amount" class="price_in_img" name="amount" readonly>
		</p>
		<p>
			<div id="slider-range"></div>
		</p>
	</div>
	<div class="col-sm-3 text-center">
		<p class="src_label search-box-space-2">
			<?php _e("Type","estate-emgt");?>
			<div class="search_icon icon_type">
			<select style="width:100%" name="type">
				<option value=""> -- <?php _e("Select Type","estate-emgt");?> -- </option>
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
		</p>
		
		<p class="src_label">
			<?php _e("Bedrooms","estate-emgt");?>
			<div class="search_icon icon_beds">
			<select style="width:100%" name="beds">
				<option value=""> -- <?php _e("Select Bedrooms","estate-emgt");?> -- </option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="4">5</option>
				<option value="4">6</option>
			</select>
			</div>
		</p>
	</div>
	<div class="col-sm-2 text-center">
	<p>&nbsp;
	</p><p>&nbsp;
	</p>
	<br>	
	<p>
		<input type="submit" value="<?php _e("Search","estate-emgt");?>" id="searchsubmit" class="search-box-space">
	</p>
	</form>
	</div>
</div>
</div>
</div>

<div class="row">
	<div class="col-sm-8 col-md-8 col-xs-12">

<?php
add_filter("the_content", "emgt_content_cap");

function emgt_content_cap($content)
{
	if(isset($_GET['emgt_search']) && $_GET['emgt_search'] =='yes')
	{
		$str = substr($content, 0, 300);
		$str .= "<a href='".get_permalink()."'>...Read More</a>";
		return $str;
	}
}
 if ( have_posts() ) : 

	while ( have_posts() ) : the_post();			
		$p_id = get_the_ID();			
		$status = get_post_status($p_id);	
			?>		
		<div class="ad_list_item">						
		<div class="col-sm-4 col-md-4 col-xs-12 body-box">			
		<?php 
			$src = get_the_post_thumbnail_url($p_id);
			if(!empty($src))
			{
				echo "<image src='{$src}' class='ad_feature_image' />";
			}else{
				 echo '<img src="' . REMS_PLUGIN_URL.'/images/no-thumbnail.png" class="ad_feature_image"/>';
			}			
			?>
			<?php
				$chk_sold_status = get_post_meta($p_id,"emgt_sold_status",true);
				if(!empty($chk_sold_status))
				{ ?>
					<span class="emgt-sold-badge"><?php _e("Sold","estate-emgt");?></span>				
		<?php   } ?>
			</div>
			<div class="col-sm-6 col-md-6 col-xs-12">			
			<div class="ad_list_item_top">
				<div class="top_left col-md-7 col-sm-12 col-xs-12 no-padding">	
				<?php 
					echo "<h4><a href='".get_permalink()."' class='emgt_ad_title'>".get_the_title()."</a><span class='ad_country'>
					<i class='fa fa-map-marker'></i> ".get_post_meta($p_id,"1_emgtfld_country",true).",".get_post_meta($p_id,"1_emgtfld_city",true)."</span></h4>";
				?>
				</div>
				<div class="top_right col-md-5 col-sm-12 col-xs-12">
					<?php
						$amenities = get_post_meta($p_id,"2_emgtfld_amenities",true);	
						if(!empty($amenities)) :
						foreach($amenities as $amenity)
						{
							switch($amenity)
							{
								CASE "Pool":
									// echo '<img src="'.REMS_PLUGIN_URL .'/images/pool.png" title="Pool">&nbsp;&nbsp;&nbsp;';
									echo '<span title="Pool"><svg class="icon icon-pool">
										<use xlink:href="#icon-pool"></use>
										<symbol id="icon-pool" viewBox="0 0 32 32">
											<title>hotel-icon-has-pool-download</title>
											<path class="path1" fill="#F5F5F5" stroke="" stroke-width="0.4338" stroke-miterlimit="4" stroke-linecap="butt" stroke-linejoin="miter" d="M16 0.217h-14.534c-0.69 0-1.249 0.559-1.249 1.249v29.067c0 0.69 0.559 1.249 1.249 1.249h29.067c0.69 0 1.249-0.559 1.249-1.249v-29.067c0-0.69-0.559-1.249-1.249-1.249h-14.534z"></path>
											<path class="path2" fill="'.$bg_color.'" d="M28.672 19.144c-0.221 0.067-0.505 0.054-0.879-0.025-0.38-0.086-0.726-0.489-1.357-0.467-0.646 0.022-1.559 0.601-2.44 0.601-0.894-0.019-1.899-0.627-2.811-0.627-0.909 0.006-1.736 0.636-2.609 0.656-0.888 0.003-1.773-0.588-2.643-0.601-0.882-0.022-1.718 0.525-2.542 0.518-0.811-0.016-1.531-0.598-2.336-0.572-0.827 0.032-1.733 0.672-2.542 0.71-0.814 0.013-1.552-0.595-2.272-0.601-0.726-0.013-1.54 0.445-2.030 0.547-0.49 0.093-0.799 0.102-0.916 0.029v1.695c0.116 0.073 0.426 0.064 0.916-0.029 0.49-0.102 1.304-0.56 2.030-0.547 0.72 0.006 1.458 0.614 2.272 0.601 0.808-0.032 1.715-0.688 2.542-0.71 0.805-0.032 1.525 0.563 2.336 0.576 0.824 0.007 1.66-0.537 2.542-0.521 0.87 0.013 1.755 0.604 2.643 0.601 0.873-0.019 1.699-0.649 2.609-0.656 0.913-0.003 1.917 0.614 2.811 0.63 0.882 0 1.794-0.579 2.44-0.601 0.631-0.029 0.977 0.381 1.357 0.464 0.374 0.080 0.658 0.093 0.879 0.026v-1.695z"></path>
											<path class="path3" fill="'.$bg_color.'" d="M14.16 18.243l7.006-3.738c-0.551-1.209-0.94-2.12-1.167-2.769-0.233-0.646-0.331-0.79-0.199-1.097 0.135-0.317-0.052-0.365 0.974-0.783 1.026-0.435 4.094-1.682 5.095-1.743 0.98-0.051 0.94 1.030 0.778 1.394-0.178 0.355-1.090 0.444-1.782 0.726-0.701 0.269-1.473 0.569-2.364 0.902l3.206 7.054c-0.413 0.355-0.962 0.493-1.703 0.438-0.753-0.073-1.758-0.79-2.695-0.793-0.934 0.003-2 0.803-2.854 0.819-0.857-0.003-1.537-0.694-2.254-0.764-0.719-0.071-1.396 0.048-2.042 0.355z"></path>
											<path class="path4" fill="'.$bg_color.'" d="M26.748 12.138c1.080 0 1.957 0.915 1.957 2.043s-0.877 2.043-1.957 2.043c-1.080 0-1.957-0.915-1.957-2.043s0.877-2.043 1.957-2.043z"></path>
											<path class="path5" fill="'.$bg_color.'" d="M9.432 18.38c-0.83-0.154-1.675-0.39-2.594-0.71-0.925-0.339-2.502-0.793-2.875-1.238-0.374-0.457-0.101-1.372 0.652-1.413 0.756-0.042 2.257 0.63 3.815 1.186 1.549 0.55 3.322 1.238 5.414 2.098-0.257 0.141-0.597 0.185-1.032 0.121-0.441-0.074-0.998-0.48-1.565-0.492-0.569-0.010-1.167 0.144-1.816 0.448z"></path>
											<path class="path6" fill="'.$bg_color.'" d="M28.672 21.981c-0.221 0.061-0.505 0.051-0.879-0.029-0.38-0.083-0.726-0.493-1.357-0.464-0.646 0.022-1.559 0.601-2.44 0.601-0.894-0.019-1.899-0.627-2.811-0.627-0.909 0.006-1.736 0.636-2.609 0.655-0.888 0.003-1.773-0.588-2.643-0.601-0.882-0.022-1.718 0.524-2.542 0.518-0.811-0.016-1.531-0.598-2.336-0.572-0.827 0.032-1.733 0.671-2.542 0.71-0.814 0.013-1.552-0.595-2.272-0.601-0.726-0.013-1.54 0.445-2.030 0.547-0.49 0.099-0.799 0.096-0.916 0.026v1.695c0.116 0.070 0.426 0.074 0.916-0.026 0.49-0.102 1.304-0.56 2.030-0.547 0.72 0.006 1.458 0.614 2.272 0.601 0.808-0.039 1.715-0.678 2.542-0.71 0.805-0.026 1.525 0.556 2.336 0.572 0.824 0.006 1.66-0.541 2.542-0.518 0.87 0.013 1.755 0.604 2.643 0.601 0.873-0.019 1.699-0.649 2.609-0.655 0.913 0 1.917 0.608 2.811 0.627 0.882 0 1.794-0.579 2.44-0.601 0.631-0.022 0.977 0.38 1.357 0.467 0.374 0.080 0.658 0.093 0.879 0.026v-1.695z"></path>
										</symbol>									
									</svg></span>';
								break;
								CASE "Parking":
									echo '<i class="fa fa-car fa-lg" title="Parking"></i>&nbsp;&nbsp;&nbsp;';
								break;
								CASE "Garden":
									// echo '<img src="'.REMS_PLUGIN_URL .'/images/garden.png" title="Garden">&nbsp;&nbsp;&nbsp;';
									echo '<span title="Garden"><svg class="icon icon-garden"><use xlink:href="#icon-garden"></use>
										<symbol id="icon-garden"  fill="'.$bg_color.'" stroke="'.$bg_color.'" viewBox="0 0 35 32">
											<title>garden</title>
											<path class="path1" d="M6.145 0.814c-1.559 2.352-2.821 4.421-3.193 5.234-0.193 0.428-0.193 0.441-0.193 1.876v1.441l-0.772 0.028c-0.862 0.028-1.055 0.097-1.469 0.503-0.483 0.49-0.483 0.49-0.483 2.517s0 2.028 0.483 2.517c0.414 0.407 0.607 0.476 1.462 0.503l0.779 0.028v4.386l-0.779 0.028c-0.855 0.028-1.048 0.097-1.462 0.503-0.483 0.49-0.483 0.49-0.483 2.517s0 2.028 0.483 2.517c0.414 0.407 0.607 0.476 1.462 0.503l0.772 0.028 0.021 2.428c0.021 2.69 0.014 2.614 0.503 3.11 0.503 0.497 0.393 0.483 3.345 0.483s2.841 0.014 3.345-0.483c0.49-0.497 0.483-0.421 0.503-3.124l0.021-2.428h3.297l0.021 2.428c0.021 2.703 0.014 2.628 0.503 3.124 0.503 0.497 0.393 0.483 3.345 0.483s2.841 0.014 3.345-0.483c0.49-0.497 0.483-0.421 0.503-3.124l0.021-2.428h3.297l0.021 2.428c0.021 2.703 0.014 2.628 0.503 3.124 0.503 0.497 0.393 0.483 3.345 0.483s2.841 0.014 3.345-0.483c0.49-0.497 0.483-0.421 0.503-3.11l0.021-2.428 0.772-0.028c0.855-0.028 1.048-0.097 1.462-0.503 0.483-0.49 0.483-0.49 0.483-2.517 0-2.034 0-2.028-0.497-2.524-0.4-0.407-0.71-0.51-1.517-0.51h-0.71v-4.4l0.772-0.028c0.862-0.028 1.055-0.097 1.469-0.503 0.483-0.49 0.483-0.49 0.483-2.517s0-2.028-0.483-2.517c-0.414-0.407-0.607-0.476-1.462-0.503l-0.779-0.028v-2.931l-0.366-0.717c-0.441-0.869-0.966-1.738-2.117-3.51-1.214-1.876-1.338-2.069-1.379-2.069-0.076 0.007-2.366 3.566-3.034 4.724-0.848 1.469-0.828 1.4-0.828 3.069v1.448h-3.31v-2.945l-0.366-0.717c-0.441-0.869-0.966-1.738-2.117-3.51-1.214-1.876-1.338-2.069-1.379-2.069-0.076 0.007-2.366 3.566-3.034 4.724-0.848 1.469-0.828 1.4-0.828 3.069v1.448h-3.31v-2.945l-0.366-0.717c-0.441-0.869-0.966-1.738-2.117-3.51-1.214-1.876-1.338-2.069-1.379-2.069-0.021 0-0.234 0.303-0.476 0.676zM6.959 2.607c0.593 0.89 1.717 2.676 2.041 3.234l0.324 0.566 0.014 12.090c0.014 11.986 0.014 12.090-0.124 12.241l-0.131 0.159h-2.476c-2.303 0-2.476-0.007-2.593-0.124-0.124-0.124-0.131-0.379-0.166-4.434-0.014-2.366-0.028-7.786-0.021-12.034l0.014-7.738 0.297-0.538c0.428-0.779 2.414-3.89 2.483-3.89 0.021 0 0.172 0.214 0.338 0.469zM17.931 2.503c0.628 0.952 1.938 3.028 2.166 3.448l0.262 0.469 0.014 12.083c0.014 11.979 0.014 12.083-0.124 12.234l-0.131 0.159h-2.476c-2.303 0-2.476-0.007-2.593-0.124-0.124-0.124-0.131-0.379-0.166-4.434-0.014-2.366-0.028-7.786-0.021-12.034l0.014-7.738 0.297-0.538c0.428-0.779 2.414-3.89 2.483-3.89 0.021 0 0.145 0.166 0.276 0.366zM28.966 2.503c0.628 0.952 1.938 3.028 2.166 3.448l0.262 0.469 0.014 12.083c0.014 11.979 0.014 12.083-0.124 12.234l-0.131 0.159h-2.476c-2.303 0-2.476-0.007-2.593-0.124-0.124-0.124-0.131-0.379-0.166-4.434-0.014-2.366-0.028-7.786-0.021-12.034l0.014-7.738 0.297-0.538c0.428-0.779 2.414-3.89 2.483-3.89 0.021 0 0.145 0.166 0.276 0.366zM3.241 12.414v1.931h-0.931c-1.303 0-1.207 0.152-1.207-1.931s-0.097-1.931 1.207-1.931h0.931v1.931zM14.276 12.414v1.931h-4.414v-3.862h4.414v1.931zM25.31 12.414v1.931h-4.414v-3.862h4.414v1.931zM34.069 10.621c0.131 0.131 0.138 0.228 0.138 1.772 0 2.097 0.097 1.952-1.29 1.952h-0.986v-3.862h1c0.91 0 1.014 0.014 1.138 0.138zM13.793 17.655v2.207h-3.31v-4.414h3.31v2.207zM24.828 17.655v2.207h-3.31v-4.414h3.31v2.207zM3.241 22.897v1.931h-0.931c-1.303 0-1.207 0.152-1.207-1.931s-0.097-1.931 1.207-1.931h0.931v1.931zM14.276 22.897v1.931h-4.414v-3.862h4.414v1.931zM25.31 22.897v1.931h-4.414v-3.862h4.414v1.931zM34.069 21.103c0.131 0.131 0.138 0.228 0.138 1.793 0 2.090 0.103 1.931-1.276 1.931h-1v-3.862h1c0.91 0 1.014 0.014 1.138 0.138z"></path>
											<path class="path2" d="M6.069 11.738c-0.393 0.269-0.517 0.497-0.517 0.952 0 0.303 0.034 0.455 0.145 0.593 0.483 0.648 1.297 0.662 1.724 0.034 0.228-0.338 0.234-0.924 0.014-1.248-0.324-0.469-0.945-0.614-1.366-0.331z"></path>
											<path class="path3" d="M6.069 22.221c-0.393 0.269-0.517 0.497-0.517 0.952 0 0.303 0.034 0.455 0.145 0.593 0.483 0.648 1.297 0.662 1.724 0.034 0.234-0.345 0.234-0.91 0-1.255-0.317-0.462-0.931-0.607-1.352-0.324z"></path>
											<path class="path4" d="M17.103 11.738c-0.393 0.269-0.517 0.497-0.517 0.952 0 0.303 0.034 0.455 0.145 0.593 0.483 0.648 1.297 0.662 1.724 0.034 0.228-0.338 0.234-0.924 0.014-1.248-0.324-0.469-0.945-0.614-1.366-0.331z"></path>
											<path class="path5" d="M17.103 22.221c-0.393 0.269-0.517 0.497-0.517 0.952 0 0.303 0.034 0.455 0.145 0.593 0.483 0.648 1.297 0.662 1.724 0.034 0.234-0.345 0.234-0.91 0-1.255-0.317-0.462-0.931-0.607-1.352-0.324z"></path>
											<path class="path6" d="M28.138 11.738c-0.393 0.269-0.517 0.497-0.517 0.952 0 0.303 0.034 0.455 0.145 0.593 0.483 0.648 1.297 0.662 1.724 0.034 0.228-0.338 0.234-0.924 0.014-1.248-0.324-0.469-0.945-0.614-1.366-0.331z"></path>
											<path class="path7" d="M28.138 22.221c-0.393 0.269-0.517 0.497-0.517 0.952 0 0.303 0.034 0.455 0.145 0.593 0.483 0.648 1.297 0.662 1.724 0.034 0.234-0.345 0.234-0.91 0-1.255-0.317-0.462-0.931-0.607-1.352-0.324z"></path>
											</symbol>
										</svg></span>';
								break;
							}
						}
						endif;
					?>
					
					<i class="fa fa-bed fa-lg" title="Bedrooms"></i>					
					<span class='bed_bath'><?php echo get_post_meta($p_id,"2_emgtfld_bedrooms",true);?></span>
					&nbsp;&nbsp;
					<!-- <img src="<?php echo REMS_PLUGIN_URL .'/images/bath.png';?>" title="Bathrooms">-->
					<span title="Bathrooms">
					<svg class="icon icon-bath"><use xlink:href="#icon-bath"></use>
						<symbol id="icon-bath" fill="<?php echo $bg_color; ?>" stroke="<?php echo $bg_color; ?>" viewBox="0 0 32 32">
						<title>bath</title>
						<path class="path1" d="M4.725 1.781c-0.625 0.256-1.119 0.756-1.381 1.388-0.119 0.294-0.125 0.487-0.144 6.625l-0.013 6.331-0.319 0.163c-0.413 0.206-0.819 0.625-1.038 1.075-0.156 0.319-0.175 0.419-0.175 1.012 0 0.619 0.012 0.681 0.206 1.075 0.244 0.5 0.738 0.975 1.212 1.175l0.331 0.137 0.906 2.725c0.494 1.506 0.975 2.906 1.069 3.119 0.094 0.206 0.281 0.506 0.425 0.663 0.319 0.344 0.894 0.656 1.3 0.706 0.638 0.075 0.606-0.012 0.319 0.837-0.287 0.863-0.281 1.069 0.019 1.375 0.156 0.156 0.244 0.188 0.481 0.188 0.344 0 0.656-0.169 0.781-0.419 0.050-0.094 0.219-0.575 0.381-1.063l0.3-0.894h13.225l0.325 0.975c0.4 1.194 0.506 1.344 1.019 1.387 0.337 0.025 0.363 0.019 0.569-0.188 0.337-0.337 0.344-0.538 0.050-1.425l-0.256-0.75h0.244c0.356 0 0.994-0.225 1.325-0.469 0.556-0.413 0.656-0.625 1.719-3.831l0.988-2.962 0.387-0.175c0.488-0.219 0.938-0.663 1.181-1.156 0.163-0.344 0.181-0.431 0.181-1.031 0-0.563-0.019-0.7-0.15-0.956-0.238-0.475-0.706-0.95-1.181-1.181l-0.419-0.206-23.781-0.031v-6.113c0-6.013 0-6.106 0.125-6.313 0.256-0.413 0.838-0.481 1.669-0.194 0.375 0.125 1.019 0.512 1.019 0.613 0 0.031-0.044 0.088-0.1 0.138-0.244 0.2-0.287 0.425-0.263 1.206 0.037 0.894 0.188 1.375 0.65 2.019 0.375 0.525 0.587 0.656 0.987 0.625l0.3-0.025 1.775-1.775c1.881-1.881 1.95-1.969 1.831-2.4-0.075-0.269-0.55-0.694-1.081-0.956-0.581-0.288-1.144-0.4-1.856-0.375-0.569 0.019-0.619 0.031-0.85 0.212l-0.238 0.194-0.281-0.244c-0.381-0.325-1.313-0.781-1.844-0.894-0.694-0.144-1.487-0.119-1.931 0.063zM10.537 4.063l0.175 0.069-0.9 0.9c-0.581 0.581-0.919 0.875-0.944 0.831s-0.069-0.213-0.088-0.381l-0.044-0.306 0.581-0.588c0.531-0.531 0.606-0.587 0.819-0.587 0.125 0 0.306 0.031 0.4 0.063zM28.531 17.831c0.381 0.381 0.331 0.962-0.106 1.231-0.206 0.125-0.281 0.125-12.425 0.125s-12.219 0-12.425-0.125c-0.438-0.269-0.487-0.85-0.106-1.231l0.206-0.206h24.65l0.206 0.206zM26.856 20.887c-0.012 0.044-0.4 1.206-0.856 2.581s-0.856 2.544-0.894 2.6c-0.038 0.056-0.144 0.15-0.238 0.206-0.156 0.094-0.969 0.1-8.863 0.1-6.431 0-8.731-0.019-8.844-0.075-0.088-0.038-0.2-0.131-0.25-0.206s-0.463-1.256-0.912-2.631c-0.456-1.369-0.844-2.531-0.856-2.575-0.025-0.056 2.169-0.075 10.856-0.075s10.881 0.019 10.856 0.075z"></path>
						<path class="path2" d="M13.275 4.875c-0.494 0.2-0.594 1.037-0.169 1.375 0.306 0.237 0.938 0.131 1.169-0.2 0.125-0.181 0.125-0.625 0.006-0.862-0.162-0.319-0.637-0.463-1.006-0.313z"></path>
						<path class="path3" d="M11.519 6.563c-0.281 0.169-0.387 0.537-0.262 0.894 0.244 0.712 1.238 0.712 1.488 0 0.081-0.231 0.081-0.281-0.019-0.544-0.075-0.194-0.175-0.319-0.287-0.381-0.256-0.131-0.681-0.119-0.919 0.031z"></path>
						<path class="path4" d="M14.262 6.662c-0.325 0.225-0.419 0.763-0.2 1.119 0.219 0.35 0.75 0.469 1.088 0.25 0.475-0.313 0.463-1.125-0.025-1.375-0.238-0.119-0.681-0.119-0.863 0.006z"></path>
						<path class="path5" d="M10.019 8.119c-0.8 0.5-0.213 1.762 0.662 1.431 0.525-0.2 0.675-0.912 0.275-1.313-0.25-0.25-0.65-0.3-0.938-0.119z"></path>
						<path class="path6" d="M12.625 8.3c-0.637 0.319-0.519 1.244 0.194 1.463 0.194 0.056 0.269 0.050 0.469-0.050 0.338-0.169 0.481-0.394 0.475-0.744 0-0.319-0.144-0.525-0.463-0.694-0.225-0.119-0.412-0.113-0.675 0.025z"></path>
						<path class="path7" d="M15.431 8.419c-0.531 0.325-0.469 1.194 0.1 1.431 0.275 0.113 0.369 0.113 0.637-0.019 0.762-0.363 0.506-1.525-0.338-1.519-0.131 0-0.313 0.050-0.4 0.106z"></path>
						<path class="path8" d="M10.838 9.938c-0.325 0.131-0.519 0.6-0.4 0.956 0.206 0.637 1.119 0.744 1.456 0.169 0.206-0.35 0.069-0.919-0.275-1.094-0.194-0.1-0.575-0.119-0.781-0.031z"></path>
						<path class="path9" d="M13.613 10.1c-0.225 0.119-0.363 0.394-0.363 0.713 0 0.456 0.319 0.75 0.819 0.75 0.206 0 0.294-0.037 0.463-0.206 0.375-0.375 0.369-0.838-0.006-1.162-0.25-0.219-0.613-0.256-0.912-0.094z"></path>
						<path class="path10" d="M16.656 10.125c-0.069 0.025-0.188 0.119-0.269 0.2-0.381 0.412-0.262 1.012 0.25 1.275 0.694 0.356 1.413-0.506 0.969-1.169-0.225-0.331-0.6-0.456-0.95-0.306z"></path>
						<path class="path11" d="M11.581 11.875c-0.681 0.412-0.394 1.438 0.4 1.438 0.344 0 0.613-0.15 0.738-0.412 0.119-0.25 0.119-0.406 0.006-0.675-0.188-0.45-0.713-0.613-1.144-0.35z"></path>
						<path class="path12" d="M14.594 11.925c-0.588 0.369-0.444 1.294 0.231 1.444 0.625 0.137 1.15-0.481 0.906-1.075-0.119-0.275-0.338-0.425-0.688-0.463-0.188-0.019-0.319 0.006-0.45 0.094z"></path>
						<path class="path13" d="M17.813 11.856c-0.194 0.069-0.444 0.363-0.5 0.594-0.181 0.669 0.619 1.238 1.188 0.85 0.313-0.213 0.438-0.581 0.313-0.938-0.144-0.419-0.606-0.65-1-0.506z"></path>
						<path class="path14" d="M12.319 13.787c-0.412 0.344-0.35 1.063 0.119 1.306 0.619 0.319 1.306-0.238 1.125-0.906-0.144-0.537-0.825-0.756-1.244-0.4z"></path>
						<path class="path15" d="M15.65 13.688c-0.519 0.206-0.537 1.144-0.025 1.406 0.581 0.3 1.275-0.194 1.163-0.819-0.031-0.144-0.131-0.319-0.256-0.444-0.175-0.175-0.25-0.206-0.475-0.2-0.15 0-0.338 0.025-0.406 0.056z"></path>
						<path class="path16" d="M18.806 13.719c-0.525 0.287-0.519 1.106 0.012 1.394 0.256 0.137 0.725 0.063 0.944-0.156 0.35-0.35 0.275-1.025-0.144-1.238-0.219-0.119-0.6-0.119-0.813 0z"></path>
						</symbol>
					</svg>
					</span>
					<span class="bed_bath"><?php echo get_post_meta($p_id,"2_emgtfld_bathrooms",true);?></span>
				</div>
			</div>
			<div class="ad_list_content">
				<?php				
				echo wp_trim_words(get_the_content(),30,"....");
			?>
			</div>
			</div>
		<div class="listing_right col-sm-2 col-md-2 col-xs-12 text-center">
			<ul class="no-padding">
				<ul class="no-padding">
				<li><?php _e("Price","estate-emgt"); echo " : <span class='emgt_price'>".emgt_get_currency_symbol(get_option("emgt_system_currency"))." ".@get_post_meta($p_id,"1_emgtfld_price",true);?></span></li>
				<li><?php _e("For","estate-emgt"); echo " : ".@get_post_meta($p_id,"1_emgtfld_for",true);?></li>
				<li><?php _e("Type","estate-emgt"); echo " : ".@get_post_meta($p_id,"1_emgtfld_type",true)?></li>
				<li><br><a href="<?php echo get_permalink();?>" class="view_btn"><?php _e("View","estate-emgt");?></a></li>
			</ul>
		</div>	
		</div>
	<?php		
		// }		
	// }
	endwhile;	
	wp_reset_query();
 else : 
 get_template_part( 'content', 'none' ); 
 endif; 
?>
	</div>	
		<div class="col-sm-3 col-md-3 col-xs-12 margin-box">
		<div class="agents_data">
			<h3 class="h_border"><?php _e("Agents","estate-emgt");?></h3>	
			<div class="agents_profile">
			<ul id="agent_ul">	
			<?php 	
			$args = array( "role" => 'emgt_role_agent');
			$agents = get_users($args);
			if(!empty($agents))
			{
			foreach ($agents as $agent)
			{ 
				$photo = (!empty($agent->user_photo)) ? $agent->user_photo : REMS_PLUGIN_URL."/images/default_user_logo.png";
			?>
				<li><a href="<?php echo esc_url(home_url("/"));?>?view_profile=yes&id=<?php echo $agent->ID;?>"><img src="<?php echo $photo; ?>" alt="dp_img" class="agent_dp" height='80px' width='80px'></img>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $agent->first_name ." ". $agent->last_name; ?></a></li>
		<?php }
			}
			else{
				echo "<li>No agents available !</li>";
			} ?>
			</ul>
				</div>
		</div>	
	</div>
</div>	
<br>
<div class="navigation">
	<div class="alignleft"><?php @previous_posts_link(__('<< Previous Page','estate-emgt'),$max_num_pages) ?></div>
	<div class="alignright"><?php @next_posts_link(__('Next Page >>','estate-emgt'),$max_num_pages) ?></div>
</div>
</div>
<?php 
do_action('emgt_end_wrap');
wp_enqueue_style('wplms-popup-css',REMS_PLUGIN_URL .'/css/frontend_style.css');
get_footer();
?>