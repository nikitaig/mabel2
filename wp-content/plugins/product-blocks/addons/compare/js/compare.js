(function($) {
    'use strict';
    
    // ------------------------
    // Compare Modal Open
    // ------------------------
    $(document).on('click', '.wopb-compare-btn, .wopb-compare-remove', function(e) {
        e.preventDefault();
        const that = $(this);
        const _modal = $('.wopb-modal-wrap:first');
        const _postId = that.data('postid');
        const _postType = that.data('action');

        // const aa = $('.wopb-compare-btn[data-postid="'+that.data("postid")+'"]').removeClass('wopb-wishlist-active');
        const aa = $('.wopb-compare-btn[data-postid="'+that.data("postid")+'"]');

        if (_postId) {
            $.ajax({
                url: wopb_compare.ajax,
                type: 'POST',
                data: { 
                    action: 'wopb_compare', 
                    postid: _postId,
                    type: _postType,
                    wpnonce: wopb_compare.security
                },
                beforeSend: function() {
                    if (that.data("redirect") == undefined && _postType == 'add') {
                        _modal.find('.wopb-modal-body').html('');
                        _modal.addClass('active');
                        _modal.find('.wopb-modal-loading').addClass('active');
                    }
                },
                success: function(response) {
                    if (response.success) {
                        if (_postType == 'add') {
                            if (response.data.html) {
                                _modal.find('.wopb-modal-body').html(response.data.html);
                                $('.wopb-compare-modal-content .wopb-loop-variations-form').remove()
                            }
                            that.addClass('wopb-compare-active');
                            const redirectUrl = that.data("redirect");
                            if (redirectUrl) {
                                window.location.href = redirectUrl;
                            }
                        } else {
                            const tableLength = that.closest('table').find('tr:first td').length;
                            if (tableLength <= 1) {
                                that.closest('table').remove();
                                _modal.removeClass('active');
                            } else {
                                $('.'+that.data('class')).remove();
                            }
                            $('.wopb-compare-btn[data-postid="'+that.data("postid")+'"]').removeClass('wopb-compare-active');
                        }
                    }
                },
                complete:function() {
                    if (_postType == 'add') {
                        _modal.find('.wopb-modal-loading').removeClass('active');
                    }
                },
                error: function(xhr) {
                    console.log('Error occured.please try again' + xhr.statusText + xhr.responseText );
                },
            });
        }
    });
    $(document).on('click', '.wopb-modal-close, .wopb-wishlist-modal-content .wopb-modal-continue', function(e){ // Close Button
        if($(this).parents('.wopb-modal-body:first').length > 0) {
			e.preventDefault();
		}
        $('.wopb-modal-wrap').removeClass('active');
    });

})( jQuery );