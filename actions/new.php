<?php

function email_action( $action, $post ) {

	// Verify post is not autosaving
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	//verify post is not a revision
	if ( wp_is_post_revision( $post_id ) )
		return;


	if( $action == 'new' ) {

		ob_start();
		include 'templates/new.php';
		$message = ob_get_contents();

		$mail = wp_mail( $recipient_emails, $subject, $message );

		ob_end_clean();
	}

}