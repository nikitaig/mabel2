(function($) {
    'use strict';

    // Shortcode OnClick Copy
    $(".wopb-shortcode-copy").click(function(e) {
        e.preventDefault();
        const that = $(this);
        navigator.clipboard.writeText(that.text());
        that.append("<span>Copied!</span>");
        setTimeout( function(){ that.find('span').remove(); }, 500 );
    });

    // WooCommerce Tab
    $(document).on('click', '.wc-tabs li', function(e){
        e.preventDefault();
        $('.wc-tabs li').removeClass('active');
        $(this).addClass('active');
        const selectId = $(this).attr('aria-controls');
        $('.woocommerce-Tabs-panel').hide();
        $('.woocommerce-Tabs-panel#'+selectId).show();
    });

    $( '.wopb-color-picker' ).wpColorPicker();

    // Add target blank for upgrade button
    $('.toplevel_page_wopb-settings ul > li > a').each(function (e) {
        if($(this).attr('href') && $(this).attr('href').indexOf("?wopb=plugins") > 0) {
            $(this).attr('target', '_blank');
        }
    });

    if($('body').hasClass('block-editor-page')){
        $('body').addClass('wopb-editor-'+wopb_option.width);
    }

    $(document).on('click', '.wopb-dashboard-container .wopb-addons-enable', function(e){
        const that = this;
        const val = $(that).hasClass('wopb-blocks') ? (this.checked ? 'yes' : '') : this.checked;
        if ($(that).hasClass('disabled')) {
            e.preventDefault();
            $('.wopb-dashboard-container .wopb-unlock-popup-container').addClass('active');
        }else if($(that).hasClass('wopb-updated-pro-feature')) {
            e.preventDefault();
            $('.wopb-dashboard-container .wopb-pro-updated-feature-popup-container').addClass('active');
        }else {
            $.ajax({
                url: wopb_option.ajax,
                type: 'POST',
                data: {
                    action: 'wopb_addon',
                    addon: $(that).attr('id'),
                    value: $(that).data('type') == 'blocks' ? (val?'yes':'') : val,
                    wpnonce: wopb_option.security
                },
                success: function (data) {
                    if( $(that).data('type') == 'wopb_templates' || $(that).data('type') == 'wopb_builder' ) {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    console.log('Error occured.please try again' + xhr.statusText + xhr.responseText);
                },
            });
        }
    });

    const actionBtn = $('.page-title-action');
    const savedBtn = $(".wopb-saved-templates-action");
    if ( savedBtn.length > 0 ) {
        if(savedBtn.data())
        actionBtn.addClass('wopb-save-templates-pro').text( savedBtn.data('text') )
        actionBtn.attr( 'href', savedBtn.data('link') )
        actionBtn.attr( 'target', '_blank' )
    }


    // ------------------------
    // Upload Flip Feature Image 
    // ------------------------
	var file_frame;
	function upload_feature_image( button ) {
		const button_id = button.attr('id');
		const field_id = button_id.replace( '_button', '' );
		if ( file_frame ) {
            file_frame.open();
            return;
		}
		file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data( 'uploader_title' ),
            button: { text: $(this).data( 'uploader_button_text' ) },
            multiple: false
		});
		file_frame.on( 'select', function() {
            const attachment = file_frame.state().get('selection').first().toJSON();
            $('#'+field_id).val(attachment.id);
            $('#flipimage-feature-image img').attr('src',attachment.url);
            $('#flipimage-feature-image img').show();
            $('#' + button_id).attr('id', 'remove_feature_image_button');
            $('#remove_feature_image_button').text('Remove Flip Image');
		});
		file_frame.open();
	};

	$('#flipimage-feature-image').on( 'click', '#upload_feature_image_button', function(e) {
        e.preventDefault();
		upload_feature_image($(this));
	});

	$('#flipimage-feature-image').on( 'click', '#remove_feature_image_button', function(e) {
		e.preventDefault();
		$( '#upload_feature_image' ).val( '' );
		$( '#flipimage-feature-image img' ).attr( 'src', '' );
		$( '#flipimage-feature-image img' ).hide();
		$( this ).attr( 'id', 'upload_feature_image_button' );
		$( '#upload_feature_image_button' ).text( 'Set Flip Image' );
    });
    
    // Add URL for Gutenberg Post Blocks
    $(document).on('click', '#plugin-information-content ul > li > a', function(e){
        const URL = $(this).attr('href');
        if (URL.includes('downloads/product-blocks-pro')) {
            e.preventDefault();
            window.open("https://www.wpxpo.com/productx/");
        }
    });

    //productx tab script in woocommerce product page in admin
    $('.wopb-productx-options-tab-wrap .wopb-tab-title-wrap .wopb-tab-title').click(function () {
        $(this).closest('.wopb-productx-options-tab-wrap').find('.wopb-tab-title').removeClass('active').eq(jQuery(this).index()).addClass('active');
        $(this).closest('.wopb-productx-options-tab-wrap').find('.wopb-tab-content').removeClass('active').eq(jQuery(this).index()).addClass('active');
    });

    $('.wopb-productx-options-tab-wrap .wopb-tab-title-wrap .wopb-tab-title:first-child').each(function () {
        $(this).closest('.wopb-productx-options-tab-wrap').find('.wopb-tab-title').removeClass('active').eq(jQuery(this).index()).addClass('active');
        $(this).closest('.wopb-productx-options-tab-wrap').find('.wopb-tab-content').removeClass('active').eq(jQuery(this).index()).addClass('active');
    })


    /* ---- variation swatches --- */
    //open media library
    $('#wopb-term-upload-img-btn').click(function (e) {
        e.preventDefault();
        let object = $(this);
        let mdeia = wp.media({
            title: 'Attribute Term Image',
            multiple: false
        }).open().on('select', function (e) {
            let selectedImage = mdeia.state().get('selection').first().toJSON();
            object.parent().prev("#wopb-term-img-thumbnail").find("img").attr("src", selectedImage.sizes.thumbnail.url);
            object.parent().find("#wopb-term-img-remove-btn").removeClass('wopb-d-none');
            object.parent().find('#wopb-term-img-input').val(selectedImage.id);
        });
    });

    //remove image from attribute
    $('#wopb-term-img-remove-btn').click(function (e) {
        $(this).parent().prev("#wopb-term-img-thumbnail").find("img").attr("src", wopb_option.url + 'assets/img/wopb-placeholder.jpg');
        $(this).parent().find('#wopb-term-img-input').val('');
    })
    /* ---- end variation swatches --- */


    /*
     * Setting Tab
    */
    if ('?page=wopb-settings' == window.location.search) {
        const hash = window.location.hash
        if (hash) {
            if (hash.indexOf('demoid') < 0) {
                $('ul.wopb-settings-tab > li, ul.wopb-settings-content > li, .toplevel_page_wopb-settings ul li').removeClass('current');
                $('ul.wopb-settings-tab > li a[href$='+hash+'], .toplevel_page_wopb-settings ul li a[href$='+hash+']').closest('li').addClass('current');
                $('ul.wopb-settings-content > li[data-tab='+hash+']').addClass('current');
                if (hash == '#home') {
                    $('.toplevel_page_wopb-settings ul li.wp-first-item').addClass('current');
                } else {
                    $('.toplevel_page_wopb-settings ul li a[href$='+hash+']').addClass('current');
                }
            }
        }
    }

    $(document).on('click', 'ul.wopb-settings-tab > li a, .toplevel_page_wopb-settings ul li a', function(e) {
        let value = $(this).attr('href')
        if (value) {
            value = value.split('#');
            if (typeof value[1] != 'undefined' && value[1].indexOf('demoid') < 0 && value[1]) {
                $('ul.wopb-settings-tab > li a, .toplevel_page_wopb-settings ul li a').closest('ul').find('li').removeClass('current');
                $('a[href$='+value[1]+']') .closest('li').addClass('current');
                $(this).closest('li').addClass('current');
                $('ul.wopb-settings-content > li').removeClass('current');
                $('ul.wopb-settings-content > li[data-tab=#'+value[1]+']').addClass('current');
                if (value[1] == 'home') {
                    $('.toplevel_page_wopb-settings ul li.wp-first-item').addClass('current');
                }
            }
        }
    });
    /*
     * End Setting Tab
    */

    $(document).on('click', '.wopb-block-settings', function(e){
        $(this).parent().find('.wopb-popup-container').addClass('active');
    });

    //Popup container close
    $(document).on('click', '.wopb-settings-content .wopb-popup-close', function(e){
        if (!$(this).hasClass('popup-center')) {
            $(this).closest('.wopb-popup-container').removeClass('active');
        }
    });
    $(document).on('click', '.wopb-addons-setting-save, .wopb-submit-button button', function(e){
        $('.wopb-addons-setting-save').addClass('wopb-loading');

        e.preventDefault()
		const that = $(this).closest('form')
        let defaultRepeater = that.find('.wopb-default-repeater');
        let defaultRepeaterHtml = defaultRepeater.html();
        that.find('.wopb-default-repeater').html('');
        let form = that[0];
        let filterFormData = {};
        const formData = that.serializeArray();

        for (let i in formData) {
            const field = formData[i];
            let repeaterInput = $('[name="' + field['name'] + '"]:first');
            let repeaterBaseName = repeaterInput.parents('.wopb-repeat-section:first').data('base-repeater');
            if (field['name'].indexOf('[]') > 0) {
                const key = field['name'].replace('[]', '');
                if(repeaterBaseName) {
                    let lastIndex = 0;
                    if(repeaterInput.hasClass('wopb-repeater-input')) {
                        if (filterFormData[key]) {
                            filterFormData[key].push(field['value']);
                        }else {
                            filterFormData[key] = [field['value']];
                        }
                        lastIndex = filterFormData[repeaterBaseName].length - 1
                        filterFormData[repeaterBaseName][lastIndex] = {};
                    }else{
                        lastIndex = filterFormData[repeaterBaseName].length - 1;
                        if (filterFormData[repeaterBaseName][lastIndex][key]) {
                            filterFormData[repeaterBaseName][lastIndex][key].push(field['value']);
                        }else {
                            filterFormData[repeaterBaseName][lastIndex][key] = [field['value']];
                        }
                    }
                }else {
                    if (filterFormData[key]) {
                        filterFormData[key].push(field['value']);
                    } else {
                        filterFormData[key] = [field['value']];
                    }
                }
            } else {
                if(repeaterBaseName) {
                    let lastIndex = filterFormData[repeaterBaseName].length - 1;
                    filterFormData[repeaterBaseName][lastIndex][field['name']] = field['value']
                }else {
                    filterFormData[field['name']] = field['value'];
                }
            }
        }

        // Empty Radio or Checkbox Select
        const $radio = that.find('input[type=radio],input[type=checkbox]',this);
        if ($radio.length > 0) {
            $.each($radio,function(){
                if(!filterFormData.hasOwnProperty(this.name)){
                    let repeaterBaseName = $('[name="' + this.name + '"]:first').parents('.wopb-repeat-section:first').data('base-repeater');
                    if(repeaterBaseName == undefined) {
                        filterFormData[this.name] = '';
                    }
                }
            });
        }

        if(defaultRepeater.length > 0 ) { // Insert repeater html to DOM
            defaultRepeater.html(defaultRepeaterHtml);
        }

        $.ajax({
            url: wopb_option.ajax,
            type: 'POST',
            data: {
                action: 'option_data_save',
                data: filterFormData,
                wpnonce: wopb_option.security
            },
            success: function(data) {
                $('.wopb-addons-setting-save').removeClass('wopb-loading');
				if (data.success) {
					that.find('.wopb-data-message').html(data.data).fadeIn();
					setTimeout(function() { that.find(".wopb-data-message").fadeOut(); }, 3000);
				}
            },
            error: function(xhr) {
                console.log('Error occured.please try again' + xhr.statusText + xhr.responseText );
            },
        });

    });

    /*
    /* Repeater script
     */
    $(document).on('click', '.wopb-repeatable-wrap .wopb-settings-field-wrap .wopb-add-btn', function(e) {
        e.preventDefault();
        let defaultRepeatSection = $(this).parents('.wopb-repeatable-wrap:first').find('.wopb-default-repeater:first');
        let repeatSection = $(this).parents('.wopb-repeatable-wrap:first').find('.wopb-repeat-section:last');

        $(defaultRepeatSection.html()).insertAfter(repeatSection);
    })

    $(document).on('click', '.wopb-repeatable-wrap .wopb-repeat-section .wopb-remove', function(e){
        $(this).parents('.wopb-repeat-section:first').remove();
    })
    $(document).on('click', '.wopb-popup-container.blocks-settings .wopb-toggle-switch', function(e){
        let input = $(this).parent().find('.wopb-toggle-checkbox');

        let repeatSection = $(this).parents('.wopb-repeat-section:first');
        let inputName = $(this).parents('.wopb-repeat-section:first').find('input[name=' + input.attr('name') + ']');
        let dependOnField = inputName.parents('.wopb-repeat-section:first').find('[name=' + input.data('depend-on-field') + ']');
        let dependTargetField = inputName.parents('.wopb-repeat-section:first').find('[name=' + input.data('depend-target-field') + ']');
        if(repeatSection.length > 0) {
            dependOnField = dependOnField.length == 0 ? inputName.parents('form:first').find('[name=' + input.data('depend-on-field') + ']') : dependOnField;
            dependTargetField = dependTargetField.length == 0 ? inputName.parents('form:first').find('[name=' + input.data('depend-target-field') + ']') : dependTargetField;

            if (dependTargetField.data('default')) {
                dependTargetField.val(dependTargetField.data('default'));
            }
            if (input.attr('name') == 'wopb_is_default_currency') {
                input.parents('.wopb-repeatable-wrap:first').find('.wopb-repeat-section .wopb-collapse-header .wopb-remove').removeClass('wopb-d-none');
                input.parents('.wopb-repeatable-wrap:first').find('.wopb-repeat-section input[name=wopb_currency_rate], .wopb-repeat-section input[name=wopb_currency_exchange_fee]').removeAttr('disabled');
            }
        }
        if(input.prop('checked') == true) {
            if(repeatSection.length > 0) {
                inputName.prop('checked', false);
                inputName.val('');
            }
            input.prop('checked', false);
            input.val('');
        }else {
            if(repeatSection.length > 0) {
                input.parents('.wopb-repeatable-wrap:first').find('.wopb-repeat-section input[name=' + input.attr('name') + ']').prop('checked', false);
                input.parents('.wopb-repeatable-wrap:first').find('.wopb-repeat-section input[name=' + input.attr('name') + ']').val('');
                inputName.prop('checked', true);
                inputName.val('yes');
                if (input.attr('name') == 'wopb_is_default_currency') {
                    $(this).parents('.wopb-repeat-section:first').find('.wopb-collapse-header .wopb-remove').addClass('wopb-d-none');
                    $(this).parents('.wopb-repeat-section:first').find('input[name=wopb_currency_rate], input[name=wopb_currency_exchange_fee]').attr('disabled', true);
                }
                if (dependOnField.val()) {
                    dependTargetField.val(dependOnField.val())
                }
            }
            input.prop('checked', true);
            input.val('yes');
        }
    })

    $(document).on('click', '.wopb-repeatable-wrap .wopb-repeat-section .wopb-collapse-header', function(e){
        if(event.target.classList.contains('wopb-toggle-switch') || event.target.classList.contains('wopb-remove')) {
            return;
        }
        const that = $(this);
        const collapseBody = that.siblings( ".wopb-collapse-body" );
        collapseBody.slideToggle( "slow", function () {
            if($(this).is(':visible')){
                that.find('.wopb-down-arrow ').removeClass('wopb-d-none');
                that.find('.wopb-right-arrow ').addClass('wopb-d-none');
            }
            else {
                that.find('.wopb-down-arrow ').addClass('wopb-d-none');
                that.find('.wopb-right-arrow ').removeClass('wopb-d-none');
            }
        } );
    })

    $(document).on('change', '.wopb-repeatable-wrap .wopb-repeat-section .wopb-settings-field select[name=wopb_currency]', function(e){
        let defaultCurrency = $(this).parents('form:first').find('input[name=wopb_default_currency]');
        let headerText = $(this).parents('.wopb-repeat-section:first').find('.wopb-header-content .wopb-header-text');
        headerText.text($(this).find('option:selected').text())
        if(defaultCurrency.prop('checked') == true) {
            defaultCurrency.val($(this).val())
        }
    })

    /*
    /* End Repeater script
     */


    // *************************************
    // Disable Google Font Action
    // *************************************
    $('input[name=disable_google_font]').on('change', function() {
        if ($(this).is(':checked')) {
            $('#wopb-regenerate-css').addClass('active')
        } else {
            $('#wopb-regenerate-css').removeClass('active')
        }
    });
    $(document).on('click', '#wopb-regenerate-css', function(e) {
        const that = $(this)
        $.ajax({
            url: wopb_option.ajax,
            type: 'POST',
            data: {
                action: 'disable_google_font',
                wpnonce: wopb_option.security
            },
            beforeSend: function() {
                that.addClass('wopb-spinner');
            },
            success: function(res) {
				if (res.success) {
                    that.find('.wopb-text').text(res.data);
				}
            },
            complete:function() {
                that.removeClass('wopb-spinner');
            },
            error: function(xhr) {
                console.log('Error occured.please try again' + xhr.statusText + xhr.responseText );
            },
        });
    });

})( jQuery );