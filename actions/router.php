<?php

function email_action_router( $new_status, $old_status, $post ) {
    if ( $new_status != $old_status ) {

		$emails = get_posts( array( 'post_type' => 'email' ) );
		foreach( $emails as $email ) {

			$email_action = get_post_meta( $email->ID, 'email_action', true );

			if ( ( $email_action == 'new' ) && ( 'new' == $new_status ) ) {
				email_action( 'new', $post );
			}

			if ( ( $email_action == 'updated' ) && ( 'updated' == $new_status ) ) {
				email_action( 'updated', $post );
			}

		}
    }
}