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
			error_log( 'new action' );
		}

		if ( ($new_status == $old_status) &&  ( $email_action == 'updated' ) ) {
			email_action( 'updated', $post->ID, $email->ID );
			error_log( 'updated action' );
		}

	}
}

function email_action( $action, $post_id, $email_id ) {

	$post = get_post( $post_id );
	$email_from 		= get_post_meta( $email_id, 'email_from', true );
	$email_from_address	= get_post_meta( $email_id, 'email_from_address' , true );
	$email_to			= get_post_meta( $email_id, 'email_to' , true );
	$email_subject		= get_post_meta( $email_id, 'email_subject' , true );

	$headers[] = 'From: '. $email_from .' <'. $email_from_address .'>';
	$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
	$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address

	$mail = wp_mail( $email_to, $email_subject, $post->post_content );

	return $mail;

}