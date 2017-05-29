<?php
// grab site options values
$bbpress_breadcrumbs      = get_option('ka_bbpress_breadcrumbs');
$bbpress_title            = get_option('ka_bbpress_title');
?>

<div class="tools">
	<span class="tools-top"></span>
        <div class="frame">

			<h1><?php bbp_forum_title(); ?></h1>
				<?php bbp_breadcrumb(); ?>

        </div><!-- END frame -->
	<span class="tools-bottom"></span>
</div><!-- END tools -->