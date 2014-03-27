<?php if( is_user_logged_in() ) { ?>

<form class="email-subscribe" role="form" method="post">
	
	<div class="form-group form-group-email-subscriber-address">
		<label class="control-label" for="email-subscriber-address">Add subscriber email address</label>
		<div class="control-input">
			<input type="text" class="form-control" id="email-subscriber-address" name="email-subscriber-address" placeholder="Email address..."> <button type="submit" class="btn">Subscribe</button>
		</div>
	</div>
	
	<input type="hidden" name="action" value="email_subscriptions">
	<input type="hidden" name="controller" value="email_subscribe">
	<input type="hidden" name="post-id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'email_subscribe' ); ?>">

</form>

<?php
$subscribers = get_post_meta( get_the_ID(), '_email_subscribers' );
// check if the custom field has a value
if( ! empty( $subscribers[0] ) ) { ?>
<form class="email-unsubscribe" role="form" method="post">
	<div class="task-subscribers">
		<label>Subscribers</label>
		<ul>
		<?php foreach( $subscribers[0] as $subscriber ) { ?>
			<li>
				<input type="checkbox" name="email_addresses[]" value="<?php echo $subscriber['email_address']; ?>">
				<a href="mailto:<?php echo $subscriber['email_address']; ?>"><?php echo $subscriber['email_address']; ?></a>
			</li>
		<?php } ?>
		</ul>
	</div>
	
	<input type="submit" class="btn" value="Unsubscribe selected">
	
	<input type="hidden" name="action" value="email_subscriptions">
	<input type="hidden" name="controller" value="email_unsubscribe">
	<input type="hidden" name="post-id" value="<?php the_ID(); ?>">
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'email_unsubscribe' ); ?>">
	
</form>

<?php } ?>

<?php } else { ?>

<div class="alert alert-warning alert-login-required">

	You must be logged in to subscribe.

</div>

<?php } ?>