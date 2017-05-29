<?php
global $ttso;
$inline_editing = $ttso->ka_inline_editing;

if ($inline_editing == "true") {

if (is_home() || is_single()) {

	$user = wp_get_current_user();
	$allowed_roles = array('editor', 'administrator', 'author');
	if( array_intersect($allowed_roles, $user->roles ) ) { 
		edit_post_link(__('+ Edit Post' , 'truethemes_localize'), '<p class="edit-page-button">', '</p>');
	}

} else {

	$user = wp_get_current_user();
	$allowed_roles = array('editor', 'administrator', 'author');
	if( array_intersect($allowed_roles, $user->roles ) ) { 
		edit_post_link(__('+ Edit Page' , 'truethemes_localize'), '<p class="edit-page-button">', '</p>');
	}
}

} // END inline_editing
?>