<?php

function email_action_router( $new_status, $old_status, $post ) {

	// Verify the post is not autosaving
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	// Verify the post is not a revision
	if ( wp_is_post_revision( $post ) )
		return;

	// Get all emails and loop through each one to see if the meta matches
	// If there's a meta match, fire the action
	$emails = get_posts( array( 'post_type' => 'email', 'posts_per_page' => -1 ) );
	foreach( $emails as $email ) {

		$meta = get_post_meta( $email->ID );

		$email_action = get_post_meta( $email->ID, 'email_action', true );

		if ( ($new_status != $old_status) && ( $email_action == 'new' ) && ( 'new' == $new_status ) ) {
			email_action( 'new', $post->ID, $email->ID );
		}

		if ( ($new_status == $old_status) &&  ( $email_action == 'updated' ) ) {
			email_action( 'updated', $post->ID, $email->ID, $old_status, $new_status );
		}

	}
}

function email_action( $action, $post_id, $email_id, $old_status, $new_status ) {

	$post = get_post( $post_id );

	$email_action 		= get_post_meta( $email_id, 'email_action', true );
	$email_type 		= get_post_meta( $email_id, 'email_type', true );
	$email_from 		= get_post_meta( $email_id, 'email_from', true );
	$email_from_name	= get_post_meta( $email_id, 'email_from_name', true );
	$email_to 			= get_post_meta( $email_id, 'email_to', true );
	$email_to_role 		= get_post_meta( $email_id, 'email_to_role', true );
	$email_cc 			= get_post_meta( $email_id, 'email_cc', true );
	$email_cc_role 		= get_post_meta( $email_id, 'email_cc_role', true );
	$email_bcc 			= get_post_meta( $email_id, 'email_bcc', true );
	$email_bcc_role 	= get_post_meta( $email_id, 'email_bcc_role', true );
	$email_subject 		= get_post_meta( $email_id, 'email_subject', true );
	$email_message 		= get_post_meta( $email_id, 'email_message', true );
	$email_hidden 		= get_post_meta( $email_id, 'email_hidden', true );

	// Build the comma separated list of email address for the TO field
	$users_to_list = '';
	if( !empty( $email_to_role ) ) {
		$users_to = get_users( array( 'role' => $email_to_role ) );
		foreach( $users_to as $user_to ) {
			$users_to_list .= $user_to->user_email .', ';
		}
	}
	if( !empty( $email_to ) ) {
		$users_to_list .= $email_to;
	}

	// Build the comma separated list of email address for the CC field
	$users_cc_list = '';
	if( !empty( $email_cc_role ) ) {
		$users_cc = get_users( array( 'role' => $email_cc_role ) );
		foreach( $users_cc as $user_cc ) {
			$users_cc_list .= $user_cc->user_email .', ';
		}
	}
	if( !empty( $email_cc ) ) {
		$users_cc_list .= $email_cc;
	}

	// Build the comma separated list of email address for the BCC field
	$users_bcc_list = '';
	if( !empty( $email_bcc_role ) ) {
		$users_bcc = get_users( array( 'role' => $email_bcc_role ) );
		foreach( $users_bcc as $user_bcc ) {
			$users_bcc_list .= $user_bcc->user_email .', ';
		}
	}
	if( !empty( $email_bcc ) ) {
		$users_bcc_list .= $email_bcc;
	}

	if( isset( $email_from_address ) && isset( $email_from ) )
		$headers[] = 'From: '. $email_from_name .' <'. $email_from .'>';

	if( isset( $users_cc_list ))
		$headers[] = 'Cc: '. $users_cc_list;

	if( isset( $users_cc_list ))
		$headers[] = 'Bcc: '. $users_bcc_list;

	$parsed_message = email_parser( $post_id, $email_id, $old_status, $new_status );
	$parsed_subject = email_parser( $post_id, $email_id, $old_status, $new_status, $email_subject );

	$mail = wp_mail( $users_to_list, $parsed_subject, $parsed_message, $headers );

	if( $mail ) {

		$args = array(
			'post_title'	=> $parsed_subject,
			'post_type'		=> 'email_log',
			'post_content'  => $parsed_message
		);
		$log_id = wp_insert_post( $args );

		foreach( $headers as $key => $val ) {
			update_post_meta( $log_id, $key, $val );
		}
	}

	return $mail;

}