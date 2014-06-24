<?php

//AJAX PAGE
//This page may be merged into options.php and AJAX removed.
//Alternatively, any saving on options.php may move to AJAX.
function find_wp_config_path() {
	$dir = dirname(__FILE__);
	do {
		if( file_exists($dir."/wp-config.php") ) {
			return $dir;
		}
	} while( $dir = realpath("$dir/..") );
	return null;
}

if ( ! function_exists('add_action') ) {
    include_once( find_wp_config_path()  . '/wp-load.php' );
}

function tscmod_reorder_modules($POSTdata){
	$needle = "tscmodid_";
	$success = "saved";
	$fail = "<strong>FAILURE!</strong>";

	if( isset( $POSTdata ) ){
		foreach( $POSTdata as $modidkey => $modnumber ){
			$pos = strpos($modidkey , $needle);
			if ( $pos === 0 && $modnumber != '' && !empty( $modnumber ) ){
				$modid = str_replace( $needle, '', $modidkey );
				update_post_meta( $modid, 'modnumber', $modnumber );
			}
		}
		if( !empty($modnumber) ) { return $success; } else { return $fail; }
	} else {
	return $fail;
	}
}
$result = tscmod_reorder_modules($_POST);
?>
<div id="saveresult">
<?php echo $result; ?>
</div>