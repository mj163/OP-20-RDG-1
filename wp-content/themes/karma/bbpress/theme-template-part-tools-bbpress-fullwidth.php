<?php
// grab site options values
$bbpress_breadcrumbs      = get_option('ka_bbpress_breadcrumbs');
$bbpress_title            = get_option('ka_bbpress_title');
$header_shadow_style      = get_option('ka_header_shadow_style');
?>
<div class="tools full-width-page-title-bar">
	<?php
	//header shadow style
	if ('no-shadow' != $header_shadow_style): ?>
	<div class="karma-header-shadow"></div><!-- END karma-header-shadow --> 
	<?php endif; //END header shadow style ?>

		<div class="tt-container">

			<h1><?php bbp_forum_title(); ?></h1>
			<?php bbp_breadcrumb(); ?>

		</div><!-- END tt-container -->
</div><!-- END full-width-page-title-bar -->