<?php 
$db = new Emgt_Db;

if(isset($_REQUEST['save_field']) || isset($_REQUEST['update_field']))
{	
	if(!empty($_REQUEST['field_label']))
	{	
		$table_name = "emgt_fields";
		$type = $_REQUEST['f_type'];
		$name = ucwords(trim($_REQUEST['field_name']));	
		$name = str_replace(" ","_",$name);
		$name = strtolower($name);
		$label = ucwords(trim($_REQUEST['field_label']));
		$section_id = $_REQUEST['section_id'];
		// $option_id = $_REQUEST['$option_id'];
		$f_id = trim($_REQUEST['f_id']);
		$f_id = str_replace(" ","_",$f_id);
		$f_class = trim($_REQUEST['f_class']);
		@$disable = ($_REQUEST['dis_type'] === '1') ? 1 : 0 ;
		@$placeholder = ($_REQUEST['placeholder'] === '1') ? 1 : 0 ;
		@$req_field = ($_REQUEST['req_type'] === '1') ? 1 : 0;	
		@$d_value = (!empty(trim($_REQUEST['d_value']))) ? trim($_REQUEST['d_value']) : NULL;		
		@$options = (empty(trim($_REQUEST['options']))) ? $_REQUEST['buttons'] : trim($_REQUEST['options']);
		@$options = (!empty($options)) ? $options : NULL ;
		//$options = (!empty(trim($_REQUEST['options']))) ?  trim($_REQUEST['options']) : NULL ;
		
		$data = array(
				'field_type'=>$type,
				'section_id'=>$section_id,
				// 'option_id'=>$option_id,
				'required_field'=>$req_field,
				'field_label'=>$label,
				'field_name'=>$name,
				'default_value'=>$d_value,
				'placeholder'=>$placeholder,
				'options'=>$options,
				'id'=>$f_id,
				'class'=>$f_class,
				'disable'=>$disable);
		if(isset($_REQUEST['update_field']))
		{			
			$update_id = $_REQUEST['field_id'];			
			$chk = $db->emgt_db_update('emgt_fields',$data,array("field_id"=>$update_id));
			if($chk)
			{
				$success = 1;
			}
			else{
				$success = 2;
			}
		}
		else
		{	
			$chk = $db::emgt_check_unique_field($table_name,"field_name",$name);	
			if($chk)
			{
				$insert_id = $db->emgt_insert($table_name,$data);			
				if($insert_id)
				{			
					$option = get_option("field_order_{$section_id}");
					if(empty($option))
					{
						$option = $insert_id;
					}
					else{
						$option .= ','.$insert_id;
					}				
					update_option("field_order_{$section_id}",$option);
					$success = 1;
				}else
				{
					$success = 2;
				}
			}
			else{
				$success = 0;
			}
		}	
	}else{
		// $success = 0;
	}	
}

if(isset($_REQUEST['did']) && $_REQUEST['did'] != '')
{
	$chk = $db->emgt_delete_record('emgt_fields','field_id',$_REQUEST['did']);	
	if($chk == true)
	{
		$success = 3 ;
		$section_id = $_REQUEST['s_id'];
		$option = get_option("field_order_{$section_id}");
		$option = str_replace($_REQUEST['did'],'',$option);
		$option = str_replace(',,',',',$option);
		$option = trim($option,',');
		update_option("field_order_{$section_id}",$option);	 	
	}	
}

?>

<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
    <div class="modal-content">
    <div class="result">
    </div>
	
    </div>
    </div> 
    
</div>


<div class="page-inner" style="min-height:1631px !important">
	<div class="page-title">
		<h3><img src="<?php echo get_option( 'emgt_system_logo' ) ?>" class="img-circle head_logo" width="40" height="40" /><?php  echo get_option( 'emgt_system_name' );?></h3>
	</div>
<div  id="main-wrapper" class="class_list">
	<?php if(isset($success)) : 
		switch($success)
		{
			CASE 0:?>
					<div class="alert alert-danger alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-warning"></i>&nbsp;<?php _e('Error !','estate-emgt');?></strong> <?php _e('Field Name already exists.Please Choose different field name.','estate-emgt');?>
					</div>					
			<?php	
			break;
			CASE 1:?>
					<div class="alert alert-success alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-check-circle"></i>&nbsp;<?php _e('Success !','estate-emgt');?></strong> <?php _e('Field Saved Successfully.','estate-emgt');?>
					</div>					
			<?php	
			break;
			CASE 2:?>
					<div class="alert alert-danger fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-info-circle"></i>&nbsp;<?php _e('Error !','estate-emgt');?></strong> <?php _e('Field cannot be Saved, Please try again later','estate-emgt');?>
					</div>					
			<?php	
			break;
			CASE 3:?>
					<div class="alert alert-success alert-success fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					  </button>
					  <strong><i class="fa fa-check-circle"></i>&nbsp;<?php _e('Success !','estate-emgt');?></strong> <?php _e('Field Deleted Successfully.','estate-emgt');?>
					</div>					
			<?php	
			break;
		}
	
	?>
	
<?php endif;?>

	<div class="panel panel-white">
	 <div class="panel-body">		
	<h3>	
		<ul class="nav nav-tabs" role="tablist">	 
		  <li class="nav-item active">
			<a class="nav-link " data-toggle="tab" href="#manage_field" role="tab"><i class="fa fa-cogs"></i> <?php _e('Manage Fields','estate-emgt');?></a>
		  </li>	
		</ul>
	</h3>
	<!-- Tab panes -->
	<script>		
	$(function() {
				$('#fields_dropper').sortable();
    });
	</script>
 
 <?php 
	$option_id = (isset($_REQUEST['o_id']))?$_REQUEST['o_id']: '1';	 
 ?>
 
 
<div class="tab-content">	 
 <div class="tab-pane active" id="manage_field" role="tabpanel">
 <hr />
	<div class="row">	
		<div class="col-sm-12">
		<h3>	
			<ul class="nav nav-pills nav-justified" id="top_menu_bar">
			  <li role="presentation" class="<?php echo ($option_id == 1)?'active':"";?>" id="1">
				<a href="?page=emgt_fields&o_id=1"><?php _e("Basic Details","estate-emgt")?></a>
			  </li>
			  <li role="presentation" class="<?php echo ($option_id == 2)?'active':"";?>" id="2">
				<a href="?page=emgt_fields&o_id=2"><?php _e("Advance","estate-emgt")?></a>
			  </li>
			  <li role="presentation" class="<?php echo ($option_id == 3)?'active':"";?>" id="3">
				<a href="?page=emgt_fields&o_id=3"><?php _e("Optional","estate-emgt")?></a>
			  </li>
			  <li role="presentation" class="<?php echo ($option_id == 4)?'active':"";?>" id="4">
				<a href="?page=emgt_fields&o_id=4" ><?php _e("Media","estate-emgt")?></a>
			  </li>
			  <li role="presentation" class="<?php echo ($option_id == 5)?'active':"";?>" id="5">
				<a href="?page=emgt_fields&o_id=5"><?php _e("Compliance","estate-emgt")?></a>
			  </li>
			</ul>
		</h3>
		</div>
	</div>
	<hr />	
	
  <div class="row">
	<?php include_once REMS_PLUGIN_DIR."/admin/fields/section_page.php"; ?>
		  
 <div class="col-md-3">
  <?php include_once REMS_PLUGIN_DIR."/admin/fields/add_field.php"; ?>
 </div>
  </div>
 </div>
</div> <!-- END tab-content -->

</div> <!-- End Panel-Body -->
<div class="ajax-ani"></div>
<div class="ajax-img"><img src="<?php echo plugins_url('Real_Estate_Management/images/ajax-loader2.gif');?>"></div>
</div> <!-- End Panel-white  -->
</div> <!-- END main-wrapper -->
</div> <!-- END page-inner -->
<script>

jQuery(document).ready(function($) {
	$('.rems_menu').click(function(){
		$('.rems_menu').removeClass("active");
		$(this).addClass("active");		
	});		
});
</script>