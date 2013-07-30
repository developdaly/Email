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

	// Must be Editor to access the settings page.
	if ( !current_user_can( 'edit_posts' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$send_options = get_option( 'email_settings' );

	// variables for the field and option names
	$email_action	= 'email_action';
	$email_type		= 'email_type';
	$email_from		= 'email_from';
	$email_to		= 'email_recipients_to';
	$email_cc		= 'email_recipients_cc';
	$email_bcc		= 'email_recipients_bcc';
	$email_message	= 'email_message';
	$email_hidden	= 'email_hidden';

	// See if the user has posted some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ $email_hidden ]) && $_POST[ $email_hidden ] == 'Y' ) {

		$title = $_POST['email_action'] .' / '. $_POST['email_type'] .' / '. $_POST['email_recipients_to'];

		$args = array(
			'post_type' => 'email',
			'meta_query' => array(
				array(
					'key' => 'email_action',
					'value' => $_POST['email_action'],
				),
				array(
					'key' => 'email_type',
					'value' => $_POST['email_type'],
				),
				array(
					'key' => 'email_from',
					'value' => $_POST['email_from'],
				),
				array(
					'key' => 'email_to',
					'value' => $_POST['email_to'],
				),
				array(
					'key' => 'email_cc',
					'value' => $_POST['email_cc'],
				),
				array(
					'key' => 'email_bcc',
					'value' => $_POST['email_bcc'],
				),
				array(
					'key' => 'email_subject',
					'value' => $_POST['email_subject']
				),
				array(
					'key' => 'email_message',
					'value' => $_POST['email_message']
				)
			)
		);
		$existing = get_posts( $args );

		if( $existing ) {
			echo '<div class="updated"><p><strong>Email configuration already exists.</strong></p></div>';
			return;
		}

		// Create post object
		$post = array(
			'post_title'	=> wp_strip_all_tags( $title ),
			'post_content'	=> $_POST['email_message'],
			'post_status'	=> 'publish',
			'post_type'		=> 'email'
		);

		// Insert the post into the database
		$post_id = wp_insert_post( $post );
		update_post_meta( $post_id, $email_action, $_POST['email_action'] );
		update_post_meta( $post_id, $send_type, $_POST['email_type'] );
		update_post_meta( $post_id, $send_recipients_to, $_POST['send_recipients_to'] );

		echo '<div class="updated"><p>When <strong>'. $_POST['email_type'] .'</strong> is <strong>'. $_POST['email_action'] .'</strong> send an email to <strong>'. $_POST['email_to'] .'s</strong></p></div>';

	} ?>

	<div class="wrap">

		<h2><?php echo __( 'Add New Email Event', 'send' ) ?></h2>

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
							<select name="<?php echo $email_type; ?>">
								<?php foreach( $types as $key => $value ) {
									echo '<option val="'. $key .'">'. $value .'</option>';
								}; ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_action; ?>">Action</label></th>
						<td>
							<select name="<?php echo $email_action; ?>">
								<?php foreach( $actions as $key => $value ) {
									echo '<option val="'. $key .'">'. $value .'</option>';
								}; ?>
							</select>

						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_from; ?>">To</label></th>
						<td>
							<select name="<?php echo $email_from; ?>">
								<?php wp_dropdown_roles(); ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_to; ?>">To</label></th>
						<td>
							<select name="<?php echo $email_to; ?>">
								<?php wp_dropdown_roles(); ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_cc; ?>">CC</label></th>
						<td>
							<select name="<?php echo $email_cc; ?>">
								<?php wp_dropdown_roles(); ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_bcc; ?>">BCC</label></th>
						<td>
							<select name="<?php echo $email_bcc; ?>">
								<?php wp_dropdown_roles(); ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="<?php echo $email_message; ?>">Message</label></th>
						<td>
							<div>You may use <code>[action]</code>, <code>[the_meta]</code>, <code>[permalink]</code>, <code>[post_type]</code>, <code>[recipient]</code>, <code>[site_title]</code></div>

							<?php
							$template = email_template_new();

							wp_editor( $template, 'editpost' );

							?>
						</td>
					</tr>

				</tbody>
			</table>

			<input type="hidden" name="<?php echo $email_hidden; ?>" value="Y">

			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Setup Email') ?>" />
			</p>

		</form>
	</div>

<?php

}