<?php
/**
 * Email.
 *
 * @package   Email_Admin
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/email/
 * @copyright 2014 Patrick Daly
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-email.php`
 *
 * @package Email_Admin
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class Email_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = 'email_page_email';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since    2.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 */
		$plugin = Email::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		
		add_action( 'transition_post_status',		array( $this, 'action_router' ), 10, 3 );
		add_action( 'admin_head',					array( $this, 'menu_icon' ) );
		add_action( 'wp_ajax_email_get_users',		array( $this, 'get_users' ) );
		add_action( 'wp_ajax_email_get_template',	array( $this, 'get_template' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since    2.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( 'chosen',	plugins_url( 'assets/chosen/chosen.css', __DIR__ ), array(), Email::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since    2.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {			
			wp_enqueue_script( 'chosen',								plugins_url( 'assets/chosen/chosen.jquery.min.js', __DIR__ ), array(), Email::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-script',	plugins_url( 'assets/js/'. $this->plugin_slug . '-admin-script.js', __DIR__ ), array(), Email::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    2.0.0
	 */
	public function add_plugin_admin_menu() {

		remove_submenu_page('edit.php?post_type='. $this->plugin_slug, 'post-new.php?post_type='. $this->plugin_slug );
		add_submenu_page( 'edit.php?post_type='. $this->plugin_slug, 'Add New Email', 'Add New', 'update_core', 'email', array( $this, 'display_plugin_admin_page' ) );
		
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    2.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    2.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Add menu icon for the Email menu item.
	 *
	 * @since    1.0.0
	 */
	public function menu_icon() {
		?>
		<style type="text/css" media="screen">
			#menu-posts-email .wp-menu-image {
				background: url('<?php echo plugin_dir_url( __DIR__ ); ?>assets/images/mail.png') no-repeat 6px -17px !important;
			}
			#menu-posts-email:hover .wp-menu-image, #menu-posts-email.wp-has-current-submenu .wp-menu-image {
				background-position:6px 7px!important;
			}
		</style>
	<?php }

	/**
	 * Get a commma separated list of users.
	 *
	 * @since    1.0.0
	 */
	public function get_users() {
		global $wpdb;
		$role = $_POST['role'];

		$users = get_users( array( 'role' => $role ) );

		if( empty( $users ) ) {
			echo '';
			die();
		} else {

			$prefix = '';
			$users_list = '';
			foreach( $users as $user ) {
				$users_list .= $prefix . $user->data->user_email;
				$prefix = ', ';
			}

			echo $users_list;

		}

		die();
	}
	
	/**
	 * Get an email template.
	 *
	 * @since    1.0.0
	 */
	public function get_template() {
		$action = $_POST['givenAction'];

		echo email_template( $action[0] );

		die();
	}

	/**
	 * Send the mail.
	 *
	 * @since    1.0.0
	 */
	public function email_action( $action, $post_id, $email_id, $old_status, $new_status ) {
		
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
		
		if( $email_to == '[subscribed]' ) {
			$users_to_list = self::email_get_subscribed( $post_id );
		}
		
		if( $email_cc == '[subscribed]' ) {
			$users_cc_list = self::email_get_subscribed( $post_id );
		}
		
		if( $email_bcc == '[subscribed]' ) {
			$users_bcc_list = self::email_get_subscribed( $post_id );
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

		$parsed_message = $this->email_parser( $post_id, $email_id, $old_status, $new_status );
		$parsed_subject = $this->email_parser( $post_id, $email_id, $old_status, $new_status, $email_subject );

		$mail = wp_mail( $users_to_list, $parsed_subject, $parsed_message, $headers );

		// Log successful email
		if( $mail ) {

			$args = array(
				'post_content'  => $parsed_message,
				'post_status'	=> 'private',
				'post_title'	=> $parsed_subject,
				'post_type'		=> 'email_log'
			);
			$log_id = wp_insert_post( $args );

			foreach( $headers as $key => $val ) {
				update_post_meta( $log_id, $key, $val );
			}
		} else {
			// Log unsuccessful email
			$args = array(
				'post_content'  => $parsed_message,
				'post_status'	=> 'error',
				'post_title'	=> '[FAILED to send "'. $action .'" email] '. $parsed_subject,
				'post_type'		=> 'email_log'
			);
			$log_id = wp_insert_post( $args );

			foreach( $headers as $key => $val ) {
				update_post_meta( $log_id, $key, $val );
			}
		}

		return $mail;

	}

	/**
	 * Route the email to the right given action.
	 *
	 * @since    1.0.0
	 */
	public function action_router( $new_status, $old_status, $post ) {

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
			$email_type = get_post_meta( $email->ID, 'email_type', true );

			if( $email_type != $post->post_type )
				continue;

			if ( ($new_status != $old_status) && ( $email_action == 'new' ) && ( 'publish' == $new_status ) ) {
				$this->email_action( 'new', $post->ID, $email->ID, $old_status, $new_status );
			}

			elseif ( ($new_status == $old_status) &&  ( $email_action == 'updated' ) ) {
				$this->email_action( 'updated', $post->ID, $email->ID, $old_status, $new_status );
			}

			elseif ( ($new_status != $old_status) && ( $email_action == 'deleted' ) && ( 'trash' == $new_status ) ) {
				$this->email_action( 'deleted', $post->ID, $email->ID, $old_status, $new_status );
			}

		}
	}
	
	/**
	 * Parses an email template and returns the results.
	 *
	 * @since    1.0.0
	 */
	public function email_parser( $post_id, $email_id, $old_status, $new_status, $string = '' ) {

		$post = get_post( $post_id );
		$email = get_post( $email_id );

		if( $string ) {
			$parse = $string;
		} else {
			$parse = $email->post_content;
		}
		
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		$gmt = get_option('gmt_offset');
		
		$last_modified_author_id = get_post_meta($post->ID, '_edit_last', true );
		$last_modified_author_display_name = get_the_author_meta( 'display_name', $last_modified_author_id );
		
		$search = array(

			// Site Information
			'[site_name]',
			'[site_description]',
			'[home_url]',
			'[admin_email]',
			'[admin_url]',

			// Post
			'[post_id]',
			'[post_type]',
			'[post_author]',
			'[post_author_email]',
			'[post_date]',
			'[post_time]',
			'[post_modified_author]',
			'[post_modified_date]',
			'[post_modified_time]',
			'[post_title]',
			'[post_content]',
			'[permalink]',
			'[old_status]',
			'[new_status]',
			'[edit_post_url]',

			// Post meta
			'[action]',
			'[to_emails]',
			'[from_email]',
			'[from_name]',
			'[cc_emails]',
			'[bcc_emails]',
			'[subscribed]'

		);
		$replace = array(

			// Site Information
			get_bloginfo( 'name' ),
			get_bloginfo( 'description' ),
			get_bloginfo( 'url' ),
			get_bloginfo( 'admin_email' ),
			get_admin_url(),

			// Post
			$post_id,
			get_post_type( $post_id ),
			get_the_author_meta( 'display_name', $post->post_author ),
			get_the_author_meta( 'user_email', $post->post_author ),
			get_post_time( $date_format, $gmt, $post_id ),
			get_post_time( $time_format, $gmt, $post_id ),
			$last_modified_author_display_name,
			get_post_modified_time( $date_format, $gmt, $post_id ),
			get_post_modified_time( $time_format, $gmt, $post_id ),
			get_the_title( $post_id ),
			get_post_field( 'post_content', $post_id ),
			get_permalink( $post_id ),
			$old_status,
			$new_status,
			get_edit_post_link( $post_id ),

			// Post meta
			get_post_meta( $post_id, 'email_action', true ),
			get_post_meta( $post_id, 'to_emails', true ),
			get_post_meta( $post_id, 'from_email', true ),
			get_post_meta( $post_id, 'from_name', true ),
			get_post_meta( $post_id, 'cc_emails', true ),
			get_post_meta( $post_id, 'bcc_emails', true ),
			$this->email_get_subscribed( $post_id )
		);

		$parsed = str_replace( $search, $replace, $parse );

		return $parsed;
	}
	
	public function email_get_subscribed( $post_id ) {
		
		$output = array();
		
		$subscribers = get_post_meta( $post_id, '_email_subscribers' );
		
		// check if the custom field has a value
		if( ! empty( $subscribers[0] ) ) {
			foreach( $subscribers[0] as $subscriber ) {
				$output[] = $subscriber['email_address'];
			}
			$output = implode(', ', $output);
		} else {
			$output = '';
		}
		
		return $output;
	
	}
	
}
