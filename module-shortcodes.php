<?php
//Shortcode register
add_shortcode( 'tsc-mc' , 'tsc_orientation_mc' );

function tsc_orientation_mc( $atts, $content="" ){

orientation_tracker( $atts, $content );
}

function orientation_tracker( $atts, $content="" ){
	//Set defaults for attributes
	$atts = shortcode_atts( array(
		'firstpost' => 'yes',
		'two' => 'value'
	), $atts, 'tsc-tracker' );
	
	$current_user = wp_get_current_user();
	$mod_title = 'Test';
	$mod_result = 'Test Result';
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
		//if content is supplied, we'll show that text as a question
		//we might need more short codes for types of questions
		echo $content;
	} else {
		//if no content is supplied, we'll use hidden forms
		echo $consultant_ID;
		echo '<input type="hidden" name="' . $mod_title . '" value="' . $mod_result . '" />';
		echo '<p><input type="submit" value="Continue..." /></p>';
	}
}

function find_consultant( $RID, $name ){
	$args = array(
	  'name' => $RID,
	  'post_type' => 'consultant'
	);
	
	$consultant = get_posts($args); //match username (RID) to slug and get post
	
	if($name == " ") { $name = $RID; } //In case first and last name haven't been set
	
	//grab the post_id from an existing post, otherwise a new post!
	if( $consultant ){
		$post_id = $consultant[0]->ID;
	}else{
		$post = array(
			'post_name' => $RID, //RID will become the slug for the post
			'post_title' => $name, //the title will be the user's name
			'post_type' => 'consultant',
			'post_status' => 'publish',
			'post_author' => '0' //I guess this doesn't matter anymore
		);
		$post_id = wp_insert_post( $post );
	}
	
	return $post_id;
}
?>