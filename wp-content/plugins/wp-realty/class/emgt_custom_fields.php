<?php 
class Emgt_fields{
	
	public $shw=0;
	
	public function emgt_show_sections($option_id = null)
	{
		$option_id  = intval($option_id);
		 $db = new Emgt_db;
			$sections = $db->emgt_get_rows("emgt_sections","option_id",$option_id);

			 foreach($sections as $section)
			{
				echo '<div id="emgt_meta_basic_details" class="postbox true">';	
				echo '<h3><span>'.$section['name'].'</span></h3>'; //h3 style="border-bottom: 1px solid #eee;
				echo '<div class="inside">';				
				$this->emgt_get_fields($section['section_id']);				
				echo '</div>';
				echo '</div>';
			}		
	}
		
	private function emgt_get_fields($section_id)
	{
		global $post;
		$section_id  = intval($section_id);
		$post_data = get_post_meta($post->ID);		
		$db = new Emgt_db;
		// $fields = $db->emgt_db_get_fields_by_section_id($section_id);
		$fields = get_option("field_order_{$section_id}");
		$fields = explode(",",$fields);
		
	if(!empty($fields[0]))
	{
		foreach($fields as $field_id)
		{
			$field = $db->emgt_get_rows("emgt_fields","field_id",$field_id);
			
			$field = $field[0];			
			
			if(!$field['disable'])	
			{				
				// echo "<table class='table'>";s
				// echo "<tr style='border-bottom: 1px solid #eee;'>";
				// echo "<td><label>{$field['field_label']}</label> : </td>";
				echo "<div 	style='margin-bottom : 20px;border-top: 1px solid #eee;'><div><label style='font-style: italic;line-height: 2em;font-weight: 600;font-size: small;color:gray;'>{$field['field_label']}</label></div>";
				$fldname = $section_id."_emgtfld_".$field['field_name'];
				// $class = $field['class'];
				if($field['required_field'] == 1)
				{
					$field['class'] = $field['class'] ." validate[required]";
				}
				switch($field['field_type'])
				{
					CASE "Textarea" :
						echo "<div><textarea name='{$fldname}' cols='30' id='{$field['id']}' class='{$field['class']}' placeholder='".($field['placeholder'] ? $field['default_value'] : '' )."'>".((isset($post_data)&&!empty($post_data[$fldname])) ? $post_data[$fldname][0] : '')."</textarea></div></div>";
					break;
					
					CASE "Dropdown" :
						
						if($fldname == "1_emgtfld_country")
						{
							$url = plugins_url( 'countrylist.xml', __FILE__ );						
							if(get_remote_file($url))
							{
								$xml =simplexml_load_string(get_remote_file($url));					
							}
							else 
							{ die("Error: Cannot create object");	}
							global $post;				
							?>
								<select style="width:auto" name="1_emgtfld_country" class="<?php echo $field['class']?>">
									<option value=""><?php _e(" -- Select Country -- ","estate-emgt");?></option>
									<?php
										foreach($xml as $country)
										{ ?>
										 <option value="<?php echo $country->code;?>" <?php echo (isset($post_data[$fldname][0]) && $post_data[$fldname][0] == $country->code) ? "selected" : ""; ?>><?php echo $country->name;?></option>
									<?php } ?>				
								</select>
							<?php
							echo '</div>';
							
						}
						else{						
							$options = explode("|",$field['options']);
							echo "<div><select name='{$fldname}' id='{$field['id']}' class='{$field['class']}'>";
							foreach($options as $option)
							{
								
								echo "<option value='{$option}' ".((isset($post_data) && $post_data[$fldname][0] == $option) ? 'selected' : '').">{$option}</option>";
							}
							echo "</select></div></div>";
						}
					break;
					
					CASE "Radio-Buttons" :				
@						$val = unserialize($post_data[$fldname][0])[0];												
						$options = explode("|",$field['options']);
						echo "<div>&nbsp;";
						foreach($options as $option)
						{
							echo "<input type='radio' name='{$fldname}[]' id='{$field['id']}' class='{$field['class']}' value='{$option}' ".((isset($post_data) && $option == $val) ? 'checked' : '').">{$option}&nbsp;&nbsp;&nbsp;";
						}
						echo "</div></div>";					
					break;
					
					CASE "Textbox" :
						echo "<div><input type='text' name='{$fldname}' id='{$field['id']}' class='{$field['class']}' placeholder='".($field['placeholder'] ? $field['default_value'] : '' )."' value='".((isset($post_data)&&!empty($post_data[$fldname])) ? $post_data[$fldname][0] : '')."'></div></div>";
					break;
					
					CASE "Checkbox" :
						
						$val = (isset($post_data) && !empty($post_data[$fldname][0])) ? unserialize($post_data[$fldname][0]) : array() ;						
						$options = explode("|",$field['options']);
						echo "<div>";						
						foreach($options as $option)
						{
							echo "<input type='checkbox' name='{$fldname}[]' id='{$field['id']}' class='{$field['class']}' value='{$option}' ".((isset($post_data) && in_array($option,$val)) ? 'checked' : '').">{$option} &nbsp;&nbsp;&nbsp;";
						}
						echo "</div></div>";
					break;
				}
				
				// echo "</tr>";
				// echo "</table>";
			}
			
		} 
	 }	
	 else { echo "<span style='color:gray'>&nbsp;&nbsp;&nbsp;* Please Add fields from <q>Manage Fields</q> page.</span>";}
	}	
	
	public function emgt_save_posts($key,$value)
	{
		global $post;		
		update_post_meta($post->ID,$key,$value);	
	}	
	
	public function check_plan_features($plan){
		
		global $wpdb;
		$tbl = $wpdb->prefix."emgt_plans";
		$result = $wpdb->get_results("SELECT * FROM {$tbl} WHERE id = {$plan}",ARRAY_A);		
		$plans = explode(",",$result['features']);	
	}	
	
	public function get_section_for_frontend_single_view_page($option_id = null)
	{
		$db = new Emgt_db;
		$sections = $db->emgt_get_rows("emgt_sections","option_id",$option_id);		
		$section_a = array();
		$chk_a = array();
		foreach($sections as $section)
		{  	
			$section_a[$section['section_id']] = $this->get_fields_front_end($section['section_id']);
			if(is_array($section_a[$section['section_id']]))
			{
				if(array_key_exists("checkbox", $section_a[$section['section_id']]))
				{
					$chk_a = $section_a[$section['section_id']]['checkbox'];
				}
			}
		}
		
		$ss =  array_merge($section_a,$chk_a);
		
		return $section_a;	
	}	
	
	
	public function emgt_get_section_only($option_id = null)
	{
		$option_id = intval($option_id);
		$db = new Emgt_db;
		$sections = $db->emgt_get_rows("emgt_sections","option_id",$option_id);
		$fields = array();
		 foreach($sections as $section)
		{						
			$fld = $this->emgt_get_fields_only($section['section_id']);	
			if(!empty($fld))
			{
				$fields[$section['name']] = $fld;
			}		
		}		
		return $fields;
	}
	
	public function emgt_get_fields_only($sec_id)
	{
		global $post;
		$sec_id = intval($sec_id);
		$print_fields = array();
		$post_data = get_post_meta($post->ID);		
		$db = new Emgt_db;	
		$fields = get_option("field_order_{$sec_id}");
		$fields = explode(",",$fields);
		if(!empty($fields[0]))
		{
			foreach($fields as $field_id)
			{
				$field = $db->emgt_get_rows("emgt_fields","field_id",$field_id);				
				$field = $field[0];							
				if(!$field['disable'])	
				{	
					
					if(get_post_meta($post->ID,"{$sec_id}_emgtfld_{$field['field_name']}",true))
					{					
						if($field['field_type'] == "Checkbox")
						{
							$print_fields[$field['field_label']]['checkbox']=get_post_meta($post->ID,"{$sec_id}_emgtfld_{$field['field_name']}",true);
						}
						else
						{
							$print_fields[$field['field_label']]=get_post_meta($post->ID,"{$sec_id}_emgtfld_{$field['field_name']}",true);
						}
					}
				}
			}
		}		
		return $print_fields;
	}
	
	public function emgt_is_field_disable($fld_name)
	{	
		global $wpdb;
		$tbl = $wpdb->prefix ."emgt_fields";
		$data = $wpdb->get_row("SELECT disable from {$tbl} WHERE field_name = '{$fld_name}'",ARRAY_A);		
		if($data['disable'])
		{
			return true;
		}else{
			return false;
		}		
	}	
}