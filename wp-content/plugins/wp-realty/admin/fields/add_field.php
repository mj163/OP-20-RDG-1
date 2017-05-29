<form method="post">
	<table class="table table-striped field_table">
		<thead>
			<tr class="thead-inverse rems_title">
				<th colspan="0"><?php _e('Add New Field','estate-emgt');?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="left"><?php _e('Field Type','estate-emgt');?>
					<select id="field_type" name="field_type">				
					<option value="Textbox" id="Textbox"><?php _e('Textbox','estate-emgt');?></option>
					<option value="Radio-Buttons" id="Radio-Buttons"><?php _e('Radio-Buttons','estate-emgt');?></option>
					<option value="Checkbox" id="Checkbox"><?php _e('Checkbox','estate-emgt');?></option>
					<option value="Textarea" id="Textarea"><?php _e('Textarea','estate-emgt');?></option>
					<option value="Dropdown" id="Dropdown"><?php _e('Dropdown','estate-emgt');?></option>
					<option value="section" id="Section"><?php _e('Add new Section','estate-emgt');?></option>
					</select>					
				</td>
			</tr>
			<tr>
				<td>
					<button type="button" class="btn btn-primary" id="add" data-toggle="modal" data-target="#myModal">
					<?php _e('Add','estate-emgt');?>
					</button> 
				</td>
			</tr>
		</tbody>
	</table>
</form>