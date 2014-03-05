<?php

function email_shortcode_add_subsciber_form() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-add-subscriber.php' );
	return ob_get_clean();
}
add_shortcode( 'email_add_subsciber_form', 'email_shortcode_add_subsciber_form' );