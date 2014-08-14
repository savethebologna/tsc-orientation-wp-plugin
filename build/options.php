<?php
// create custom plugin settings menu
add_action('admin_menu', 'tscmod_create_menu');

function tscmod_create_menu() {

	//create new top-level menu
	$hooksuffix = add_menu_page('TSC Testing Plugin', 'Testing Settings', 'delete_pages', __FILE__, 'tscmod_settings_page','dashicons-forms');

	//get jquery, the sortable plugin, and some custom jquery ready
	add_action( 'load-' . $hooksuffix, 'hook_tsc_scripts' );
	function hook_tsc_scripts(){
		add_action( 'admin_enqueue_scripts', 'enqueue_tsc_scripts' );
		function enqueue_tsc_scripts(){
			wp_register_script( 'jqsortable', plugins_url( 'jquery.sortable.min.js', __FILE__ ), array('jquery') );
			wp_register_script( 'sortmodules', plugins_url( 'sortmodules.js', __FILE__ ), array('jqsortable') );
			$passtosortmodules = array( 'saveurl' => plugins_url( 'saveoptions.php', __FILE__ ) );
			wp_localize_script( 'sortmodules', 'wp_tsc_object', $passtosortmodules );
			wp_enqueue_script( 'sortmodules' );
		}
	}
	
	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'tscmod_settings_group', 'tscmod_parent_page' );
	register_setting( 'tscmod_settings_group', 'some_other_option' );
	register_setting( 'tscmod_settings_group', 'option_etc' );
}

function tscmod_settings_page() {
?>
<style>
.postbox{
width:45%;
margin: 0 5px;
min-width:300px;
display:inline-block;
}
.tscmod_sortable{
width:auto;
padding: 1em 1em;
background-color: #eee;
box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.tscmod_sortable li{
font-size:1.3em;
display:block;
background-color: #fff;
text-align:center;
line-height:2em;
box-shadow: 0 1px 1px rgba(0,0,0,.04);
border: 1px solid #e5e5e5;
}
#savestate{
width: 100%;
text-align: center;
}
#savestate .spinner{
vertical-align:text-bottom;
display:inline-block;
float:none;
}
.hidden{
display:none !important;
}
p.submit{
text-align:center;
padding-bottom:0;
}
.center{
text-align:center;
}
.inside{
text-align:left;
}
.js .postbox h3{
cursor:auto;
}
</style>
<div class="wrap">
<h2>TSC Testing Plugin</h2>
<div class="center">
	<div id="tscmod_reorder" class="postbox">
		<div class="inside">
			<h3>Reorder Training Modules</h3>
			<p>Drag and drop the modules to change the order. The first module is at the top. You may need to reorder these items in the menu if you have not hidden them.</p>
			<ul class="tscmod_sortable">
<?php
	$modules = tscmod_list_modules();
	if( is_array($modules) ){
		foreach($modules as $module => $info){
			$modtitle = $info['title'];
			$modid = $info['ID'];
			echo '<li data-modid="'.$modid.'">'.$modtitle.'</li>';
		}
	}else{
		echo $modules;
		echo "\n<br>If this seems incorrect, verify your parent page is set correctly.";
	}
?>		
			</ul>
			<div id="savestate" class="hidden"><div class="spinner"></div><span id="saving">Saving</span></div>
			<p class="submit"><input type="button" name="submit" id="submit" class="button button-primary" value="Save Order" /></p>
		</div>
	</div>
	<div id="tscmod_choose_parent" class="postbox">
		<div class="inside">
			<h3>Additional Options</h3>
			<form method="post" action="options.php">
				<p>Remember to save. These settings should not change often.</p>
				<label>Parent Page:</label>
				<select class="tscmod_pages" name="tscmod_parent_page">
<?php
	global $parent_page;
	$pages = get_pages();
	foreach ( $pages as $page ) {
		if($parent_page === $page->ID){ $selected = "selected"; }else{ $selected = ""; }
		$option = '<option value="' . $page->ID . '" '.$selected.'>';
		$option .= $page->post_title;
		$option .= '</option>';
		echo $option;
	}
?>
				</select>
<?php settings_fields( 'tscmod_settings_group' ); ?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" /></p>
<!--I HAVE NO IDEA WHY THIS DIV MOVES WHEN SUBMIT IS CLICKED IN CHROME-->
<!--Additionally, its placement is weird, but again without a known reason-->
			</form>
		</div>
	</div>
	<div style="display:none" id="hidden_form_elements"></div>
</div>
</div>
<!--JS for Drag/Drop sorting for module order-->

<?php } ?>