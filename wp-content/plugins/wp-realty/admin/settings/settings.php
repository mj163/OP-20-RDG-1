<?php
if(isset($_POST['save_setting']))
{
	update_option("emgt_system_name",$_POST['emgt_name']);
	update_option("emgt_system_address",$_POST['emgt_address']);
	update_option("emgt_system_phone",$_POST['emgt_phone']);
	update_option("emgt_system_country",$_POST['emgt_contry']);
	update_option("emgt_system_currency",$_POST['emgt_currency']);
	update_option("emgt_system_email",$_POST['emgt_email']);
	update_option("emgt_system_logo",$_POST['emgt_logo']);
	update_option("emgt_system_front_color","#".$_POST['front_color']);
	update_option("emgt_system_front_text_color","#".$_POST['front_text_color']);
	
	if(isset($_POST['p_approve']))
	{
		if($_POST['p_approve'] == "1")
		{
			update_option("emgt_system_property_approval",1);
		}
	}else{
		update_option("emgt_system_property_approval",0);
	}	
	$property_list_page_id = $_POST['property_list_page'];
	update_option("emgt_property_list_page",$property_list_page_id);
}


?>
<script>
$(document).ready(function(){
$("#sfrm").validationEngine();
});
</script>

<div class="page-inner" style="min-height:1631px !important">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo get_option( 'emgt_system_name' );?></h3>
	</div>
	<div id="main-wrapper" class="class_list">
	<div class="panel panel-white">	
	<div class="panel-body">	
	<form method="post" class="form-horizontal" id="sfrm">
		<fieldset>
		<legend> <?php _e("General Settings","estate-emgt");?> </legend>	
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="emgt_name"><?php _e('System Name','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="emgt_name" class="validate[required] form-control" value="<?php  echo get_option( 'emgt_system_name' ); ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="emgt_address"><?php _e('Address','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="emgt_address" class="validate[required] form-control" value="<?php  echo get_option( 'emgt_system_address' ); ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="emgt_phone"><?php _e('Phone','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" name="emgt_phone" class="validate[required] form-control" value="<?php  echo get_option( 'emgt_system_phone' ); ?>" />
			</div>
		</div>
		<div class="form-group" class="form-control" id="">
			<label class="col-sm-2 control-label" for="emgt_contry"><?php _e('Country','estate-emgt');?></label>
			<div class="col-sm-8">			
			<?php 
			$url = REMS_PLUGIN_URL.'/lib/countrylist.xml';		
			if(get_remote_file($url))
			{
				$xml =simplexml_load_string(get_remote_file($url));
			}
			else 
				die("Error: Cannot create object");
		
			?>
			 <select name="emgt_contry" class="form-control validate[required]" id="emgt_contry">
				<option value=""><?php _e('Select Country','estate-emgt');?></option>
				<?php
					foreach($xml as $country)
					{  
					?>
					 <option value="<?php echo $country->name;?>" <?php selected(get_option( 'emgt_system_country' ), $country->name);  ?>><?php echo $country->name;?></option>
				<?php }?>
             </select> 
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="emgt_currency"><?php _e('Select Currency','estate-emgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
			
		<select name="emgt_currency" class="form-control validate[required] text-input">
		  <option value=""> <?php _e('Select Currency','estate-emgt');?></option>
		  <option value="AUD" <?php echo selected(get_option( 'emgt_system_currency' ),'AUD');?>>
		  <?php _e('Australian Dollar','estate-emgt');?></option>
		  <option value="BRL" <?php echo selected(get_option( 'emgt_system_currency' ),'BRL');?>>
		  <?php _e('Brazilian Real','estate-emgt');?> </option>
		  <option value="CAD" <?php echo selected(get_option( 'emgt_system_currency' ),'CAD');?>>
		  <?php _e('Canadian Dollar','estate-emgt');?></option>
		  <option value="CZK" <?php echo selected(get_option( 'emgt_system_currency' ),'CZK');?>>
		  <?php _e('Czech Koruna','estate-emgt');?></option>
		  <option value="DKK" <?php echo selected(get_option( 'emgt_system_currency' ),'DKK');?>>
		  <?php _e('Danish Krone','estate-emgt');?></option>
		  <option value="EUR" <?php echo selected(get_option( 'emgt_system_currency' ),'EUR');?>>
		  <?php _e('Euro','estate-emgt');?></option>
		  <option value="HKD" <?php echo selected(get_option( 'emgt_system_currency' ),'HKD');?>>
		  <?php _e('Hong Kong Dollar','estate-emgt');?></option>
		  <option value="HUF" <?php echo selected(get_option( 'emgt_system_currency' ),'HUF');?>>
		  <?php _e('Hungarian Forint','estate-emgt');?> </option>
		  <option value="ILS" <?php echo selected(get_option( 'emgt_system_currency' ),'ILS');?>>
		  <?php _e('Israeli New Sheqel','estate-emgt');?></option>
		  <option value="JPY" <?php echo selected(get_option( 'emgt_system_currency' ),'JPY');?>>
		  <?php _e('Japanese Yen','estate-emgt');?></option>
		  <option value="MYR" <?php echo selected(get_option( 'emgt_system_currency' ),'MYR');?>>
		  <?php _e('Malaysian Ringgit','estate-emgt');?></option>
		  <option value="MXN" <?php echo selected(get_option( 'emgt_system_currency' ),'MXN');?>>
		  <?php _e('Mexican Peso','estate-emgt');?></option>
		  <option value="NOK" <?php echo selected(get_option( 'emgt_system_currency' ),'NOK');?>>
		  <?php _e('Norwegian Krone','estate-emgt');?></option>
		  <option value="NZD" <?php echo selected(get_option( 'emgt_system_currency' ),'NZD');?>>
		  <?php _e('New Zealand Dollar','estate-emgt');?></option>
		  <option value="PHP" <?php echo selected(get_option( 'emgt_system_currency' ),'PHP');?>>
		  <?php _e('Philippine Peso','estate-emgt');?></option>
		  <option value="PLN" <?php echo selected(get_option( 'emgt_system_currency' ),'PLN');?>>
		  <?php _e('Polish Zloty','estate-emgt');?></option>
		  <option value="GBP" <?php echo selected(get_option( 'emgt_system_currency' ),'GBP');?>>
		  <?php _e('Pound Sterling','estate-emgt');?></option>
		  <option value="SGD" <?php echo selected(get_option( 'emgt_system_currency' ),'SGD');?>>
		  <?php _e('Singapore Dollar','estate-emgt');?></option>
		  <option value="SEK" <?php echo selected(get_option( 'emgt_system_currency' ),'SEK');?>>
		  <?php _e('Swedish Krona','estate-emgt');?></option>
		  <option value="CHF" <?php echo selected(get_option( 'emgt_system_currency' ),'CHF');?>>
		  <?php _e('Swiss Franc','estate-emgt');?></option>
		  <option value="TWD" <?php echo selected(get_option( 'emgt_system_currency' ),'TWD');?>>
		  <?php _e('Taiwan New Dollar','estate-emgt');?></option>
		  <option value="THB" <?php echo selected(get_option( 'emgt_system_currency' ),'THB');?>>
		  <?php _e('Thai Baht','estate-emgt');?></option>
		  <option value="TRY" <?php echo selected(get_option( 'emgt_system_currency' ),'TRY');?>>
		  <?php _e('Turkish Lira','estate-emgt');?></option>
		  <option value="USD" <?php echo selected(get_option( 'emgt_system_currency' ),'USD');?>>
		  <?php _e('U.S. Dollar','estate-emgt');?></option>
		</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="emgt_email"><?php _e('Email Id','estate-emgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
				<input id="emgt_email" class="form-control validate[required,custom[email]] text-input" type="text" value="<?php echo get_option( 'emgt_system_email' );?>"  name="emgt_email">
			</div>
		</div>		
		<div class="form-group">
			<label class="col-sm-2 control-label" for="upload_user_avatar_button"><?php _e('System Logo','estate-emgt');?><span class="require-field">*</span></label>
			<div class="col-sm-8">
			<input type="text" id="emgt_user_avatar_url" name="emgt_logo" class="validate[required]" value="<?php  echo get_option( 'emgt_system_logo' ); ?>" />
       				 <input id="upload_user_avatar_button" type="button" class="button" value="<?php _e( 'Upload image', 'estate-emgt' ); ?>" />
       				 <span class="description"><?php _e('Upload image.', 'estate-emgt' ); ?></span>                     
                     <div id="upload_user_avatar_preview" style="min-height: 100px;">
			<img style="max-width:100%;" src="<?php  echo get_option('emgt_system_logo'); ?>" />			
			</div>
		</div>
		</div>		
    </fieldset>
	<fieldset>
		<legend><?php _e('System Settings', 'estate-emgt' ); ?></legend>
		<div class="form-group">					
			<label class="checkbox col-sm-2 text-right"><?php _e("Property Approval System","estate-emgt");?></label>			
			<div class="col-sm-8">
				<input type="checkbox" name="p_approve" value="1" <?php echo (get_option("emgt_system_property_approval") == 1) ? "checked" : "" ;?> >&nbsp;&nbsp;
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="emgt_email"><?php _e('Property List Page','estate-emgt');?></label>
			<div class="col-sm-8">				
					<?php $args = array(
										'sort_order' => 'asc',
										'sort_column' => 'post_title',
										'hierarchical' => 1,
										'exclude' => '',
										'include' => '',
										'meta_key' => '',
										'meta_value' => '',
										'authors' => '',
										'child_of' => 0,
										'parent' => -1,
										'exclude_tree' => '',
										'number' => '',
										'offset' => 0,
										'post_type' => 'page',
										'post_status' => 'publish'
									); 
									$pages = get_pages($args);							
					?>
				<select name="property_list_page">
					<option value=""><?php _e('Select Page','estate-emgt');?></option>
					<option value=""><?php _e('None','estate-emgt');?></option>
					<?php  foreach($pages as $page)
				{?>
						<option value="<?php echo $page->ID;?>" <?php selected(get_option("emgt_property_list_page"),$page->ID);?>><?php echo $page->post_title;?></option>
				<?php } ?>
				</select>
			</div>
		</div>
		</fieldset>
		<fieldset>
		<legend><?php _e('Front-end Settings', 'estate-emgt' ); ?></legend>
		<div class="form-group">					
			<label class="checkbox col-sm-2 text-right"><?php _e("Change Default Color","estate-emgt");?></label>			
			<div class="col-sm-5">
				 <input name="front_color" id="bg_color" class="jscolor form-control" value="<?php echo get_option("emgt_system_front_color");?>">
			</div>
			<div class="col-sm-5">
				<a href="javascript:void(0)" class="btn btn-default" onclick="document.getElementById('bg_color').jscolor.show()">Show Picker</a>
			</div>
			
		</div>	
		<div class="form-group">					
			<label class="checkbox col-sm-2 text-right"><?php _e("Change Text Color","estate-emgt");?></label>			
			<div class="col-sm-5">
				 <input name="front_text_color" id="txt_color" class="jscolor form-control" value="<?php echo get_option("emgt_system_front_text_color");?>">
			</div>
			<div class="col-sm-5">
				<a href="javascript:void(0)" class="btn btn-default" onclick="document.getElementById('txt_color').jscolor.show()">Show Picker</a>
			</div>
		</div>
	</fieldset>
	<hr />
	<div class="col-sm-offset-2 col-sm-8">        	
        	<input type="submit" value="<?php _e('Save', 'estate-emgt' ); ?>" name="save_setting" class="btn btn-success"/>
    </div>
	</form>	
	</div>
	</div>
	</div>
</div> <!-- END of Page-inner -->		