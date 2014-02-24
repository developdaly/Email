<?php

function email_template( $action ) {

	if( $action == 'new' ) {

$output = 'A new [post_type] (#[post_id] "[post_title]") was created by [post_author]
This action was taken on [post_modified_date] at [post_modified_time]

[old_status] => [new_status]

--------------------

== [post_type] details ==
Title: [post_title]
Author: [post_author] ([post_author_email])

== Actions ==
Edit: [edit_post_url]
View: [permalink]

--------------------

[site_name] | [home_url] | [admin_url]
';

	}

	if( $action == 'updated' ) {

$output = 'A [post_type] (#[post_id] "[post_title]") was updated by [post_modified_author]
This action was taken on [post_modified_date] at [post_modified_time]

[old_status] => [new_status]

--------------------

== [post_type] details ==
Title: [post_title]
Author: [post_author] ([post_author_email])

== Actions ==
Edit: [edit_post_url]
View: [permalink]

--------------------

[site_name] | [home_url] | [admin_url]
';

	}

	return $output;

}