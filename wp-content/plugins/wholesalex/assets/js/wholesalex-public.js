(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 */

	$(document).on('submit', '#wholesalex_registration_form', function (e) {
		e.preventDefault();

		var data = ($(this).serializeArray());
		data.push({name:'action',value:'wholesalex_user_registration'});
		data.push({name:'nonce' , value: wholesalex.nonce});
		$("#wholesalex_registration_error").hide();
		$("#wholesalex_registration_success").hide();

		$.ajax({
			url: wholesalex.ajax,
			type: 'POST',
			data,
			success: function (data) {
				if(!data.success){
					$("#wholesalex_registration_error").text(data.data);
					$("#wholesalex_registration_error").show();
				}
				else
				{
					$("#wholesalex_registration_success").text("Registration Successfull");
					$("#wholesalex_registration_success").show();
					window.location.href = "/wordpress";
				}
			},
			error: function (xhr) {
			},
		});
	});


	// Conversation Toggle
	$(document).on('click', '.wsx-title', function(e) {
		e.preventDefault();
        if ($(this).hasClass('active')) {
			$(this).parent().siblings('.wsx-content').slideUp();
			$(this).removeClass('active');
        } else {
			$('.wsx-content').slideUp();
			$('.wsx-title').removeClass('active');
			$(this).parent().siblings('.wsx-content').slideToggle();
			$(this).toggleClass('active');
        }
    });

})(jQuery);


const conversationOpen = () => {
	const conversationID = event.target.getAttribute('data-conv-id');
	const nonce = event.target.getAttribute('data-security');
	const formData = new FormData();
	formData.append('action', 'conversation_status_change');
	formData.append('conversationID', conversationID);
	formData.append('nonce', nonce);

	fetch(
		wholesalex.ajax, {
		method: 'POST',
		body: formData,
	})
	.then(res => res.json())
	.then(res => {
	})
}