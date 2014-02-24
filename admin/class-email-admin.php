<?php
/**
 * Email.
 *
 * @package   Email_Admin
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Patrick Daly
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-email.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
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
		
		add_action( 'transition_post_status',		array( $this, 'action_router', 10, 3 ) );
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
	 * Create the email post.
	 *
	 * @since    1.0.0
	 */
	public function insert_post() {

		// variables for the field and option names
		$email_action 	= (isset($_POST['email_action']) 	? $_POST['email_action'] : '');
		$email_type 	= (isset($_POST['email_type']) 		? $_POST['email_type'] : '');
		$email_from 	= (isset($_POST['email_from']) 		? $_POST['email_from'] : '');
		$email_from_name = (isset($_POST['email_from_name']) ? $_POST['email_from_name'] : '');
		$email_to 		= (isset($_POST['email_to']) 		? $_POST['email_to'] : '');
		$email_to_role 	= (isset($_POST['email_to_role']) 	? $_POST['email_to_role'] : '');
		$email_cc 		= (isset($_POST['email_cc']) 		? $_POST['email_cc'] : '');
		$email_cc_role 	= (isset($_POST['email_cc_role']) 	? $_POST['email_cc_role'] : '');
		$email_bcc 		= (isset($_POST['email_bcc']) 		? $_POST['email_bcc'] : '');
		$email_bcc_role = (isset($_POST['email_bcc_role']) 	? $_POST['email_bcc_role'] : '');
		$email_subject 	= (isset($_POST['email_subject']) 	? $_POST['email_subject'] : '');
		$email_message 	= (isset($_POST['email_message']) 	? $_POST['email_message'] : '');
		$email_hidden 	= (isset($_POST['email_hidden']) 	? $_POST['email_hidden'] : '');

		if( isset($email_hidden) && ( $email_hidden == 'Y' ) ) {

			$errors = array();
			$success = array();

			if( empty( $email_type ) ) {
				$errors[] = '<strong>ERROR</strong>: You must set a <strong>type</strong>.';
			}

			if( empty( $email_action ) ) {
				$errors[] = '<strong>ERROR</strong>: You must set an <strong>action</strong>.';
			}

			if( empty( $email_subject ) ) {
				$errors[] = '<strong>ERROR</strong>: You must set a <strong>subject</strong>.';
			}

			if( empty( $email_message ) ) {
				$errors[] = '<strong>ERROR</strong>: You must set a <strong>message</strong>.';
			}
			if( empty( $email_from ) ) {
				$errors[] = '<strong>ERROR</strong>: You must set a <strong>From email address</strong>.';
			}
			if( empty( $email_from_name ) ) {
				$errors[] = '<strong>ERROR</strong>: You must set a <strong>From name</strong>.';
			}
			if( empty( $email_to ) && empty( $email_to_role )) {
				$errors[] = '<strong>ERROR</strong>: You must set the <strong>To field</strong>.';
			}

			$args = array(
				'post_type' => 'email',
				'meta_query' => array(
					array(
						'key' => 'email_action',
						'value' => $email_action
					),
					array(
						'key' => 'email_type',
						'value' => $email_type
					),
					array(
						'key' => 'email_from',
						'value' => $email_from
					),
					array(
						'key' => 'email_from_name',
						'value' => $email_from_name
					),
					array(
						'key' => 'email_to',
						'value' => $email_to
					),
					array(
						'key' => 'email_to_role',
						'value' => $email_to_role
					),
					array(
						'key' => 'email_cc',
						'value' => $email_cc
					),
					array(
						'key' => 'email_cc_role',
						'value' => $email_cc_role
					),
					array(
						'key' => 'email_bcc',
						'value' => $email_bcc
					),
					array(
						'key' => 'email_bcc_role',
						'value' => $email_bcc_role
					),
					array(
						'key' => 'email_subject',
						'value' => $email_subject
					),
					array(
						'key' => 'email_message',
						'value' => $email_message
					)
				)
			);

			$emails = get_posts( $args );

			if( isset( $emails ) ) {

				if( !empty( $emails ) ) {

					foreach( $emails as $email ) {
						$errors[] = '<a href="'. get_edit_post_link( $email->ID ) .'">Email configuration</a> already exists.';
					}

				}
			}

			if( !empty( $errors ) ) {
				$output = '<div class="error">';
				if( $errors ) {
					$output .= '<ul>';
					foreach( $errors as $error ) {
						$output .= '<li>'. $error .'</li>';
					}
					$output .= '</ul>';
				}
				$output .= '</div>';
				return $output;
			}

			$title = $email_type .' > '. $email_action;

			// Create post object
			$post = array(
				'post_title'	=> wp_strip_all_tags( $title ),
				'post_content'	=> $email_message,
				'post_status'	=> 'publish',
				'post_type'		=> 'email'
			);

			// Insert the post into the database
			$post_id = wp_insert_post( $post );
			if ( !empty( $email_action ) ) {
				update_post_meta( $post_id, 'email_action', $email_action );
				$success[] = $email_action;
			}
			if ( !empty( $email_type ) ) {
				update_post_meta( $post_id, 'email_type', $email_type );
				$success[] = $email_type;
			}
			if ( !empty( $email_from ) ) {
				update_post_meta( $post_id, 'email_from', $email_from );
				$success[] = $email_from;
			}
			if ( !empty( $email_from_name ) ) {
				update_post_meta( $post_id, 'email_from_name', $email_from_name );
				$success[] = $email_from_name;
			}
			if ( !empty( $email_to ) ) {
				update_post_meta( $post_id, 'email_to', $email_to );
				$success[] = $email_to;
			}
			if ( !empty( $email_to_role ) ) {
				update_post_meta( $post_id, 'email_to_role', $email_to_role );
				$success[] = $email_to_role;
			}
			if ( !empty( $email_cc ) ) {
				update_post_meta( $post_id, 'email_cc', $email_cc );
				$success[] = $email_cc;
			}
			if ( !empty( $email_cc_role ) ) {
				update_post_meta( $post_id, 'email_cc_role',	$email_cc_role );
				$success[] = $email_cc_role;
			}
			if ( !empty( $email_bcc ) ) {
				update_post_meta( $post_id, 'email_bcc', $email_bcc );
				$success[] = $email_bcc;
			}
			if ( !empty( $email_bcc_role ) ) {
				update_post_meta( $post_id, 'email_bcc_role',$email_bcc_role );
				$success[] = $email_bcc_role;
			}
			if ( !empty( $email_subject ) ) {
				update_post_meta( $post_id, 'email_subject',	$email_subject );
				$success[] = $email_subject;
			}

			$success[] = 'Edit <a href="'. get_edit_post_link( $post_id ) .'">'. get_the_title( $post_id ) .'</a>';

			if( !empty( $post_id ) ) {
				$output = '<div class="updated">';
				if( $success ) {
					$output .= '<ul>';
					foreach( $success as $item ) {
						$output .= '<li>'. $item .'</li>';
					}
					$output .= '</ul>';
				}
				$output .= '</div>';
				return $output;
			}

		}

	}

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
	
}
