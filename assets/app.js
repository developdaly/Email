jQuery(document).ready(function($) {

	$('.chosen-select').chosen();

	$('.select-role').chosen().on('change', function(evt, params) {

		var currentVal	= $(this).next().next().val(),
			nextId		= $(this).next().next().attr('id'),
			givenRole	= $(this).val(),
			data		= {
				action: 'email_get_users',
				role: givenRole
			};

		$.post(ajaxurl, data, function(response) {
			if( response !== 0 ) {
				$('#' + nextId).val( currentVal + response + ', ');
			}
		});

	});

	$('#email_action').on('change', function(evt, params) {

		var data = {
				action: 'email_get_template',
				givenAction: $(this).val(),
			};

		$.post(ajaxurl, data, function(response) {
			if( response !== 0 ) {
				$('#email_message').val( response );
			}
		});

	});

});