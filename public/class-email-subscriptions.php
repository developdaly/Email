<?php
/**
 * Email.
 *
 * @package   Email_Subscriptions
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
 * @package Email_Subscriptions
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class Email_Subscriptions {

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
	protected $plugin_screen_hook_suffix = 'email_page_subscriptions';

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

		// Load JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		if ( is_admin() && ( defined('DOING_AJAX') && DOING_AJAX ) ) {
			add_action( 'wp_ajax_nopriv_email_subscriptions',	array( $this, 'go' ) );
			add_action( 'wp_ajax_email_subscriptions',			array( $this, 'go' ) );
		} else {
			add_action( 'init',	array( $this, 'go' ), 11 );
		}
		
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
	 * This is the first function in the task process. It will route actions
	 * based on the controller value.
	 *
	 * First checks if the user is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function go() {
					
		if ( !empty( $_POST ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {

			if( isset( $_POST['action'] ) && $_POST['action'] == 'email_subscriptions' ) {

				if( $_POST['controller'] == 'email_subscribe' ) {
					$result = self::add_subscriber($_POST);
				}
				if( $_POST['controller'] == 'email_unsubscribe' ) {
					$result = self::unsubscribe($_POST);
				}
				// If this is an ajax request
				if ( defined('DOING_AJAX') && DOING_AJAX ) {
					if ( isset( $result ) ) {
						die( json_encode( $result ) );
					} else {
						die(
							json_encode(
								array(
									'success' => false,
									'message' => __( 'An error occured. Please refresh the page and try again.' )
								)
							)
						);
					}
				}
			}
		}
	}
	
	/**
	 * Register and enqueue JavaScript.
	 *
	 * @since    2.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script',	plugins_url( 'public/assets/js/public.js', __DIR__ ), array( 'jquery' ), Email::VERSION );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'email', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' )
		) );

	}

	/**
	 * Inserts a new subscription.
	 *
	 * @since    2.0.0
	 */

	public function add_subscriber( $data ) {
		if( empty( $data ) ) {
			return;
		}

		// If the current user can't publish posts stop
		if ( !current_user_can('read') ) {
			$output = 'You don\'t have proper permissions to subscibe. :(';
			return $output;
		}

		if( isset( $data['email-subscriber-address'] ) ) {
			$email_address = $data['email-subscriber-address'];
		} else {
			$email_address = false;
		}
		if( isset( $data['post-id'] ) ) {
			$post_id = $data['post-id'];
		} else {
			$post_id = false;
		}
		
		if( $post_id && $email_address ) {

			$value = get_post_meta( $post_id, '_email_subscribers', true );

			$new_value = array(
				'email_address' => $email_address,
				'timestamp' => time ()
			);
			
			if( is_array( $value ) ) {
				$value[] = $new_value;
			} else {
				$value = array($new_value);
			}
			
			$updated = update_post_meta( $post_id, '_email_subscribers', $value );
			
		} else {
			$output = 'The post ID or email address were not provided';
			return $output;
		}

		// If the task inserted succesffully
		if ( $updated != false ) {

			$post = get_post( $post_id );
			$subscribers = get_post_meta( $post_id, '_email_subscribers', true );

			$output = array(
				'status'		=> 'success',
				'message'		=> __('Success!'),
				'post'			=> $post,
				'subscribers'	=> $subscribers
			);

		} else {
			$output = 'There was an error while creating a new subscription. Please refresh the page and try again.';
		}

		return $output;
	}

	/**
	 * Inserts a new subscription.
	 *
	 * @since    2.0.0
	 */

	public function unsubscribe( $data ) {
		if( empty( $data ) ) {
			return;
		}

		// If the current user can't publish posts stop
		if ( !current_user_can('read') ) {
			$output = 'You don\'t have proper permissions to subscibe. :(';
			return $output;
		}

		if( isset( $data['email_addresses'] ) ) {
			$email_addresses = $data['email_addresses'];
		} else {
			$email_addresses = false;
		}
		if( isset( $data['post-id'] ) ) {
			$post_id = $data['post-id'];
		} else {
			$post_id = false;
		}

		if( $post_id && $email_addresses ) {

			$subscribers = get_post_meta( $post_id, '_email_subscribers', true );

			foreach( $email_addresses as $email_address ) {
				$key = array_searchRecursive( $email_address, $subscribers );
				unset($subscribers[$key[0]]);
			}
			
			$updated = update_post_meta( $post_id, '_email_subscribers', $subscribers );
			
		} else {
			$output = 'The post ID or email address were not provided';
			return $output;
		}

		// If the task inserted succesffully
		if ( $updated != false ) {

			$post = get_post( $post_id );
			$subscribers = get_post_meta( $post_id, '_email_subscribers', true );

			$output = array(
				'status'		=> 'success',
				'message'		=> __('Success!'),
				'post'			=> $post,
				'subscribers'	=> $subscribers
			);

		} else {
			$output = 'There was an error while unsubscribing that email address. Please refresh the page and try again.';
		}

		return $output;
	}
}

function array_searchRecursive( $needle, $haystack, $strict=false, $path=array() )
{
    if( !is_array($haystack) ) {
        return false;
    }
 
    foreach( $haystack as $key => $val ) {
        if( is_array($val) && $subPath = array_searchRecursive($needle, $val, $strict, $path) ) {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
            $path[] = $key;
            return $path;
        }
    }
    return false;
}
