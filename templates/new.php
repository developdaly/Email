<?php

function email_template_new() {

	$output = '
	A new [post_type] (#[post_id] "[post_title") was created by [post_author]
	This action was taken on [post_date]

	[old_status] => [new_status]

	--------------------

	== [post_type] details ==
	Title: [post_title]
	Author: [post_atuhor] ([post_author_email])

	== Actions ==
	Edit: [edit_post]
	View: [permalink]

	--------------------

	[site_name] | [site_url] | [admin_url]
	';

	return $output;

}