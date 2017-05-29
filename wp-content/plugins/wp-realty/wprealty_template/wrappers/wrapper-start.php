<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$template = get_option('template');

switch( $template ) {

	// IF Twenty Eleven
	case 'twentyeleven' :
		echo '<div id="primary"><div id="content" role="main" class="emgt_style">';		
		break;
	// IF Twenty Twelve
	case 'twentytwelve' :
		echo '<div id="primary" class="site-content custom_twelve_style"><div id="content" role="main" class="entry-content emgt_style">';
		break;
	// IF Twenty Fourteen
	case 'twentyfourteen' :
		echo '<div id="main-content" class="main-content custom_fouteen_style"><div id="primary" class="content-area"><div id="content" class="site-content" role="main"><div class="entry-content emgt_style">';
		break;
		// IF twentyfifteen
	case 'twentyfifteen' :		
		echo '<div id="primary" class="content-area custom_fifteen_style">
			<main id="main" class="site-main emgt_style" role="main">';					
		break;
	// IF Twenty Sixteen
	case 'twentysixteen' :
		echo '<div id="primary" class="content-area custom_sixteen_style">
			  <main id="main" class="site-main emgt_style" role="main">';	
		break;
	case 'wprealty':
		
		echo '<div id="primary" class="content-area emgt_style"><div id="content" class="site-content" role="main"><div class="entry-content123">';
		echo "<style>
			@media screen and (min-width:768px)
			{
				.rems_container{
					margin-left:50px;
					margin-right:-58px;
				}
			}
		</style>";
		break;
	// Default
	default :
		// echo '<div id="content" class="page wplms-col-full">		
		// <div id="main" class="wplms-col-left"><div class="wplms-container">';
		echo '<div class="row">';
		echo '<div class="col-md-8 no-padding">';
		echo "<style>
			@media screen and (min-width:768px)
			{
				.rems_container{
					margin-left:50px;
					margin-right:-58px;
				}
			}
		</style>";
		break;
}

?>