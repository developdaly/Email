jQuery(document).ready(function($) {

	$('.chosen-select').chosen({
		allow_single_deselect: true
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