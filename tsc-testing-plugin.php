<?php
	/*
	Plugin Name: TSC Testing Plugin
	Plugin URI: http://goreschak.com/tsc
	Description: This plugin powers the TSC training modules and orientation log. Training may be tracked through the Orientation Log custom post type.
	Author: Justin J. Goreschak
	Version: 0.1c
	Author URI: http://goreschak.com
	*/

//Cache current directory
$dir = dirname( __FILE__ );

//Create custom post type
require( $dir . '/orientation-log.php' );

//Create shortcodes
require( $dir . '/module-shortcodes.php' );

//Create template for custom post type
add_filter( 'single_template', 'create_single_consultant' );
function create_single_consultant( $single_consultant ){
	global $post;
	global $dir;
    if ( $post->post_type == 'consultant' ) {
		$single_consultant = $dir . '/single-consultant.php';
	}
    return $single_consultant;
}

//Create page template for modules
add_filter( 'page_template', 'create_module_template' );
function create_module_template( $module_template ){
	global $post;
	global $dir;
    if ( is_page( 'module' ) ) {
        $module_template = $dir . '/module-template.php';
    }
    return $module_template;
}

//Create an easy means for preparing module variables
function load_module_results($post){
	$custom = get_post_custom($post->ID);
	$mod = $custom["mod"][0];
	$cleanmod = maybe_unserialize($mod);
	return $cleanmod;
}

?>