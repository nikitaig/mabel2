(function($) {
    'use strict';

    // ------------------------
    // Quick Add to Quickview Button
    // ------------------------
    $(document).on('click', '.wopb-quickview-btn', function(e){
        let that = $(this);
        e.preventDefault();
        const _modal = $('.wopb-modal-wrap:first');
        const _postId = $(this).data('postid');
        const _postList = $(this).data('list');

        if(_postId){
            $.ajax({
                url: wopb_quickview.ajax,
                type: 'POST',
                data: {
                    action: 'wopb_quickview',
                    postid: _postId,
                    postList: _postList,
                    wpnonce: wopb_quickview.security
                },
                beforeSend: function() {
                    _modal.find('.wopb-modal-body').html('');
                    _modal.addClass('active');
                    _modal.find('.wopb-modal-loading').addClass('active');
                },
                success: function(data) {
                    _modal.find('.wopb-modal-body').html(data);
                    const width = $(window).width()-40;
                    that.quickViewElement(_modal );
                },
                complete:function() {
                    _modal.find('.wopb-modal-loading').removeClass('active');
                },
                error: function(xhr) {
                    console.log('Error occured.please try again' + xhr.statusText + xhr.responseText );
                },
            });
        }
    });

    // ------------------------
    // Quick View Element Initialize
    // ------------------------
    
    $.fn.quickViewElement = function(_modal) {
        _modal.find(".ct-increase").remove();
        _modal.find(".ct-decrease").remove();
        
        jQuery('.quickview-slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true
        });

        if ($('.wopb-quick-view-modal .wopb-quick-view-image .wopb-thumbnails img').length > 1) {
            $('.wopb-quick-view-modal .wopb-quick-view-image .wopb-thumbnails').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                arrows: true,
            });
        }
        const form_variation = _modal.find(".variations_form");
        const wc_product_gallery = _modal.find(".woocommerce-product-gallery");

        wc_product_gallery.each( function() {
            jQuery( this ).trigger( 'wc-product-gallery-before-init', [ this, wc_single_product_params ] );

            jQuery( this ).wc_product_gallery( wc_single_product_params );

            jQuery( this ).trigger( 'wc-product-gallery-after-init', [ this, wc_single_product_params ] );

            jQuery( this ).wc_product_gallery(  );
        } );

        form_variation.each(function () {
            jQuery(this).wc_variation_form();
            if(wopb_quickview.isVariationSwitchActive == 'true') {
                jQuery(this).loopVariationSwitcherForm();
            }
        });
    }

    // ------------------------
    // Quick Add to Cart Plus
    // ------------------------
    $(document).on('click', '.wopb-add-to-cart-plus', function(e){
        e.preventDefault();
        if($('.woocommerce-shop').length === 1 && $('.wopb-builder-container').length ===0) {
            const parents = $(this).closest('form.cart');
            const parentQuantity = $(this).parent().find('input.qty');
            let max = parentQuantity.attr('max');
            let _val = isNaN(parseInt(parentQuantity.val())) ? 0 : parseInt(parentQuantity.val());
            if ( max && typeof max !== typeof undefined ) {
                if ( _val < parseInt(max) ) {
                    _val = _val + 1;
                }else{
                    $(this).addClass('disable');
                }
            } else {
                _val = _val + 1;
            }
            parents.find('.single_add_to_cart_button').attr('data-quantity', _val );
            parentQuantity.val( _val );
            $('.wopb-add-to-cart-minus').removeClass('disable');
        }
        
    });

    // ------------------------
    // Quick Add to Cart Minus
    // ------------------------
    $(document).on('click', '.wopb-add-to-cart-minus', function(e){
        e.preventDefault();
        if($('.woocommerce-shop').length === 1 && $('.wopb-builder-container').length ===0) {
            const parents = $(this).closest('form.cart');
            const parentQuantity = parents.find('.quantity .qty');
            let _val = parseInt(parentQuantity.val());
            if ( _val >= 2 ) {
                _val = _val - 1;
            } else {
                $(this).addClass('disable');
            }
            parents.find('.single_add_to_cart_button').attr('data-quantity', _val );
            parentQuantity.val( _val );
            $('.wopb-add-to-cart-plus').removeClass('disable');
        }
    });


    $(document).on('submit', '.wopb-quick-view-content form', function(e){
        e.preventDefault();
        let modalContent = $(this).parents('.wopb-modal-content:first');
        let variation_selectors = modalContent.find('.variations_form .variations select[data-attribute_name]');
        let variation = {};
        variation_selectors.each(function() {
            let attribute_name = $(this).data('attribute_name');
            variation[attribute_name] = $(this).val();
        });
        $.ajax({
            url: wopb_core.ajax,
            type: 'POST',
            data: {
                action: 'wopb_addcart',
                postid: modalContent.data('product_id'),
                variationId: modalContent.find('input[name=variation_id]').val(),
                quantity: modalContent.find('.qty').val(),
                variation: variation,
                wpnonce: wopb_core.security
            },
            beforeSend: function() {
                modalContent.find('.single_add_to_cart_button').addClass('loading')
            },
            success: function(response) {
                $( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
                if(response.message) {
                    modalContent.find('.woocommerce-message').removeClass('wopb-d-none');
                    modalContent.find('.woocommerce-message').html(response.message);
                    modalContent.animate({
                        scrollTop: modalContent.find('.woocommerce-message').offset().top - modalContent.offset().top + modalContent.scrollTop()
                    }, 300);
                }
            },
            complete:function() {
                modalContent.find('.single_add_to_cart_button').removeClass('loading')
            },
            error: function(xhr) {
                console.log('Error occured.please try again' + xhr.statusText + xhr.responseText );
            },
        });
    })


})( jQuery );