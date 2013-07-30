<?php
/*
 * Plugin Name: Email
 * Description: Email users with custom templates when certain actions happen, such as new posts, updated custom post types, deleted users.
 * Author: developdaly
 * Version: 0.1
 * Author URI: http://developdaly.com/
 */

require_once( WP_PLUGIN_DIR .'/email/admin.php' );
require_once( WP_PLUGIN_DIR .'/email/actions/router.php' );
require_once( WP_PLUGIN_DIR .'/email/actions/new.php' );
require_once( WP_PLUGIN_DIR .'/email/templates/new.php' );

add_action( 'init',						'email_register' );
add_action( 'transition_post_status',	'email_action_router', 10, 3 );
add_action( 'admin_menu',				'email_add_menu' );
add_action( 'admin_enqueue_scripts',	'email_enqueue_scripts' );