<?php

class Cornerstone_Debug extends Cornerstone_Plugin_Component {

  public function setup() {
    add_action( 'parse_request', array( $this, 'detect_load' ) );
  }

  public function detect_load( $wp ) {

    if ( ! ( current_user_can('manage_options') && isset( $_GET['cs-debug'] ) && '1' === $_GET['cs-debug'] ) ) {
      return;
    }

    add_filter( 'template_include', '__return_empty_string', 999999 );

    remove_all_actions( 'wp_enqueue_scripts' );
    remove_all_actions( 'wp_print_styles' );
    remove_all_actions( 'wp_print_head_scripts' );

    global $wp_styles;
    global $wp_scripts;

    $wp_styles = new WP_Styles();
    $wp_scripts = new WP_Scripts();

    if ( !class_exists('WP_Admin_Bar') ) {
      _wp_admin_bar_init();
    }

    $this->show_admin_bar = true;
    nocache_headers();

    $this->boilerplate();

    exit;

  }

  public function boilerplate() { ?>

    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
    <title>CS Debug</title>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    wp_enqueue_scripts();
    wp_print_styles();
    wp_print_head_scripts();
    ?>
    </head>
    <body<?php $this->body_classes(); ?>>
    <?php
      do_action('cs_debug');
      wp_print_footer_scripts();
      wp_admin_bar_render();
    ?>
    </body>
    </html>
    <?php
  }

  public function body_classes() {

    $classes = array( 'no-customize-support' );

    if ( is_rtl() ) {
      $classes[] = 'rtl';
    }

    if ( $this->show_admin_bar ) {
      $classes[] = 'admin-bar';
    }

    if ( empty( $classes ) ) {
      return;
    }

    $classes = array_map( 'esc_attr', array_unique( $classes ) );
    $class = join( ' ', $classes );
    echo " class=\"$class\"";

  }
}
