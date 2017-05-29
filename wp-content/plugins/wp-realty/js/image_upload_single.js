jQuery(document).ready(function($) {
	
	var file_frame;	

	 // jQuery('#upload_user_avatar_button').live('click', function( event ){
	$("body").on("click","#upload_user_avatar_button",function(event){
		    event.preventDefault();
		    // If the media frame already exists, reopen it.
		    if ( file_frame ) {
		      file_frame.open();
		      return;
		    }

		    // Create the media frame.
		    file_frame = wp.media.frames.file_frame = wp.media({
		      title: jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: false  // Set to true to allow multiple files to be selected
		    });

		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() {
		      // We set multiple to false so only get one image from the uploader
		      attachment = file_frame.state().get('selection').first().toJSON();
		     // alert(attachment.url);
		      jQuery("#emgt_user_avatar_url").val(attachment.url);
		      $('#upload_user_avatar_preview img').attr('src',attachment.url);
		      // Do something with attachment.id and/or attachment.url here
		    });

		    // Finally, open the modal
		    file_frame.open();
		  });
	
	
	
		  // jQuery('.upload_user_cover_button').live('click', function( event ){
			$("body").on("click",".upload_user_cover_button",function(event){
		    event.preventDefault();

		    // If the media frame already exists, reopen it.
		    if ( file_frame ) {
		      file_frame.open();
		      return;
		    }

		    // Create the media frame.
		    file_frame = wp.media.frames.file_frame = wp.media({
		      title: jQuery( this ).data( 'uploader_title' ),
		      button: {
		        text: jQuery( this ).data( 'uploader_button_text' ),
		      },
		      multiple: false  // Set to true to allow multiple files to be selected
		    });

		    // When an image is selected, run a callback.
		    file_frame.on( 'select', function() {
		      // We set multiple to false so only get one image from the uploader
		      attachment = file_frame.state().get('selection').first().toJSON();
		     // alert(attachment.url);
		      jQuery("#smgt_school_background_image").val(attachment.url);
		      $('#upload_school_cover_preview img').attr('src',attachment.url);
		      // Do something with attachment.id and/or attachment.url here
		    });

		    // Finally, open the modal
		    file_frame.open();
		  });
	
});