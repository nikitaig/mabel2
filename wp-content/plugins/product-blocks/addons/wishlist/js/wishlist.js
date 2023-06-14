(function ($) {
	"use strict";

	$(document).on(
		"click",
		".wopb-wishlist-add, .wopb-wishlist-remove, .wopb-wishlist-cart-added",
		function (e) {
			e.preventDefault();
			const that = $(this);
			const _modal = $('.wopb-modal-wrap:first');
			const actionType = that.data("action");
			const emptyWishlist = wopb_wishlist.emptyWishlist;

			const simpleProduct = that.find('a').hasClass('product_type_simple') && that.find('a').hasClass('add_to_cart_button');

			$.ajax({
				url: wopb_wishlist.ajax,
				type: "POST",
				data: {
					action: "wopb_wishlist",
					post_id: that.data("postid"),
					type: actionType,
					simpleProduct: simpleProduct,
					wpnonce: wopb_wishlist.security,
				},
				beforeSend: function () {
					if(actionType !== 'add') {
						_modal.find('.wopb-modal-loading').addClass('active');
					}
					if (that.data("redirect") == undefined && actionType == 'add' && that.hasClass('wopb-wishlist-active')) {
						_modal.find('.wopb-modal-body').html('');
						_modal.addClass('active');
					}
				},
				success: function (response) {
					if (response.success) {
						if (actionType == "remove") {
							$('.wopb-wishlist-add[data-postid="'+that.data("postid")+'"]').removeClass('wopb-wishlist-active');
							that.removeWishListItem(_modal);
						}
						if (actionType == "cart_remove" && emptyWishlist) {
							$('.wopb-wishlist-add[data-postid="'+that.data("postid")+'"]').removeClass('wopb-wishlist-active');
							if ( simpleProduct ) {
								that.removeWishListItem(_modal);
							}
						}
						if (actionType == "cart_remove_all" && emptyWishlist) {
							let post_ids = that.data("postid");
							if ( $.type(post_ids) === 'number' ) {
								$('.wopb-wishlist-add[data-postid="'+post_ids+'"]').removeClass('wopb-wishlist-active');
							}
							else if ($.type(post_ids) === 'string' && post_ids.includes(",")) {
								post_ids = post_ids.split(",");
								post_ids.forEach(element => {
									$('.wopb-wishlist-add[data-postid="'+element+'"]').removeClass('wopb-wishlist-active');
								});
							}
							$(".wopb-wishlist-modal-content table").remove();
							_modal.removeClass('active');
						}
						if (actionType == "add") {
							if(that.hasClass('wopb-wishlist-active')) {
								_modal.find('.wopb-modal-body').html(response.data.html);
        						$('.wopb-wishlist-modal-content .wopb-loop-variations-form').remove()
							}else{
								that.addClass('wopb-wishlist-active');
							}
						}
						let redirectUrl = that.data("redirect");

						if ( !simpleProduct && actionType == "cart_remove" ) {
							redirectUrl = that.find('a').prop('href');
						}
						if ( redirectUrl ) {
							window.location.href = redirectUrl;
						}
					}
				},
				complete: function (data) {
					if(actionType !== 'add') {
						_modal.find('.wopb-modal-loading').removeClass('active');
					}
				},
				error: function (xhr) {
					console.log( "Error occured.please try again" + xhr.statusText + xhr.responseText );
				},
			});
		}
	);

	$.fn.removeWishListItem = function(_modal) {
		let that = $(this);
		if (that.closest('tbody').find('tr').length <= 1) {
			that.closest("table").remove();
			$('.wopb-wishlist-modal-content .wopb-wishlist-cart-added').remove();
			_modal.removeClass('active');
		} else {
			that.closest("tr").remove();
		}
	}
	$(document).on('click', '.wopb-modal-close, .wopb-wishlist-modal-content .wopb-modal-continue', function(e){ // Close Button
		if($(this).parents('.wopb-modal-body:first').length > 0) {
			e.preventDefault();
		}
        $('.wopb-modal-wrap').removeClass('active');
    });


})(jQuery);