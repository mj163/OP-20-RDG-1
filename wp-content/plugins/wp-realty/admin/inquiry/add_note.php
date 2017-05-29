<?php
if($edit)
{
/*  $inq = $db->emgt_get_rows("emgt_inquiry","id",$edit_id);
	$inq = $inq[0]; //getting only single row */
}

?>
<script type="text/javascript">
$(document).ready(function() {
	$("#note_frm").validationEngine();
	$('#task_date').datepicker({
			  changeMonth: true,
			  changeYear: true,
			  dateFormat: "yy-mm-dd",
			  yearRange:'-65:+0',
			  onChangeMonthYear: function(year, month, inst) {
					$(this).val(month + "/" + year);
			  }
                    
         });	
});
</script>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
    <div class="modal-content">
    <div class="result">
    </div>
	
    </div>
    </div>     
</div>
<form method="post" id="note_frm" class="form-horizontal">	
<fieldset>
	<legend><?php _e("Note Details","estate-emgt");?></legend>
	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="title"><?php _e('Note Title','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-4">
			<input type="text" name="title" id="title" class="form-control validate[required]" value="" />
			</div>
	</div>	
	<div class="form-group">
			<label class="col-sm-2 control-label text-right" for="note"><?php _e('Note Description','estate-emgt');?> <span class="require-field">*</span></label>
			<div class="col-sm-8">
			<textarea name="note" id="note" class="form-control validate[required]" value=""></textarea>
			</div>
	</div>			
	</fieldset>
	<input type="hidden" name="inq_id" value="<?php echo $_GET['vid']; ?>">
	<div class="col-sm-offset-2 col-sm-8">        	
		<input type="submit" value="<?php _e('Save Note', 'estate-emgt' ); ?>" name="add_note" class="btn btn-success"/>
	</div>
</form>
<br><br><br><br>
<legend><?php _e("Assigned Tasks","estate-emgt"); ?></legend>
<script type="text/javascript">
$(document).ready(function() {
	jQuery('#tasks').DataTable({
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},	
					  {"bSortable": true},					  
	                  {"bSortable": false}]});
} );
</script>   
<table id="tasks" class="display" cellspacing="0" width="100%">
	<thead>
		<th><?php _e("Created Date","estate-emgt");?></th>
		<th><?php _e("Note Title","estate-emgt");?></th>		
		<th><?php _e("Note Description","estate-emgt");?></th>		
		<th><?php _e("Action","estate-emgt");?></th>
	</thead>
	<tfoot>
		<th><?php _e("Created Date","estate-emgt");?></th>
		<th><?php _e("Note Title","estate-emgt");?></th>		
		<th><?php _e("Note Description","estate-emgt");?></th>		
		<th><?php _e("Action","estate-emgt");?></th>
	</tfoot>
	<tbody>
	<?php 
		$notes = $db->emgt_get_rows("emgt_notes","inquiry_id",$edit_id);	
		foreach($notes as $note)
		{ 
			echo "<tr>";
			echo "<td>{$note['created_date']}</td><td>{$note['title']}</td><td>{$note['description']}</td>
				<td>
				<button type='button' class='btn btn-primary' n_id='{$note['id']}' id='view_note'><i class='fa fa-eye'></i></button>&nbsp;
				<a href='?page=emgt_inquiries&tab=add_note&vid={$edit_id}&ndid={$note['id']}' class='btn btn-danger' onclick=\"return confirm('Are you sure you want to delete this record ?');\"><i class='fa fa-remove'></i></a></td>";
			echo "</tr>";
		}
	?>
	</tbody>
</table>
