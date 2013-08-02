<?php

function email_parse_message( $post_id, $old_status, $new_status ) {

	$post 			= get_post( $post_id );
	$author 		= get_the_author( $post_id );

	$ouput = $post->post_content;

	// Site information
	$output .= str_replace( '[site_title]', 			get_bloginfo( 'name' ), $message );
	$output .= str_replace( '[site_description]', 		get_bloginfo( 'description' ), $message );
	$output .= str_replace( '[home_url]', 				get_bloginfo( 'url' ), $message );
	$output .= str_replace( '[admin_email]', 			get_bloginfo( 'admin_email' ), $message );
	$output .= str_replace( '[admin_url]', 				get_admin_url(), $message );

	// Post
	$output .= str_replace( '[post_id]', 				$post_id, $message );
	$output .= str_replace( '[post_type]', 				get_post_type( $post_id ), $message );
	$output .= str_replace( '[post_author]', 			get_the_author( $post_id ), $message );
	$output .= str_replace( '[post_author_email]', 		get_the_author_meta( 'email', $author->ID ), $message );
	$output .= str_replace( '[permalink]', 				get_permalink( $post_id ), $message );
	$output .= str_replace( '[old_status]', 			$old_status, $message );
	$output .= str_replace( '[new_status]', 			$new_status, $message );
	$output .= str_replace( '[edit_post_url]', 			get_edit_post_link( $post_id ), $message );

	// Post meta
	$output .= str_replace( '[the_meta]', 				get_post_meta( $post_id ), $message );
	$output .= str_replace( '[action]', 				get_post_meta( $post_id, 'email_action', true ), $message );
	$output .= str_replace( '[to_emails]', 				get_post_meta( $post_id, 'to_emails', true ), $message );
	$output .= str_replace( '[from_email]', 			get_post_meta( $post_id, 'from_email', true ), $message );
	$output .= str_replace( '[from_name]', 				get_post_meta( $post_id, 'from_name', true ), $message );
	$output .= str_replace( '[cc_emails]', 				get_post_meta( $post_id, 'cc_emails', true ), $message );
	$output .= str_replace( '[bcc_emails]', 			get_post_meta( $post_id, 'bcc_emails', true ), $message );

}