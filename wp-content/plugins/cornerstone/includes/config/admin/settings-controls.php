<?php

$controls = array(
	'allowed_post_types' => array(
		'type' => 'multi-select',
		'ui' => array(
			'title'   => __( 'Allowed Post Types', 'cornerstone' ),
			'description' => __( 'Select which post types to enable for the Content Builder.', 'cornerstone' ),
		),
		'options' => array(
			'placeholder' => __( 'Click to select post types.', 'cornerstone' ),
			'choices' => $this->component( 'Settings_Handler' )->get_post_type_choices()
		)
	),
	'permitted_roles' => array(
		'type' => 'multi-select',
		'ui' => array(
			'title'       => __( 'Content Roles', 'cornerstone' ),
			'description' => __( 'Allow content editing for roles other than Administrator.', 'cornerstone' ),
		),
		'options' => array(
			'placeholder' => __( 'Click to choose additional roles.', 'cornerstone' ),
			'choices' => $this->component( 'Settings_Handler' )->get_role_choices()
		)
	),
	'show_wp_toolbar' => array(
		'type' => 'checkbox',
		'ui' => array(
			'title'       => __( 'Show WordPress Toolbar', 'cornerstone' ),
			'description' => __( 'While working in the application, you may opt to display the WordPress toolbar.', 'cornerstone' ),
		)
	),
	'visual_enhancements' => array(
		'type' => 'checkbox',
		'ui' => array(
			'title'       => __( 'Fun Mode', 'cornerstone' ),
			'description' => __( 'Turns on creative save messages.', 'cornerstone' ),
		)
	),
  'help_text' => array(
		'type' => 'checkbox',
		'ui' => array(
			'title'       => __( 'Help Text', 'cornerstone' ),
			'description' => __( 'Show helpful inline messaging throughout the tool to describe various features.', 'cornerstone' ),
		)
	),

  'custom_app_slug' => array(
    'type' => 'text',
    'ui' => array(
      'title'       => __( 'Custom Path', 'cornerstone' ),
      'description' => __( 'Change the path used to load the main interface.', 'cornerstone' ),
    ),
    'options' => array(
      'placeholder' => apply_filters( 'cornerstone_default_app_slug', 'cornerstone' )
    ),
  ),

  'hide_access_path' => array(
    'type' => 'checkbox',
    'ui' => array(
      'title'       => __( 'Hide Access Path', 'cornerstone' ),
      'description' => __( 'Logged out users trying to access the interface will see a 404 instead of a login prompt.', 'cornerstone' ),
    )
  )
);

return $controls;
