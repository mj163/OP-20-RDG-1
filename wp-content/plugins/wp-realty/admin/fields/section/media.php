<?php 
	$active_tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : 'fieldlist';
	$sid = (isset($_REQUEST['s_id'])) ? $_REQUEST['s_id'] : '' ;
	?>
  <div class="row">
   <div class="col-md-2">
   <ul class="nav nav-pills nav-stacked border" id="left_section_menu">
    <li class="rems_title"><?php _e("Section List","estate-emgt"); ?></li>
   <?php 
	$i = 1;
	$sections  = $db->emgt_get_rows("emgt_sections","option_id",4);
	if(!empty($sections))
	{		
		foreach($sections as $section)
		{?>
		   <li role="presentation" class="rems_menu rems_menu_link <?php echo ($sid == $section['section_id'])?'active':'';?>" id="<?php echo $section['section_id'];?>"><a href="?page=emgt_fields&tab=fieldlist&s_id=<?php echo $section['section_id'];?>" id="<?php echo $section['section_id']; ?>" ><?php echo $section['name'];?></a></li>
<?php  $i++; }
	}
   ?>
   </ul>
 
 <?php 
	if($sid == '')
	{ 
		$active_tab ='fieldlist';  ?>
		   <script>
				$('#1').addClass("active");				
		   </script>
<?php 
	} ?>

   </div>
  
	<div class="col-md-7">
	<?php 
	if($active_tab == 'fieldlist')
	{
		if(!isset($_REQUEST['s_id'])) // get 1st section_id from list and see field_list when page visited 1st time.
		{?>
			<script>
				var s_id = $("#left_section_menu li:nth-child(2)").attr("id");				
				window.location.href = "?page=emgt_fields&s_id="+s_id+"&tab=fieldlist";
			</script>
<?php   }else
		{
			include_once REMS_PLUGIN_DIR."/admin/fields/field_list.php";
		}
		
	}
	?>
		
	</div>
	  
 <div class="col-md-3">
  <?php include_once REMS_PLUGIN_DIR."/admin/fields/add_field.php"; ?>
 </div>
  </div>


