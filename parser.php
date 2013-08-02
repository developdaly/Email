<?php

function email_parser( $post_id, $email_id, $old_status, $new_status, $string = '' ) {

	$post = get_post( $post_id );
	$email = get_post( $email_id );

	if( $string ) {
		$parse = $string;
	} else {
		$parse = $email->post_content;
	}

	// Site information
	$parse = str_replace( '[site_title]', 				get_bloginfo( 'name' ), $parse );
	$parse .= str_replace( '[site_description]', 		get_bloginfo( 'description' ), $parse );
	$parse .= str_replace( '[home_url]', 				get_bloginfo( 'url' ), $parse );
	$parse .= str_replace( '[admin_email]', 			get_bloginfo( 'admin_email' ), $parse );
	$parse .= str_replace( '[admin_url]', 				get_admin_url(), $parse );

	// Post
	$parse .= str_replace( '[post_id]', 				$post_id, $parse );
	$parse .= str_replace( '[post_type]', 				get_post_type( $post_id ), $parse );
	$parse .= str_replace( '[post_author]', 			get_the_author_meta( 'display_name', $post->post_author ), $parse );
	$parse .= str_replace( '[post_author_email]', 		get_the_author_meta( 'email', $post->post_author ), $parse );
	$parse .= str_replace( '[permalink]', 				get_permalink( $post_id ), $parse );
	$parse .= str_replace( '[old_status]', 				$old_status, $parse );
	$parse .= str_replace( '[new_status]', 				$new_status, $parse );
	$parse .= str_replace( '[edit_post_url]', 			get_edit_post_link( $post_id ), $parse );

	// Post meta
	$parse .= str_replace( '[action]', 					get_post_meta( $post_id, 'email_action', true ), $parse );
	$parse .= str_replace( '[to_emails]', 				get_post_meta( $post_id, 'to_emails', true ), $parse );
	$parse .= str_replace( '[from_email]', 				get_post_meta( $post_id, 'from_email', true ), $parse );
	$parse .= str_replace( '[from_name]', 				get_post_meta( $post_id, 'from_name', true ), $parse );
	$parse .= str_replace( '[cc_emails]', 				get_post_meta( $post_id, 'cc_emails', true ), $parse );
	$parse .= str_replace( '[bcc_emails]', 				get_post_meta( $post_id, 'bcc_emails', true ), $parse );

	return $parse;
}