jQuery(document).ready(function () {
 
  const {__} = wp.i18n;
  var wocInstalled = ldfbt_data.ldfbt_plugin_wc_install == 2 ? true : false;
  var eddInstalled = ldfbt_data.ldfbt_plugin_edd_install == 2 ? true : false;
  var fbt_plugin_default = jQuery("input[name='ldfbt_upsells[ldfbt_plugin_type]']:checked").val();
  var ldfbtNonce = ldfbt_data.ldfbt_nonce;
  var alertTitle = ldfbt_data.ldfbt_pro_alert_title;
  var alertSubTitle = ldfbt_data.ldfbt_pro_alert_sub_title;
  var popupTitle = ldfbt_data.ldfbt_popup_title;
  var popUpPoint1 = ldfbt_data.ldfbt_popup_point1;
  var popUpPoint2 = ldfbt_data.ldfbt_popup_point2;
  var popUpPoint3 = ldfbt_data.ldfbt_popup_point3;
  var popUpPoint4 = ldfbt_data.ldfbt_popup_point4;
  var popUpPoint5 = ldfbt_data.ldfbt_popup_point5;
  var ldfbtUpgradeNow = ldfbt_data.ldfbt_upgarade_now;

  if (wocInstalled) {
    if ('edd' !== fbt_plugin_default) {
      // hide edd fields.
      jQuery("#ldfbt_upsells_fbt_edd_section_field").hide();
      jQuery("#ldfbt_upsells_edd_fbt_enable_field").hide();
      jQuery("#ldfbt_upsells_edd_fbt_ero_enable_field").hide();
    } else {
      // hide wc fields.
      jQuery("#ldfbt_upsells_ldfbt_discount_label_field").hide();
      jQuery("#ldfbt_upsells_fbt_wocom_section_field").hide();
      jQuery("#ldfbt_upsells_wc_fbt_enable_field").hide();
    }
  } else if (eddInstalled) {
    if ('woocom' !== fbt_plugin_default) {
      // hide wc fields.
      jQuery("#ldfbt_upsells_ldfbt_discount_label_field").hide();
      jQuery("#ldfbt_upsells_fbt_wocom_section_field").hide();
      jQuery("#ldfbt_upsells_wc_fbt_enable_field").hide();
    } else {
      // hide edd fields.
      jQuery("#ldfbt_upsells_fbt_edd_section_field").hide();
      jQuery("#ldfbt_upsells_edd_fbt_enable_field").hide();
      jQuery("#ldfbt_upsells_edd_fbt_ero_enable_field").hide();
    }
  }


  // if woocommerce is not installed
  if (!wocInstalled) {
    jQuery("input[name='ldfbt_upsells[ldfbt_plugin_type]'][value='woocom']").attr('disabled', true);
    jQuery("#ldfbt_upsells_ldfbt_discount_label_field").hide();
    jQuery("#ldfbt_upsells_fbt_wocom_section_field").hide();
    jQuery("#ldfbt_upsells_wc_fbt_enable_field").hide();
  }

  // if edd is not installed.
  if (!eddInstalled) {
    jQuery("input[name='ldfbt_upsells[ldfbt_plugin_type]'][value='edd']").attr('disabled', true);
    jQuery("#ldfbt_upsells_fbt_edd_section_field").hide();
    jQuery("#ldfbt_upsells_edd_fbt_enable_field").hide();
    jQuery("#ldfbt_upsells_edd_fbt_ero_enable_field").hide();
  }

  // if both plugin not installed disable every thing
  if ((!wocInstalled) && (!eddInstalled)) {
    jQuery("input[name='ldfbt_upsells[ldfbt_plugin_type]']").attr('disabled', true);
    jQuery("input[name='ldfbt_upsells[ldfbt_plugin_type]']").prop('checked', false);
    jQuery("#ldfbt_upsells_ldfbt_widget_heading").attr('disabled', true);
  }

  // enable/disables edd and wc option based on plugin option selected.
  jQuery(document).on("change", "input[name='ldfbt_upsells[ldfbt_plugin_type]']", function () {
    if (this.value === 'woocom') {
      jQuery("#ldfbt_upsells_ldfbt_discount_label_field").show();
      jQuery("#ldfbt_upsells_fbt_wocom_section_field").show();
      jQuery("#ldfbt_upsells_wc_fbt_enable_field").show();
      jQuery("#ldfbt_upsells_fbt_edd_section_field").hide();
      jQuery("#ldfbt_upsells_edd_fbt_enable_field").hide();
      jQuery("#ldfbt_upsells_edd_fbt_ero_enable_field").hide();
    } else {
      jQuery("#ldfbt_upsells_ldfbt_discount_label_field").hide();
      jQuery("#ldfbt_upsells_fbt_wocom_section_field").hide();
      jQuery("#ldfbt_upsells_wc_fbt_enable_field").hide();
      jQuery("#ldfbt_upsells_fbt_edd_section_field").show();
      jQuery("#ldfbt_upsells_edd_fbt_enable_field").show();
      jQuery("#ldfbt_upsells_edd_fbt_ero_enable_field").show();
    }
  });

  // redirect to repective cart page wc or edd.
  jQuery("#ldfbt-course-add-to-cart").click(function (event) {
    event.preventDefault();
    var postId = parseInt(jQuery(this).attr("data-course-id"));
    var userFBTProducts = jQuery("#ldfbt-products-added").attr('products-added');
    
    jQuery.ajax({
      method: 'POST',
      url: ldfbt_data.ajaxurl,
      data: {
        action: 'ldfbt_free_cart',
        postId: postId,
        userFBTProductsKey : userFBTProducts,
        ldfbtNonce:ldfbtNonce,
      },
      success: (res) => {
        var red_url = JSON.parse(res);
        if (red_url['woocom'])
          window.location.href = red_url['woocom']
        if (red_url['edd'])
          window.location.href = red_url['edd'];
      }
    });
  });

  // redirect to same page after rated.
  jQuery(".ldfbt_hide_rate").click(function (event) {
    event.preventDefault();
    jQuery.ajax({
      method: 'POST',
      url: ldfbt_data.ajaxurl,
      data: {
        action: 'ldfbt_update',
        nonce: ldfbt_data.nonce,
      },
      success: (res) => {
        window.location.href = window.location.href
      }
    });
  });

    // Free to Pro Upgarde
    jQuery('label[for="ldfbt_upsells_ldfbt_widget_position"]').append("<span class ='fbtld-pro-alert'> Pro </span>");
    jQuery('label[for ="ldfbt_upsells_ldfbt_widget_img_size"]').append("<span class ='fbtld-pro-alert'> Pro </span>");
    jQuery('label[for ="ldfbt_upsells_ldfbt_discount_label"]').append("<span class ='fbtld-pro-alert'> Pro </span>");
    jQuery('label[for ="ldfbt_upsells_wc_fbt_enable"]:first').append("<span class ='fbtld-pro-alert'> Pro </span>");
    jQuery('label[for ="ldfbt_upsells_edd_fbt_enable"]:first').append("<span class ='fbtld-pro-alert'> Pro </span>");
    jQuery('label[for ="ldfbt_upsells_edd_fbt_ero_enable"]:first').append("<span class ='fbtld-pro-alert'> Pro </span>");
    jQuery('#ldfbt_upsells_ldfbt_widget_position, #ldfbt_upsells_ldfbt_widget_img_size, #ldfbt_upsells_ldfbt_discount_label, #ldfbt_upsells_ldfbt_discount_label').prop('disabled', true);
    jQuery('#ldfbt_upsells_edd_fbt_enable, #ldfbt_upsells_edd_fbt_ero_enable, #ldfbt_upsells_wc_fbt_enable').prop('disabled', true);

    
    jQuery('label[for="ldfbt_upsells_ldfbt_widget_position"], label[for ="ldfbt_upsells_ldfbt_widget_img_size"], label[for ="ldfbt_upsells_ldfbt_discount_label"], label[for ="ldfbt_upsells_wc_fbt_enable"]:first,label[for ="ldfbt_upsells_edd_fbt_enable"]:first, label[for ="ldfbt_upsells_edd_fbt_ero_enable"]:first,  #ldfbt_upsells_ldfbt_widget_position_field .sfwd_option_input .sfwd_option_div .ld-select, #ldfbt_upsells_ldfbt_widget_img_size_field .sfwd_option_input .sfwd_option_div .ld-select, #ldfbt_upsells_ldfbt_discount_label_field .sfwd_option_input .sfwd_option_div').click(function () {
        Swal.fire({
            title: '<div class="pro-alert-header">'+popupTitle+'</div>',
            showCloseButton: true,
            html: '<div class="pro-crown"><svg xmlns="http://www.w3.org/2000/svg" height="100" width="100" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path fill="#f8c844" d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5 .4 5.1 .8 7.7 .8 26.5 0 48-21.5 48-48s-21.5-48-48-48z"/></svg></div><div class="popup-text-One">'+alertTitle+'</div><div class="popup-text-two">'+alertSubTitle+'</div> <ul><li><svg xmlns="http://www.w3.org/2000/svg" height="25" width="25" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ff3d3d" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>'+popUpPoint5+'</li><li><svg xmlns="http://www.w3.org/2000/svg" height="25" width="25" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ff3d3d" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>'+popUpPoint1+'</li><li><svg xmlns="http://www.w3.org/2000/svg" height="25" width="25" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ff3d3d" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>'+popUpPoint2+'</li><li><svg xmlns="http://www.w3.org/2000/svg" height="25" width="25" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ff3d3d" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>'+popUpPoint3+'</li><li><svg xmlns="http://www.w3.org/2000/svg" height="25" width="25" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ff3d3d" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>'+popUpPoint4+'</li> </ul>' + '<button class="ldfbt-upgrade-now" style="border: none"><a href="https://www.saffiretech.com/frequently-bought-together-for-learndash/?utm_source=wp_plugin&utm_medium=profield&utm_campaign=free2pro&utm_id=c1&utm_term=upgrade_now&utm_content=ldfbt" target="_blank" class="purchase-pro-link">'+ldfbtUpgradeNow+'</a></button>',
            customClass: "ldfbt-popup",
            showConfirmButton: false,
        });

        jQuery( '.ldfbt-popup' ).css('width', '800px');
        jQuery( '.ldfbt-popup > .swal2-header').css('background', '#061727' );
        jQuery( '.ldfbt-popup > .swal2-header').css('margin', '-20px' );
        jQuery( '.pro-alert-header' ).css('padding-top', '25px' );
        jQuery( '.pro-alert-header' ).css('padding-bottom', '20px' );
        jQuery( '.pro-alert-header' ).css( 'color', 'white' );
        jQuery( '.pro-crown' ).css( 'margin-top', '20px' );
        jQuery( '.popup-text-One').css( 'font-size', '30px' );
        jQuery( '.popup-text-One' ).css( 'font-weight', '600' );
        jQuery( '.popup-text-One' ).css( 'padding-bottom', '10px' );
        jQuery( '.ldfbt-popup > .swal2-content > .swal2-html-container > ul ' ).css( 'text-align', 'justify' );
        jQuery( '.ldfbt-popup > .swal2-content > .swal2-html-container > ul ' ).css( 'padding-left', '25px' );
        jQuery( '.ldfbt-popup > .swal2-content > .swal2-html-container > ul ' ).css( 'padding-right', '25px' );
        jQuery( '.ldfbt-popup > .swal2-content > .swal2-html-container > ul ' ).css( 'line-height', '2em' );
        jQuery( '.popup-text-two' ).css( 'padding', '10px' );
        jQuery( '.popup-text-two' ).css( 'font-weignt', '500');
        jQuery( '.ldfbt-popup > .swal2-content > .swal2-html-container > ul, .popup-text-One, .popup-text-two').css('color', '#061727' );
        
    });

    jQuery('.ldfbt-footer-upgrade').insertAfter('#post-body');
});
