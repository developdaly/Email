<?php

function email_register() {

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
		'menu_name' => _x( 'Email Logs', 'email' ),
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

	register_post_type( 'email_log', $args );

}

// Add page as submenu to Tools
function email_add_menu() {
	remove_submenu_page('edit.php?post_type=email', 'post-new.php?post_type=email');
	add_submenu_page( 'edit.php?post_type=email', 'Add New', 'Add New', 'update_core', 'email/emails.php', 'email_add_menu_page_callback' );
}

// Load scripts and styles
function email_enqueue_scripts() {
	wp_enqueue_style( 'chosen',					plugins_url( '/assets/chosen.css', __FILE__ ) );
	wp_enqueue_style( 'jquery-ui-datepicker',	plugins_url( '/assets/jquery-ui-1.9.2.custom.min.css', __FILE__ ) );

	wp_enqueue_script( 'chosen',				plugins_url( '/assets/jquery.chosen.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'jquery-ui-timepicker',	plugins_url( '/assets/jquery.timepicker.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker' ) );
	wp_enqueue_script( 'app',					plugins_url( '/assets/app.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-timepicker' ) );
}

// Display the admin page
function email_add_menu_page_callback() {

	echo email_insert_post();

	// Must be Editor to access the settings page.
	if ( !current_user_can( 'edit_posts' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$current_user = wp_get_current_user();

	$send_options = get_option( 'email_settings' );

	// variables for the field and option names
	$email_action	= 'email_action';
	$email_type		= 'email_type';
	$email_from		= 'email_from';
	$email_to		= 'email_to';
	$email_to_role	= 'email_to_role';
	$email_cc		= 'email_cc';
	$email_cc_role	= 'email_cc_role';
	$email_bcc		= 'email_bcc';
	$email_bcc_role	= 'email_bcc_role';
	$email_subject	= 'email_subject';
	$email_message	= 'email_message';
	$email_hidden	= 'email_hidden';

	?>

	<div class="wrap">

		<h2><?php echo __( 'Add New Email', 'send' ) ?></h2>

		<form name="send" method="post" action="">

			<?php

				$actions = array( 'new', 'updated' );
				$types = get_post_types();

			?>

			<p>An <strong>email event</strong> is a set a conditions set below that will trigger an email to be sent.</p>

			<table class="form-table">
				<tbody>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_type; ?>">Post Type</label></th>
						<td>
							<select id="<?php echo $email_type; ?>" name="<?php echo $email_type; ?>" class="chosen-select" multiple="multiple" data-placeholder="Choose post types..." style="width: 50%">
								<?php foreach( $types as $key => $value ) {
									echo '<option val="'. $key .'">'. $value .'</option>';
								}; ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_action; ?>">Action</label></th>
						<td>
							<select id="<?php echo $email_action; ?>" name="<?php echo $email_action; ?>" class="chosen-select" multiple="multiple" data-placeholder="Choose actions..." style="width: 50%">
								<?php foreach( $actions as $key => $value ) {
									echo '<option val="'. $key .'">'. $value .'</option>';
								}; ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_from; ?>">From</label></th>
						<td>
							<input type="text" id="<?php echo $email_from; ?>" name="<?php echo $email_from; ?>" style="width: 50%" value="<?php echo $current_user->user_email; ?>" placeholder="The email address to send from">
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_to_role; ?>">To</label></th>
						<td>
							<select id="<?php echo $email_to_role; ?>" name="<?php echo $email_to_role; ?>" class="chosen-select select-role" data-placeholder="Choose a role (optional)" style="width: 25%">
								<option></option>
								<?php wp_dropdown_roles(); ?>
							</select>
							<input type="text" id="<?php echo $email_to; ?>" name="<?php echo $email_to; ?>" style="width: 70%" placeholder="Additional email addresses">
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_cc_role; ?>">CC</label></th>
						<td>
							<select id="<?php echo $email_cc_role; ?>" name="<?php echo $email_cc_role; ?>" class="chosen-select select-role" data-placeholder="Choose a role (optional)" style="width: 25%">
								<option></option>
								<?php wp_dropdown_roles(); ?>
							</select>
							<input type="text" id="<?php echo $email_cc; ?>" name="<?php echo $email_cc; ?>" style="width: 70%" placeholder="Additional email addresses">
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_bcc_role; ?>">BCC</label></th>
						<td>
							<select id="<?php echo $email_bcc_role; ?>" name="<?php echo $email_bcc_role; ?>" class="chosen-select select-role" data-placeholder="Choose a role (optional)" style="width: 25%">
								<option></option>
								<?php wp_dropdown_roles(); ?>
							</select>
							<input type="text" id="<?php echo $email_bcc; ?>" name="<?php echo $email_bcc; ?>" style="width: 70%" placeholder="Additional email addresses">
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_subject; ?>">Subject</label></th>
						<td>
							<input type="text" name="<?php echo $email_subject; ?>" style="width: 50%" value="[[site_title]] [post_title] [action]"> Example: "[My Site] Hello World! updated"
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_message; ?>">Message</label></th>
						<td>

							<p>See the Glossary of Shortcodes available for use in this template.</p>

							<?php wp_editor( false, $email_message, array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => false ) ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"></th>
						<td>
							<input type="hidden" name="<?php echo $email_hidden; ?>" value="Y">
							<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Setup Email') ?>" />
						</td>
					</tr>

				</tbody>
			</table>

		</form>

		<h2>Glossary of Shortcodes</h2>

		<dl>

			<dt><code>[action]</code></dt>
			<dd>The type of action that took place such as "new", "updated", "deleted".</dd>

			<dt><code>[post_type]</code></dt>
			<dd>The type of post being referred to, such as "post", "page", or a custom post type.</dd>

			<dt><code>[permalink]</code></dt>
			<dd>The link to the post.</dd>

			<dt><code>[the_meta]</code></dt>
			<dd>An unordered list of all post meta attached to the post.</dd>

			<dt><code>[to_emails]</code></dt>
			<dd>A comma separated list of email addresses in the "To" field.</dd>

			<dt><code>[from_email]</code></dt>
			<dd>The name of the sender.</dd>

			<dt><code>[from_name]</code></dt>
			<dd>The email addresss of the sender.</dd>

			<dt><code>[to_names]</code></dt>
			<dd>A comma separated list of names associated with the addresses in the "To" field.</dd>

			<dt><code>[cc_emails]</code></dt>
			<dd>A comma separated list of email addresses in the "CC" field.</dd>

			<dt><code>[cc_names]</code></dt>
			<dd>A comma separated list of names associated with the addresses in the "CC" field.</dd>

			<dt><code>[bcc_emails]</code></dt>
			<dd>A comma separated list of email addresses in the "BCC" field.</dd>

			<dt><code>[bcc_names]</code></dt>
			<dd>A comma separated list of names associated with the addresses in the "BCC" field.</dd>

			<dt><code>[site_title]</code></dt>
			<dd>The title of this site.</dd>

			<dt><code>[home_url]</code></dt>
			<dd>The home page URL of this site.</dd>

			<dt><code>[admin_url]</code></dt>
			<dd>The URL of this site's WordPress dashboard.</dd>

			<dt><code>[old_status]</code></dt>
			<dd>The old, or previous, status of the post.</dd>

			<dt><code>[new_status]</code></dt>
			<dd>The new, or updated, satus of the post.</dd>

			<dt><code>[edit_post_url]</code></dt>
			<dd>A direct link to the edit screen of the post.</dd>

			<dt><code>[post_id]</code></dt>
			<dd>The ID of the post.</dd>

			<dt><code>[post_author]</code></dt>
			<dd>The display name of the author of the post.</dd>

			<dt><code>[post_author_email]</code></dt>
			<dd>The email address of the author of the post.</dd>

			<dt><code>[post_modified_author]</code></dt>
			<dd>The display name of the author that modified the post.</dd>

			<dt><code>[post_modified_author_email]</code></dt>
			<dd>The email address of the author that modified the post.</dd>

			<dt><code>[post_date]</code></dt>
			<dd>The date the post was published. This will only work with published posts.</dd>

			<dt><code>[post_modified_date]</code></dt>
			<dd>The date the post was modified. This is the timestamp of current action.</dd>

		</dl>

	</div>

<?php

}

function email_insert_post() {

	// variables for the field and option names
	$email_action 	= (isset($_POST['email_action']) 	? $_POST['email_action'] : '');
	$email_type 	= (isset($_POST['email_type']) 		? $_POST['email_type'] : '');
	$email_from 	= (isset($_POST['email_from']) 		? $_POST['email_from'] : '');
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

function email_get_users() {
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

function email_get_template() {
	global $wpdb;
	$action = $_POST['givenAction'];

	echo email_template( $action[0] );

	die();
}