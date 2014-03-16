(function ($) {
	"use strict";

	$(function () {

		function emailSubscriptionAjax(data, event, triggerType) {

			var xhr;

			if (xhr) {
				xhr.abort();
			}

			xhr = $.ajax({
				type: 'POST',
				url: email.ajaxurl,
				dataType: 'json',
				data: $(data).serialize()
			})
				.done(function (response) {
					if (response) {
						try {
							resetForm($(data));
						} catch (err) {

						}
					}
				})
				.fail(function (response) {

				})
				.always(function (response) {
					
					if (triggerType) {
						$.event.trigger({
							type: triggerType,
							message: response,
							time: new Date()
						});
					}

				});

			event.preventDefault();

		}
		
		function emailSubscriberList(e) {
			var total = $(e.message.subscribers).length;
			$('.task-subscribers ul').empty();
			$.each(e.message.subscribers, function (key, value) {
				$('.task-subscribers ul').append(function () {
					var output = '<li><input type="checkbox" name="email_addresses[]" value="' + value.email_address + '"> <a href="' + value.email_address + '">' + value.email_address + '</a></li>';
					return output;
				});
			});			
		}
		
		function emailSubscriberSubscribe(e) {
			
			emailSubscriberList(e);
		}

		function emailSubscriberUnsubscribe(e) {
			emailSubscriberList(e);
		}

		function resetForm($form) {
			$form.find('input:text, input:password, input:file, select, textarea').val('');
			$form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		}

		$('.email-subscribe').submit(function (e) {
			emailSubscriptionAjax(this, e, 'email-subscribe');
		});

		$('.email-unsubscribe').submit(function (e) {
			emailSubscriptionAjax(this, e, 'email-unsubscribe');
		});

		$(document).on('email-subscribe', emailSubscriberSubscribe);
		$(document).on('email-unsubscribe', emailSubscriberUnsubscribe);

	});

}(jQuery));