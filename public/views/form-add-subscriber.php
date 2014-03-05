<?php if( is_user_logged_in() ) { ?>

<form class="email-add-subscriber form-horizontal" role="form" method="post">

	<div class="form-group">
		<label class="control-label col-sm-2" for="email-subscriber-address">Email Address</label>
		<div class="control-input col-sm-10">
			<input type="text" class="form-control" id="email-subscriber-addressr" name="email-subscriber-address" placeholder="Email address...">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn">Subscribe</button>
		</div>
	</div>

	<input type="hidden" name="action" value="email_subscriptions">
	<input type="hidden" name="controller" value="email_add_subscriber">
	<input type="hidden" name="post-id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'email_add_subscriber' ); ?>">

</form>

<?php
$subscribers = get_post_meta( get_the_ID(), '_email_subscribers' );
// check if the custom field has a value
if( ! empty( $subscribers[0] ) ) { ?>
<div class="task-subscribers">
	<ul>
	<?php foreach( $subscribers[0] as $subscriber ) { ?>
		<li><a href="mailto:<?php echo $subscriber['email_address']; ?>"><?php echo $subscriber['email_address']; ?></a></li>
	<?php } ?>
	</ul>
</div>
							   
<?php } ?>

<?php } else { ?>

<div class="alert alert-warning">

	You must be logged in to subscribe.
	
</div>

<?php } ?>