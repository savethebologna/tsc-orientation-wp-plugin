<?php
//lets you reorder the modules
add_action( 'add_meta_boxes', 'tscmod_add_meta_to_page' );
function tscmod_add_meta_to_page(){
	global $post;
	global $parent_page;
	$post_id = $post->ID;
	$parents = get_post_ancestors($post->ID);
	$parent = get_page($parents[0]);
	$parent_id = $parent->ID;
	if ( $parent_page === $parent_id  && $post_id != $parent_id ) {
		add_meta_box( 'tscmod-page-meta', 'TSC Testing Plugin Data', 'tscmod_pagemeta_options', 'page', 'side' );
	}
}

//Create area for extra fields
function tscmod_pagemeta_options(){  
	global $post; 
		
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
?>
<style type="text/css">
.orientation_log_extras{
padding-left:5px;
}
.orientation_log_extras div{
margin: 10px 0;
font-size:1em;
min-height:2em;
}
.orientation_log_extras div label,.orientation_log_extras div p{
width: 180px;
font-size:1.3em;
display:inline-block;
vertical-align:middle;
}
.orientation_log_extras div input{
display:inline-block;
vertical-align:middle;
width:180px;
}
</style>
<?php
	$custom = get_post_custom($post->ID);
	$modnumber = $custom['modnumber'][0];
?>
<div class="orientation_log_extras">
	<div>
		<p>Module Number: <?php if( empty($modnumber) ){ echo 'Not yet published.'; }else{ echo $modnumber; } ?></p>
		<p>You can make changes to the module order in <a href="<?php echo admin_url('admin.php?page=tsc-testing-plugin/options.php'); ?>">Testing Settings</a>.</p>
	</div>
</div>
<?php 
}

add_action( 'publish_page', 'tscmod_assign_modnumber' );
function tscmod_assign_modnumber(){
	global $post;
	global $parent_page;
	$post_id = $post->ID;
	$parents = get_post_ancestors($post->ID);
	$parent = get_page($parents[0]);
	$parent_id = $parent->ID;
	if( $parent_page === $parent_id  && $post_id !== $parent_id ){
		$custom = get_post_custom($post->ID);
		$modnumber = $custom['modnumber'][0];
		
		if( empty($modnumber) ){
			$modules = tscmod_list_modules();
			$modnumbers = array();
			if( is_array($modules) ){
				foreach( $modules as $module => $info ){
					$modnumbers[] = $info['number'];
				}
				$i = 1;
				foreach( $modnumbers as $used ){
					if( in_array( $i, $modnumbers ) === false && empty($modnumber) ){
						$modnumber = $i;
					}
					$i++;
				}
			}else{ $i = 1; }
			if( empty($modnumber) ) $modnumber = $i;
			update_post_meta( $post->ID, 'modnumber', $modnumber );
		}
	}
}


?>