<?php

add_shortcode( 'home_url', 'email_shortcode_home_url');

function email_shortcode_home_url() {
     return get_home_url();
}

/*
$send_message = str_replace( '[site_title]', get_bloginfo( 'name' ), $send_message );
$send_message = str_replace( '[recipient]', $send_recipients_to	, $send_message );
$send_message = str_replace( '[post_type]', $send_type, $send_message );
$send_message = str_replace( '[action]', $send_action, $send_message );
$send_message = str_replace( '[permalink]', get_permalink( $post_id ), $send_message );
$send_message = str_replace( '[the_meta]', '<table>'. $meta_rows .'</table>', $send_message );
*/