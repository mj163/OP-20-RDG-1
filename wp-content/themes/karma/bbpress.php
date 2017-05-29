<?php 
/**
 * The template for displaying bbPress
 *
 * @package Karma
 * @author TrueThemes
 * @link http://truethemes.net
 */

get_header();

//grab values from options panel
$bbpress_layout             = get_option('ka_bbpress_layout');
$bbpress_tools_panel        = get_option('ka_bbpress_tools_panel');
$ka_page_title_bar_select   = get_option('ka_page_title_bar_select');//@since 4.6
$bbpress_searchbar          = get_option('ka_bbpress_searchbar');
$bbpress_breadcrumbs        = get_option('ka_bbpress_breadcrumbs');
$header_shadow_style        = get_option('ka_header_shadow_style');//@since 4.8
?>

</div><!-- END header-area -->
</div><!-- END header-overlay -->
</div><!-- END header-holder -->
</header><!-- END header -->

<?php truethemes_before_main_hook(); //action hook ?>

<div id="main" class="karma-bbpress">
	<?php
		// Check for full-width page-title-bar (breadcrumbs, etc)
		if( ('Full Width' === $ka_page_title_bar_select) && ('true' === $bbpress_tools_panel) ):
			get_template_part( 'bbpress/theme-template-part-tools-bbpress','fullwidth' );
		endif;

		/* //header shadow style
		if (('no-shadow' != $header_shadow_style) && ('Full Width' != $ka_page_title_bar_select)) : ?>
		<div class="karma-header-shadow"></div><!-- END karma-header-shadow --> 
		<?php endif; //END header shadow style ?>
		*/
		?>

	<div class="main-area">
		<?php
		// Check for fixed-width page-title-bar (breadcrumbs, etc)
		if( ('Fixed Width' === $ka_page_title_bar_select) && ('true' === $bbpress_tools_panel) ):
			get_template_part( 'bbpress/theme-template-part-tools-bbpress','fixed-width' );
		endif;

//if Right Sidebar
if ('Right Sidebar' == $bbpress_layout): echo '<main role="main" id="content">'; endif;

//if Left Sidebar
if ('Left Sidebar' == $bbpress_layout): echo '<main role="main" id="content" class="content_left_sidebar content_no_subnav">'; endif;

//if Full-Width
if ('Full Width' == $bbpress_layout): echo '<main role="main" id="content" class="content_full_width">'; endif;


if ( have_posts() ) : 
	while( have_posts() ) :
		the_post(); 
		the_content();
		truethemes_link_pages(); 
	endwhile;
endif; 
comments_template('/page-comments.php', true);
?>
</main><!-- END main #content -->


<?php 
//if Right Sidebar
if ('Right Sidebar' == $bbpress_layout): 
	echo '<aside role="complementary" id="sidebar" class="right_sidebar" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">';
		dynamic_sidebar("bbPress Forum");
	echo'</aside><!-- END .right_sidebar-->';
endif;


//if Left Sidebar
if ('Left Sidebar' == $bbpress_layout): 
	echo '<aside role="complementary" id="sidebar" class="left_sidebar" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">';
		dynamic_sidebar("bbPress Forum");
	echo'</aside><!-- END .left_sidebar-->';
endif;
?>

</div><!-- END main-area -->
<?php get_footer(); ?>