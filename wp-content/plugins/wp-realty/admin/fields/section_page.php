<div id="page-load-ani">
	<img src="<?php echo REMS_PLUGIN_URL.'/images/ajax-loader1.gif';?>">
</div>
<script>
	$("#page-load-ani").css('display','block');	
</script>
 <?php 

	$option_id = (isset($_REQUEST['o_id']))?$_REQUEST['o_id']: '1';	 
	$active_tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : '';
	$sid = (isset($_REQUEST['s_id'])) ? $_REQUEST['s_id'] : '' ;
	?>
   <div class="col-md-2" id="left-sec-menu" style="visibility:hidden;">
   <ul class="nav nav-pills nav-stacked border" id="left_section_menu">
    <li class="rems_title"><?php _e("Section List","estate-emgt");?></li>
   <?php 
	$i = 1;
	$sections  = $db->emgt_get_rows("emgt_sections","option_id",$option_id);
	if(!empty($sections))
	{		
		foreach($sections as $section)
		{?>
		   <li role="presentation" class="rems_menu rems_menu_link <?php echo ($sid == $section['section_id'])?'active':'';?>" id="<?php echo $section['section_id'];?>">
		   <a href="?page=emgt_fields&o_id=<?php echo $option_id;?>&s_id=<?php echo $section['section_id'];?>" id="<?php echo $section['section_id']; ?>">
		   <?php echo $section['name'];?>
		   </a>
		   </li>
<?php  $i++; }
	}
   ?>
   </ul>
 <?php 
	if($sid == '')
	{?>
	   <script>			
		$('#1').addClass("active");				
	   </script>
<?php 
	} ?>

   </div>
  
	<div class="col-md-7">
	<?php 
		if(!isset($_REQUEST['s_id'])) // get 1st section_id from list and see field_list when page visited 1st time.
		{ ?>
			<script>
				var s_id = $("#left_section_menu li:nth-child(2)").attr("id");				
				window.location.href = "?page=emgt_fields&s_id="+s_id+"&o_id="+<?php echo $option_id; ?>;
			</script>
<?php  }else
		{ ?>		
<?php		require_once REMS_PLUGIN_DIR."/admin/fields/field_list.php";
		} ?>		
	</div>
