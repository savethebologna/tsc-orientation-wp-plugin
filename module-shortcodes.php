<?php
//Shortcode register
add_shortcode( 'tsc_accept' , 'tsc_shortcode_accept' );
add_shortcode( 'tsc_mc' , 'tsc_shortcode_mc' );
add_shortcode( 'tsc_cb' , 'tsc_shortcode_cb' );
add_shortcode( 'tsc_comments' , 'tsc_shortcode_comments' );
add_shortcode( 'tsc_continue' , 'tsc_shortcode_continue' );

function tsc_shortcode_accept( $atts, $content ){
	//Set defaults for attributes
	extract( shortcode_atts( array(
		'require' => 'yes',
	), $atts, 'tsc_accept' ) );
	
	$name = tsc_shortcode_init();
	
	
	if( empty( $content ) ) { $content = 'By checking this box, you agree to these conditions.'; }
	
	if( $require == 'yes' ) {
		$required = 'required';
	} else {
		$required = '';
	}
	
	$echovalue = '<p><input type="checkbox" name="'.$name.'" value="agreed" '.$required.' />'.$content.'</p>';
	
	return $echovalue;
}

function tsc_shortcode_mc( $atts, $content ){
	//Set defaults for attributes
	extract( shortcode_atts( array(
		'require' => 'yes',
		'correct' => 'nonecorrect',
		'options' => null,
		'grade' => 'yes'
	), $atts, 'tsc_mc' ) );
	
	$name = tsc_shortcode_init();
	
	if( $require == 'yes' && $correct != 'nonecorrect' ) {
		$required = 'required';
	} else {
		$required = '';
	}
	
	if( !empty( $options ) ){
		$optionsarray = explode( '; ', $options);
	}else{
		$optionsarray = array('True', 'False');
	}
	
	$echovalue = '<div><p>'.$content.'<br>';
	
	foreach( $optionsarray as $option ){
		$echovalue .= '<input type="radio" name="'.$name.'" value="'.$option.'" data-grade="'.$grade.'" '.$required.' />'.$option.'<br>';
	}
	
	$echovalue .= '</p></div>';
	
	return $echovalue;
}

function tsc_shortcode_cb( $atts, $content ){
	//Set defaults for attributes
	$atts = shortcode_atts( array(
		'require' => 'no',
	), $atts, 'tsc_cb' );
	
	$name = tsc_shortcode_init();
	
	//not done yet
}

function tsc_shortcode_comments( $atts, $content ){
	//Set defaults for attributes
	$atts = shortcode_atts( array(
		'required' => 'no'
	), $atts, 'tsc_comments' );
	
	global $post;
	$name = tsc_shortcode_init();
	
	if( $require == 'yes' && $correct != 'nonecorrect' ) {
		$required = 'required';
	} else {
		$required = '';
	}
		
	//If shortcode has content, we should use it
	if( $content != "" ){
		$echovalue .= '<p>'.$content.'<br><textarea name="tsc_comments" style="width:100%;" '.$required.'></textarea></p>';
	} else {
		$echovalue .= '<p>Please leave any comments or questions you may have about orientation here. Thank you!<br><textarea name="tsc_comments" style="width:100%;" '.$required.'/></textarea></p>';
	}
	
	return $echovalue;
}

function tsc_shortcode_continue( $atts, $content ){
	//Set defaults for attributes
	$atts = shortcode_atts( array(
		'two' => 'value'
	), $atts, 'tsc_continue' );
	
	global $post;
	$name = tsc_shortcode_init();
	
	$namearray = explode( '-', $name);
	$last = count($namearray) - 1;
	$sccount = $namearray[$last];
	
	if( $sccount === '1' ){
		$url = tscmod_next_url();
		if( $content != "" ){
			$echovalue = '<a href="'.$url.'"><input type="button" value="'.$content.'" /></a>';
		} else {
			$echovalue = '<a href="'.$url.'"><input type="button" value="Continue" /></a>';
		}
	}else{
		$current_user = wp_get_current_user();
		$consultant_rid = $current_user->user_login; //user name is Royal ID
		$consultant_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
		
		if( !current_user_can('delete_pages') ){
			//Find post_id for consultant or make one
			$consultant_ID = find_consultant( $consultant_rid, $consultant_name );
		} else {
			//"Test Consultant" will be available for testing modules by editors and admins
			$consultant_ID = find_consultant( 'test-consultant', 'Test Consultant' );
		}
		
		//If shortcode has content, we should use it
		if( $content != "" ){
			$echovalue .= '<p><input type="submit" name="tsc_submit" value="' . $content . '" /></p>';
		} else {
			$echovalue .= '<p><input type="submit" name="tsc_submit" /></p>';
		}
		
		$echovalue .= tsc_score_form();
		
		$echovalue .= '</form></div>';
		
		if( tsc_submit_data( $consultant_ID ) === true ) {
			$echovalue .= '<style>#tsc_form{display:none;}</style>
			<div id="tsc_submitted">
				<p>Module submitted successfully. Click next to continue.</p>
				<a href="'.tscmod_next_url().'"><input type="button" value="Continue" /></a>
			</div>';
		}
	}
	return $echovalue;
}

function tsc_shortcode_init(){
	global $post;
	static $sccount = 0;
	$sccount++;
	
	//if this is the first shortcode, add a form opening tag
	if( $sccount == 1 ) echo '<div id="tsc_form"><form method="post" autocomplete="off" action="" name="tscmod_form">';

	$post_name = $post->post_name;
	return $post_name.'-'.$sccount;
}

function find_consultant( $RID, $fullname ){
	$args = array(
	  'name' => $RID,
	  'post_type' => 'consultant'
	);
	
	$consultant = get_posts($args); //match username (RID) to slug and get post
	
	if( $fullname == " " ) $fullname = $RID; //In case first and last name haven't been set
	
	//grab the post_id from an existing post, otherwise a new post!
	if( $consultant ) {
		$post_id = $consultant[0]->ID;
	} else {
		$post = array(
			'post_name' => $RID, //RID will become the slug for the post
			'post_title' => $fullname, //the title will be the user's name
			'post_type' => 'consultant',
			'post_status' => 'publish',
			'post_author' => '0' //I guess this doesn't matter anymore
		);
		$post_id = wp_insert_post( $post );
	}
	
	return $post_id;
}

function tsc_submit_data( $consultant_id ){
	global $post;
	global $name;
	$post_name = $post->post_name;
	
	if( isset( $_POST['tsc_submit'] ) ){
		foreach( $_POST as $fullkey => $result ){
			$pos = strpos($fullkey , $post_name);
			if ( $pos === 0 && $result != '' && !empty( $result ) ){
				$key = str_replace( $post_name.'-', '', $fullkey );
				$resultsarray[$key] = $result;
			}
		}
		$module = 'tscmod_'.$post_name;
		update_post_meta( $consultant_id, $module, $resultsarray );
		if( !empty($_POST['tsc_comments']) ){
			$date = date ( 'M d, Y');
			$comments = 'On '.$date.' the consultant wrote: '.$_POST['tsc_comments'];
			$oldcomments = get_post_field( 'post_content', $consultant_id );
			if( !empty( $oldcomments ) ){
				$comments .= "\n\n".$oldcomments;
			}
			wp_update_post( array('ID' => $consultant_id, 'post_content' => $comments) );
		}
		return true;
	}
}

function tsc_score_form(){
	global $post;
	global $name;
	
	echo '<input type="hidden" name="'.$name.'-score" value="" />';
	
	//change submit to button, add js submit and scoring
	//will probably add a lot of ajax in the future
}

function tscmod_next_url(){
	global $post;
	$custom = get_post_custom($post->ID);
	$modnumber = $custom['modnumber'][0];
	if( empty($modnumber) ) $modnumber = 0;
	$modules = tscmod_list_modules();
	if( is_array($modules) ){
		foreach($modules as $module => $info){
			$othermodnumber = $info['number'];
			if((int)$othermodnumber > (int)$modnumber){
				return get_permalink( $info['ID'] );
			}
		}
	}
	return get_home_url();
}

?>