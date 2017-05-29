<?php
//This file is required by public function render_blog_posts() in karma_builder.php
//The following are values set in blog_posts shortcode.
//$style (Frame Style) not in use
$count = $character_count; // sets the character count, for use in limit_content();
$ka_blogbutton = $link_text; //sets the read more or continue reading button text


//The rest of this file is 99% indentical to that found in Karma Theme post-formats folder.

global $ttso;
$ka_blogbutton_color      = $ttso->ka_blogbutton_color;
$ka_blogbutton_size       = $ttso->ka_blogbutton_size;
$ka_posted_by             = $ttso->ka_posted_by;
$ka_dragshare             = $ttso->ka_dragshare;
$content_default          = $ttso->ka_tt_content_default;


//pre-define values for backward compatibility
if ('' == $ka_blogbutton_color): 'black'  == $ka_blogbutton_color; endif;
if ('' == $ka_blogbutton_size):  'small'  == $ka_blogbutton_size;  endif;
if ('' == $content_default):     'false'  == $content_default;     endif;

//format "continue reading" button
$formatted_size    =  strtolower($ka_blogbutton_size);
$formatted_button  =  $formatted_size.'_button '.$formatted_size.'_'.$ka_blogbutton_color;

//define post_class
//http://codex.wordpress.org/Template_Tags/post_class
//http://codex.wordpress.org/Function_Reference/get_post_class
$array_post_classes = get_post_class(); 
$post_classes = '';
foreach($array_post_classes as $post_class){
$post_classes .= " ".$post_class;
}
?>

<article class="blog_wrap <?php echo $post_classes;if ('' == ($video_url || $external_image_url || $thumb)):echo ' true-blog-no-feature';endif;?>" id="post-<?php the_ID(); ?>">
<div class="post_title">
<?php truethemes_begin_post_title_hook(); // action hook ?>

<?php if ('' == $linkpost): ?>
<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2> <?php else: ?>

<h2 class="entry-title"><a href="<?php echo $linkpost; ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2> <?php endif; ?>

<?php if ('true' != $ka_posted_by): ?>
<p class="posted-by-text"><span><?php _e('Posted by:', 'truethemes_localize') ?></span> <span class="vcard author"><span class="fn"><?php the_author_posts_link(); ?></span></span></p> <?php endif; ?>

<?php truethemes_end_post_title_hook(); // action hook ?>
</div><!-- END post_title -->

<div class="post_content">
<?php truethemes_begin_post_content_hook(); // action hook ?>

	<div class="karma-blog-slider">
		<ul class="slides">
			<?php

			//Use large size gallery images for gallery shortcode,
			//see function at end of functions.php 
			add_filter( 'shortcode_atts_gallery', 'tt_shortcode_atts_gallery' );
			global $post;
			$id = $post->ID;
			//This grabs the first gallery shortcode only!, doesn't matter how many set you added in content.
			$gallery = get_post_gallery_images( $id ); 
				     if ( $gallery ) {
				        foreach ( $gallery as $gallery_image ) {
				           echo '<li><div class="masonry-slider">';
				           echo "<img src='$gallery_image' />";
				           echo '</div></li>';
				          }
				     }
			
			?>
		</ul>
	</div><!--blog-slider -->

<?php
//@since 4.0
//check for content() enabled in Site Options, if not load custom function
if ("true" == $content_default) {
	
	//comment out original codes.
	//the_content('<span class="ka_button '.$formatted_button.'"><span>'.$ka_blogbutton.'</span></span>');

	$content = tt_strip_shortcode_gallery(get_the_content('<span class="ka_button '.$formatted_button.'"><span>'.$ka_blogbutton.'</span></span>'));
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	echo $content;	
} else {
	limit_content($count,  true, '');
	echo '<a class="ka_button '.$formatted_button.'" href="'.get_permalink().'" rel="bookmark" title="';_e('Continue reading ', 'truethemes_localize'); echo get_the_title().'">
	<span>'.$ka_blogbutton.'</span></a>';
}

get_template_part('theme-template-part-inline-editing','childtheme');

//shareaholic plugin (display if installed)
if(function_exists('selfserv_shareaholic')) { selfserv_shareaholic(); } ?>

<div class="post_date">
	<span class="day date updated"><?php the_time('j'); ?></span>
    <br />
    <span class="month"><?php echo strtoupper(get_the_time('M')); ?></span>
</div><!-- END post_date -->

<div class="post_comments">
	<a href="<?php echo the_permalink().'#post-comments'; ?>"><span><?php comments_number('0', '1', '%'); ?></span></a>
</div><!-- END post_comments -->

<?php
global $post; 
$post_title = get_the_title($post->ID);
$permalink = get_permalink($post->ID);
if ($ka_dragshare == "true"){ echo "<a class='post_share sharelink_small' href='$permalink' data-gal='prettySociable'>Share</a>"; }
?>

<?php truethemes_end_post_content_hook();// action hook ?>
</div><!-- END post_content -->

<div class="post_footer">
    <?php truethemes_begin_post_footer_hook();// action hook ?>
        <p class="post_cats"><?php the_category(', '); ?></p>
        
        <?php if (get_the_tags()) : ?>
        <p class="post_tags"><?php the_tags('', ', '); ?></p>
    
    <?php endif; truethemes_end_post_footer_hook();// action hook?>
    </div><!-- END post_footer -->
</article><!-- END blog_wrap -->