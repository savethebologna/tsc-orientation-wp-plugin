<?php
// create custom plugin settings menu
add_action('admin_menu', 'tscmod_create_menu');

function tscmod_create_menu() {

	//create new top-level menu
	add_menu_page('TSC Testing Plugin', 'Testing Settings', 'delete_pages', __FILE__, 'tscmod_settings_page','dashicons-forms');

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
height:2em;
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
			</form>
		</div>
	</div>
	<div style="display:none" id="hidden_form_elements"></div>
</div>
</div>
	<script src="<?php echo plugins_url( 'jquery.sortable.min.js' , __FILE__ ); ?>"></script>
	<script>
	jQuery('.tscmod_sortable').sortable();
	jQuery('#submit').on('click', tscmodUpdateOptionsByPOST);
	function tscmodUpdateOptionsByPOST(){
		jQuery('#savestate').removeClass('hidden');
		var pathname = document.URL;
		var tscmodForm = document.createElement('form');
		tscmodForm.action = pathname;
		tscmodForm.method = 'post';
		tscmodForm.id = 'tscmod_hidden_order_form';
		var tscmodInput = [];
		jQuery('.tscmod_sortable > li').each(function(i,j){
			i++;
			jQuery(this).attr('data-modnumber', i);
			var modnumber = jQuery(this).attr('data-modnumber');
			var modid = jQuery(this).attr('data-modid');
			tscmodInput[i] = document.createElement('input');
			tscmodInput[i].type = 'hidden';
			tscmodInput[i].name = 'tscmodid_' + modid;
			tscmodInput[i].value = modnumber;
			tscmodForm.appendChild(tscmodInput[i]);
		});  
		try { document.getElementById('tscmod_hidden_order_form').remove(); } catch(err) {  } //do nothing if there's a problem
		document.getElementById('hidden_form_elements').appendChild(tscmodForm);
		var posting = jQuery.post( '<?php echo plugins_url( 'saveoptions.php' , __FILE__ ); ?>', jQuery('#tscmod_hidden_order_form').serialize() );
		posting.done(function( data ) {
			var content = jQuery( data ).find('#saveresult');
			jQuery('#saving').empty().append(data);
			jQuery('#savestate > .spinner').addClass('hidden');
			jQuery('#savestate').fadeOut(5000, 'swing', function(){
				jQuery('#savestate').addClass('hidden');
				jQuery('#savestate > .spinner').removeClass('hidden');
				jQuery('#savestate').css('display','block');
				jQuery('#saving').empty().append('saving');
			});
		});
	}
	</script>
<?php } ?>