(function ( $ ) {
	"use strict";

	$(function () {

		$('.email-add-subscriber').submit(function (e) {
			emailSubscriptionrAjax(this, e, 'email-add-subscriber' );
		});


		function emailSubscriptionrAjax(data, event, triggerType) {

			var xhr;

			if ( xhr ) {
				xhr.abort();
			}

			xhr = $.ajax( {
				type: 'POST',
				url: email.ajaxurl,
				dataType: 'json',
				data: $(data).serialize()
			})
			.done(function( response ) {
				if ( response ) {
					try {
						resetForm($(data));
					} catch ( err ) {

					}
				}
			})
			.fail(function( response ) {

			})
			.always(function( response ) {
				
				console.log(data);
				
				console.log(event);
				
				console.log(response);

				if( triggerType ) {
					$.event.trigger({
						type: triggerType,
						message: response,
						time: new Date()
					});
				}

			});

			event.preventDefault();

		}

		function resetForm($form) {
			$form.find('input:text, input:password, input:file, select, textarea').val('');
			$form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		}

	});

}(jQuery));