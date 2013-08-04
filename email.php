<?php
/*
 * Plugin Name: Email
 * Description: Email users with custom templates when certain actions happen, such as new posts, updated custom post types, deleted users.
 * Author: developdaly
 * Version: 1.0.1
 * Author URI: http://developdaly.com/
 * Text Domain: email
 *
 * mail.png icon by Yusuke Kamiyamane (http://p.yusukekamiyamane.com/)
 * used under Creative Commons 3.0 http://creativecommons.org/licenses/by/3.0/
 */

define( 'EMAIL_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

require_once( EMAIL_DIR . 'admin.php' );
require_once( EMAIL_DIR . 'templates.php' );
require_once( EMAIL_DIR . 'router.php' );
require_once( EMAIL_DIR . 'parser.php' );

add_action( 'init',							'email_register' );
add_action( 'transition_post_status',		'email_action_router', 10, 3 );
add_action( 'admin_head',					'email_menu_icon' );
add_action( 'admin_menu',					'email_add_menu' );
add_action( 'admin_enqueue_scripts',		'email_enqueue_scripts' );
add_action( 'wp_ajax_email_get_users',		'email_get_users' );
add_action( 'wp_ajax_email_get_template',	'email_get_template' );