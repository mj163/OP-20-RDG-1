<?php 
defined( 'ABSPATH' ) or die( 'Access Denied!' );
function estate_admin_menu()
{
if(REMS_CURRENT_ROLE == "administrator")
{
 add_menu_page('estate',__('Real Estate Management','estate-emgt'),'administrator','emgt_estate','emgt_dashboard','dashicons-admin-home',20);
 add_submenu_page('emgt_estate','Dashboard',__('Dashboard','estate-emgt'),'administrator','emgt_estate','emgt_dashboard');
 add_submenu_page('emgt_estate','Fields Management',__('Fields Management','estate-emgt'),'administrator','emgt_fields','emgt_field_manage');
 add_submenu_page('emgt_estate','User Management',__('User Management','estate-emgt'),'administrator','emgt_users','emgt_user_manage');
 add_submenu_page('emgt_estate','Inquiries',__('Inquiries','estate-emgt'),'administrator','emgt_inquiries','emgt_inquiries');
 add_submenu_page('emgt_estate','Payment',__('Payment','estate-emgt'),'administrator','emgt_payment','emgt_payment');
 add_submenu_page('emgt_estate','Reports',__('Reports','estate-emgt'),'administrator','emgt_reports','emgt_reports');
 add_submenu_page('emgt_estate','Plan Creator',__('Plan Creator','estate-emgt'),'administrator','emgt_plan','emgt_plan_creator');
 add_submenu_page('emgt_estate','Case Management',__('Case Management','estate-emgt'),'administrator','emgt_case','emgt_case_manage');
 add_submenu_page('emgt_estate','Task Management',__('Task Management','estate-emgt'),'administrator','emgt_task','emgt_task_manage');
 add_submenu_page('emgt_estate','Settings',__('Settings','estate-emgt'),'administrator','emgt_settings','emgt_settings');
}
 if(REMS_CURRENT_ROLE == "emgt_role_agent")
 {	 
	 add_menu_page('estate',__('Real Estate Management','estate-emgt'),'emgt_role_agent','emgt_estate','emgt_dashboard','dashicons-admin-home',20);
	 add_submenu_page('emgt_estate','Dashboard',__('Dashboard','estate-emgt'),'emgt_role_agent','emgt_estate','emgt_dashboard'); 
	 add_submenu_page('emgt_estate','Inquiries',__('Inquiries','estate-emgt'),'emgt_role_agent','emgt_inquiries','emgt_inquiries');
	 add_submenu_page('emgt_estate','Payment',__('Payment','estate-emgt'),'emgt_role_agent','emgt_payment','emgt_payment');
	 add_submenu_page('emgt_estate','Case Management',__('Case Management','estate-emgt'),'emgt_role_agent','emgt_case','emgt_case_manage');
	 add_submenu_page('emgt_estate','Task Management',__('Task Management','estate-emgt'),'emgt_role_agent','emgt_task','emgt_task_manage');
 }
  if(REMS_CURRENT_ROLE == "emgt_role_owner")
 {
	 add_menu_page('estate',__('Real Estate Management','estate-emgt'),'emgt_role_owner','emgt_estate','emgt_dashboard','dashicons-admin-home',20);
	 add_submenu_page('emgt_estate','Dashboard',__('Dashboard','estate-emgt'),'emgt_role_owner','emgt_estate','emgt_dashboard'); 
	 add_submenu_page('emgt_estate','Inquiries',__('Inquiries','estate-emgt'),'emgt_role_owner','emgt_inquiries','emgt_inquiries');
	 add_submenu_page('emgt_estate','Payment',__('Payment','estate-emgt'),'emgt_role_owner','emgt_payment','emgt_payment');
	 // add_submenu_page('emgt_estate','Case Management',__('Case Management','estate-emgt'),'emgt_role_owner','emgt_case','emgt_case_manage');
	 add_submenu_page('emgt_estate','Task Management',__('Task Management','estate-emgt'),'emgt_role_owner','emgt_task','emgt_task_manage');
 } 
}
add_action('admin_menu','estate_admin_menu');

add_action('admin_init','emgt_register_settings');
function emgt_register_settings()
{
   add_option('field_order','');
}

function emgt_dashboard()
{
	if(REMS_CURRENT_ROLE == "administrator")
	{
		require_once REMS_PLUGIN_DIR.'/admin/dashboard/dashboard.php';
	}
	else
	{
		require_once REMS_PLUGIN_DIR.'/admin/dashboard/user_dashboard.php';
	}
}

function emgt_property_management()
{
	require_once REMS_PLUGIN_DIR.'/admin/property/index.php';
}

function emgt_field_manage()
{
	require_once REMS_PLUGIN_DIR.'/admin/fields/index.php';
}

function emgt_user_manage()
{
	require_once REMS_PLUGIN_DIR.'/admin/user_manage/index.php';
}

function emgt_payment()
{
	require_once REMS_PLUGIN_DIR.'/admin/payment/index.php';
}

function emgt_reports()
{
	require_once REMS_PLUGIN_DIR.'/admin/reports/index.php';
}

function emgt_settings()
{
	require_once REMS_PLUGIN_DIR.'/admin/settings/settings.php';
}

function emgt_inquiries()
{
	require_once REMS_PLUGIN_DIR.'/admin/inquiry/index.php';
}

function emgt_plan_creator()
{
	require_once REMS_PLUGIN_DIR.'/admin/plan_creator/index.php';
}

function emgt_task_manage()
{
	require_once REMS_PLUGIN_DIR.'/admin/task_manage/index.php';
}

function emgt_case_manage()
{
	require_once REMS_PLUGIN_DIR.'/admin/case_manage/index.php';
}

?>