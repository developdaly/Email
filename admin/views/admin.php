<?php
/**
 * Represents the view for the email setup page.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Email
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Patrick Daly
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<?php

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
	$email_from_name= 'email_from_name';
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

		<form name="send" method="post" action="">

			<?php

				$actions = array( 'new', 'updated', 'deleted' );
				$types = get_post_types();

			?>

			<p>Configure settings here to create the email rules.</p>

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
						<th scope="row"><label for="<?php echo $email_from_name; ?>">From</label></th>
						<td>
							<input type="text" id="<?php echo $email_from_name; ?>" name="<?php echo $email_from_name; ?>" style="width: 25%" value="<?php echo get_bloginfo( 'site_name' ); ?>" placeholder="The name in the From field">
							<input type="text" id="<?php echo $email_from; ?>" name="<?php echo $email_from; ?>" style="width: 25%" value="<?php echo $current_user->user_email; ?>" placeholder="The email address to send from">
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
							<input type="text" name="<?php echo $email_subject; ?>" style="width: 50%" value="[[site_name]] [post_title] [action]"> Example: "[My Site] Hello World! updated"
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

</div>
