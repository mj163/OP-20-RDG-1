<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$template = get_option('template');

switch( $template ) {

	// IF Twenty Eleven
	case 'twentyeleven' :
	?>
			</div>
		</div>
	<?php
		break;

	// IF Twenty Twelve
	case 'twentytwelve' :
	?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
	<?php
		break;

	// IF Twenty Fourteen
	case 'twentyfourteen' :
	?>
					</div>
				</div>
			</div>
		</div>
		<?php get_sidebar(); ?>
	<?php
		break;
	case 'twentyfifteen' :
		echo '</div></main>';
		
		break;
	// IF Twenty Sixteen
	case 'twentysixteen' :
		echo '</div></main>';
		 get_sidebar();
		break;
	case 'wprealty':
		echo '</div></div></div>';
			 get_sidebar();
		break;
	// Default
	default :
	?>
	</div>
		<?php
		global $post;		
		if(is_page() || is_single())  
		{
			if(is_single() && $post->post_type == "emgt_add_listing")
			{
				do_shortcode("[emgt_single_property_sidebar]");	 
			}else{
				if($post->post_name!="register"){do_shortcode("[emgt_agent_list]");}
				get_sidebar(); 
			}
		}
		else{		
				if($_GET["emgt_search"] == "yes")
				{do_shortcode("[emgt_agent_list]");}
				get_sidebar(); // for profile view page 
			}
		?>		
	</div>

	<?php
		break;
}

?>