<?php
if ( !defined('CP_AUTH_INCLUDE') ) { echo 'Direct access not allowed.';  exit; }
// Corrects a conflict with W3 Total Cache
if( function_exists( 'w3_instance' ) )
{
	try
	{
		$w3_config = w3_instance( 'W3_Config' );
		$w3_config->set( 'minify.html.enable', false );
	}
	catch( Exception $err )
	{

	}
}

if( function_exists( 'cp_calculatedfieldsf_link_tag' ) )
{
	add_filter( 'style_loader_tag', 'cp_calculatedfieldsf_link_tag' );
}
wp_enqueue_style( 'cpcff_stylepublic', plugins_url('css/stylepublic.css', __FILE__), array(), 'pro' );
wp_enqueue_style( 'cpcff_jquery_ui'  , plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__), array(), 'pro' );

$form_data = cp_calculatedfieldsf_get_option( 'form_structure', CP_CALCULATEDFIELDSF_DEFAULT_form_structure, $id );
if( !empty( $form_data ) )
{
	if( isset( $form_data[ 1 ] ) && isset( $form_data[ 1 ][ 0 ] ) && isset( $form_data[ 1 ][ 0 ]->formtemplate ) )
	{
		$templatelist = cp_calculatedfieldsf_available_templates();
		$template = $form_data[ 1 ][ 0 ]->formtemplate;
		if( isset( $templatelist[ $template ] ) && !defined('CPCFF_LOADED_TEMPLATE'.$template))
        {
			define('CPCFF_LOADED_TEMPLATE'.$template, true);
			if(CP_CALCULATEDFIELDSF_DEFAULT_DEFER_SCRIPTS_LOADING)
			{
				 wp_enqueue_style( 'cpcff_template_css'.$template,  $templatelist[ $template ][ 'file' ], array(), 'pro' );
			}
			else
			{
				print '<link href="'.esc_attr( esc_url( $templatelist[ $template ][ 'file' ] ) ).'?ver=pro" type="text/css" rel="stylesheet" property="stylesheet" />';
			}

            if( isset( $templatelist[ $template ][ 'js' ] ) )
            {
                if(CP_CALCULATEDFIELDSF_DEFAULT_DEFER_SCRIPTS_LOADING)
				{
					 wp_enqueue_script( 'cpcff_template_js'.$template,  $templatelist[ $template ][ 'js' ], array(), 'pro' );
				}
				else
				{
					print '<script src="'.esc_attr( esc_url( $templatelist[ $template ][ 'js' ] ) ).'"></script>';
				}
            }
        }
	}
	$form_data[ 1 ][ 'formid' ]="cp_calculatedfieldsf_pform".$CP_CFF_global_form_count;
?>
<form name="<?php echo $form_data[ 1 ][ 'formid' ]; ?>" id="<?php echo $form_data[ 1 ][ 'formid' ]; ?>" action="?" method="post" enctype="multipart/form-data"><pre style="display:none;"><!--noptimize--><script>form_structure<?php echo $CP_CFF_global_form_count; ?>=<?php print str_replace( array( "\n", "\r" ), " ", ((version_compare(CP_CFF_PHPVERSION,"5.3.0")>=0)?json_encode($form_data, JSON_HEX_QUOT|JSON_HEX_TAG):json_encode($form_data)) ); ?>;</script><!--/noptimize--></pre>
<div id="fbuilder">
  <div id="fbuilder<?php echo $CP_CFF_global_form_count; ?>">
      <div id="formheader<?php echo $CP_CFF_global_form_count; ?>"></div>
      <div id="fieldlist<?php echo $CP_CFF_global_form_count; ?>"></div>
  </div>
</div>
<div class="clearer"></div>
</form>
<?php
}
?>