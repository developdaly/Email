<?php

function email_parser( $post_id, $email_id, $old_status, $new_status, $string = '' ) {

	$post = get_post( $post_id );
	$email = get_post( $email_id );

	if( $string ) {
		$parse = $string;
	} else {
		$parse = $email->post_content;
	}

	$search = array(

		// Site Information
		'[site_name]',
		'[site_description]',
		'[home_url]',
		'[admin_email]',
		'[admin_url]',

		// Post
		'[post_id]',
		'[post_type]',
		'[post_author]',
		'[post_author_email]',
		'[post_date]',
		'[post_time]',
		'[post_modified_date]',
		'[post_modified_time]',
		'[post_title]',
		'[permalink]',
		'[old_status]',
		'[new_status]',
		'[edit_post_url]',

		// Post meta
		'[action]',
		'[to_emails]',
		'[from_email]',
		'[from_name]',
		'[cc_emails]',
		'[bcc_emails]'

	);
	$replace = array(

		// Site Information
		get_bloginfo( 'name' ),
		get_bloginfo( 'description' ),
		get_bloginfo( 'url' ),
		get_bloginfo( 'admin_email' ),
		get_admin_url(),

		// Post
		$post_id,
		get_post_type( $post_id ),
		get_the_author_meta( 'display_name', $post->post_author ),
		get_the_author_meta( 'email', $post->post_author ),
		get_the_date( '', $post_id ),
		get_the_time( '', $post_id ),
		get_the_modified_date( '', $post_id ),
		get_the_modified_time( '', $post_id ),
		get_the_title( $post_id ),
		get_permalink( $post_id ),
		$old_status,
		$new_status,
		get_edit_post_link( $post_id )		,

		// Post meta
		get_post_meta( $post_id, 'email_action', true ),
		get_post_meta( $post_id, 'to_emails', true ),
		get_post_meta( $post_id, 'from_email', true ),
		get_post_meta( $post_id, 'from_name', true ),
		get_post_meta( $post_id, 'cc_emails', true ),
		get_post_meta( $post_id, 'bcc_emails', true )
	);

	$parsed = str_replace( $search, $replace, $parse );

	return $parsed;
}