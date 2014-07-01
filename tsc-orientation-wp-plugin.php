<?php
	/*
	Plugin Name: TSC Orientation WordPress Plugin
	Plugin URI: http://github.com/savethebologna/tsc-orientation-wp-plugin
	Description: This plugin powers the TSC training modules and orientation log. Training may be tracked through the Orientation Log custom post type.
	Author: Justin J. Goreschak
	Version: 0.2c
	Author URI: http://goreschak.com
	*/

//Cache current directory
$dir = dirname( __FILE__ );

//OPTIONS
$parent_page = (int) get_option('tscmod_parent_page'); //Set to id of the training intro page
require( $dir . '/options.php' ); //build admin page for options

//Create custom post type
require( $dir . '/orientation-log.php' );

//Add some custom meta to module pages (to order modules)
require( $dir . '/module-meta-supplement.php' );

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

//Create page template for modules that are children of $parent_page
//$parent_page is set in OPTIONS
add_filter( 'page_template', 'create_module_template' );
function create_module_template( $module_template ){
	global $post;
	global $dir;
	global $parent_page;
	if( is_page() ) {
		$parents = get_post_ancestors($post->ID);
		$parent_id = get_page($parents[0])->ID;
		if ( $parent_page == $parent_id ) {
			$module_template = $dir . '/module-template.php';
		}
	}
    return $module_template;
}

//load a list of the module pages
function tscmod_list_modules(){
	global $parent_page;
	$modulequeryargs = array(
		'child_of' => $parent_page,
		'hierarchical' => 0,
	);
	$modules = get_pages($modulequeryargs);
	
	$i = 0;
	foreach( $modules as $module => $info ){
		$custom = get_post_custom($info->ID);
		$modulesarray[$i]['number'] = $custom['modnumber'][0];
		$modulesarray[$i]['slug'] = $info->post_name;
		$modulesarray[$i]['title'] = $info->post_title;
		$modulesarray[$i]['ID'] = $info->ID;
		$i++;
	}
	
	$sort = array();
	if( is_array($modulesarray) ) {
		foreach($modulesarray as $k => $v){
			$sort['number'][$k] = $v['number'];
		}
		array_multisort($sort['number'], SORT_ASC, $modulesarray);
	}else{
	$modulesarray = "There are fewer than two modules.";
	}
	
	return $modulesarray;
}

//Create an easy means for preparing module variables
function load_module_results($consultant){
	$custom = get_post_custom($consultant->ID);
	foreach( $custom as $mod => $resultstring ){
		$pos = strpos( $mod, 'tscmod_' );
		if( $pos === 0 ){
			$mod_name = str_replace( 'tscmod_', '', $mod );
			$resultarray = maybe_unserialize($resultstring[0]);
			$modules[$mod_name] = $resultarray;
		}
	}
	return $modules;
}

//Makes a dropdown with each module name in it
//Used alongside load_module_results($post) and js to load module results usually
function create_module_dropdown($modules){
	if(is_array($modules)) {
		echo "<p>
			<select name='moduleDropdown' onChange='handleSelect(this.value)'>
			<option value='' selected>Choose a module</option>";
		foreach ( $modules as $module => $resultarray ) {
			$moduleels = explode('_',$module);
			$moduleid = $moduleels[0];
			$moduletitle = get_the_title($moduleid);
			echo '<option value="'.$module.'">'.$moduletitle.'</option>';
		}
		echo "</select></p>";
		return true;
	}else{
		return false;
	} 
}

//add script for dropdown toggle
function create_module_dropdown_script($modules,$div,$input){
	if( is_array($modules) ) {
		$echovalue = "<script>function handleSelect(module) {\nswitch(module)\n{\n";
		if( $input == true || $input == 'yes' ){ $displaytext = "<p><strong>Viewing a different module will erase any changes you have made, unless you click update first.</strong></p>"; }
		foreach ( $modules as $module => $resultarray ) {
			$echovalue .= "case \"".$module."\":\ndocument.getElementById('".$div."').innerHTML = \"".$displaytext;
			if( is_array($resultarray) ){
				if( $input == true || $input == 'yes' ){
					foreach ( $resultarray as $key => $result ) {
						$echovalue .= "<div><label>".$key.":</label><input name='tscmod_".$module."_".$key."' value='".$result."' /></div>";
					}
				}else{
					foreach ( $resultarray as $key => $result ) {
						$questionels = explode( '-', $key );
						$last = count($questionels) - 1;
						if( $questionels[$last] === 'comments' ){
							$echovalue .= "<div>User Comments: ".$result."</div><br>";
						}else{
							$echovalue .= "<div>Question ".$questionels[$last].": ".$result."</div><br>";
						}
					}
				}
			}else{
				$echovalue .= '<div>There are no results for this module.</div>';
			}
			$echovalue .= "\";\nbreak;\n";
		}
		$echovalue .= "}}</script>";
	}else{
		$echovalue = "Modules not loaded.";
	}
	return $echovalue;
}

//Renumber modules after page delete

?>
