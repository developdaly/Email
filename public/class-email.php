<?php
/**
 * Email.
 *
 * @package   Email
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Patrick Daly
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-email-admin.php`
 *
 * @package Email
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class Email {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    2.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'email';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     2.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Register custom data
		add_action( 'init', array( $this, 'register' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    2.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.0.0
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
	 * Fired when the plugin is activated.
	 *
	 * @since    2.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    2.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    2.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    2.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    2.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    2.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Register the custom post types, taxonomies, statuses
	 *
	 * @since    1.0.0
	 */
	public function register() {

		$labels = array(
			'name' => _x( 'Emails', 'email' ),
			'singular_name' => _x( 'Email', 'email' ),
			'add_new' => _x( 'Add New', 'email' ),
			'add_new_item' => _x( 'Add New Email', 'email' ),
			'edit_item' => _x( 'Edit Email', 'email' ),
			'new_item' => _x( 'New Email', 'email' ),
			'view_item' => _x( 'View Email', 'email' ),
			'search_items' => _x( 'Search Emails', 'email' ),
			'not_found' => _x( 'No emails found', 'email' ),
			'not_found_in_trash' => _x( 'No emails found in Trash', 'email' ),
			'parent_item_colon' => _x( 'Parent Emails:', 'email' ),
			'menu_name' => _x( 'Emails', 'email' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array( 'title', 'editor', 'custom-fields' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post'
		);

		register_post_type( 'email', $args );

		$labels = array(
			'name' => _x( 'Email Logs', 'email' ),
			'singular_name' => _x( 'Email Log', 'email' ),
			'add_new' => _x( 'Add New', 'email' ),
			'add_new_item' => _x( 'Add New Email', 'email' ),
			'edit_item' => _x( 'Edit Email Log', 'email' ),
			'new_item' => _x( 'New Email Log', 'email' ),
			'view_item' => _x( 'View Email Log', 'email' ),
			'search_items' => _x( 'Search Emails Logs', 'email' ),
			'not_found' => _x( 'No email logs found', 'email' ),
			'not_found_in_trash' => _x( 'No email logs found in Trash', 'email' ),
			'parent_item_colon' => _x( 'Parent Email Logs:', 'email' ),
			'menu_name' => _x( 'Logs', 'email' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'supports' => array( 'title', 'editor', 'custom-fields' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => 'edit.php?post_type=email',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
		);

		register_post_type( 'email_log', $args );

		register_post_status( 'error', array(
			'label'			=> _x( 'Error', 'email' ),
			'public'		=> false,
			'label_count'	=> _n_noop( 'Errors <span class="count">(%s)</span>', 'Errors <span class="count">(%s)</span>' )
		) );

	}
}