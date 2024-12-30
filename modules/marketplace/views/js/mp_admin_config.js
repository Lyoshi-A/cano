/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/

 //hide and show seller details tab according to switch
 $(document).on("click", 'input[name="WK_MP_SHOW_SELLER_DETAILS"]', function() {
    hideAndShowSellerDetails();
});

//hide and show terms and condition text box according to switch
$(document).on("click", 'input[name="WK_MP_TERMS_AND_CONDITIONS_STATUS"]', function() {
    hideAndShowTermsCondition();
});

//hide and show multilang options text box according to switch
$(document).on("click", 'input[name="WK_MP_MULTILANG_ADMIN_APPROVE"]', function() {
    hideAndShowMultiLangAdminApprove();
});

//hide and show link rewrite text box according to switch
$(document).on("click", 'input[name="WK_MP_URL_REWRITE_ADMIN_APPROVE"]', function() {
    hideAndShowLinkRewriteURL();
});

//hide and show combination activate/deactive options for seller
$(document).on("click", 'input[name="WK_MP_SELLER_PRODUCT_COMBINATION"]', function() {
    hideAndShowMpCombinationActivateDeactivate();
});

//hide and show social tabs
$(document).on("click", 'input[name="WK_MP_SOCIAL_TABS"]', function() {
    hideAndShowMpSocialTab();
});

//hide and show order status
$(document).on("click", 'input[name="WK_MP_SELLER_ORDER_STATUS_CHANGE"]', function() {
    hideSellerOrderStatus();
});

//hide and show customer review settings
$(document).on("click", 'input[name="WK_MP_REVIEW_SETTINGS"]', function() {
    hideCustomerReviewSettings();
});

//hide and show shipping distribution settings
$(document).on("click", 'input[name="WK_MP_SHIPPING_DISTRIBUTION_ALLOW"]', function() {
    hideShippingDistributionSettings();
});

//hide and show ps tracking number update settings
$(document).on("click", 'input[name="WK_MP_SELLER_ORDER_TRACKING_ALLOW"]', function() {
    hidePsTrackingNumberSettings();
});

//hide and show seller manufacturer setting according to switch
$(document).on("click", 'input[name="WK_MP_PRODUCT_MANUFACTURER"]', function() {
    hideAndShowManufacturerSettings();
});

//hide and show seller supplier setting according to switch
$(document).on("click", 'input[name="WK_MP_PRODUCT_SUPPLIER"]', function() {
    hideAndShowSuppliersSettings();
});

//hide and show shipping distribution
$(document).on("click", 'input[name="MP_SHIPPING_DISTRIBUTION_ALLOW"]', function() {
    hideShowAdminShippingDistribution();
});

$(document).ready(function() {
    if ($('#wk_mp_commision_form_submit_btn:visible').length) {
        document.querySelector('#wk_mp_commision_form_submit_btn').addEventListener('click', function() {
            setTimeout(() => this.disabled = true)
        });
    }
    //Call on page load
    hideAndShowSellerDetails();
    hideAndShowTermsCondition();
    hideAndShowMultiLangAdminApprove();
    hideAndShowLinkRewriteURL();
    hideAndShowMpCombinationActivateDeactivate();
    hideAndShowMpSocialTab();
    hideSellerOrderStatus();
    hideCustomerReviewSettings();
    hideShippingDistributionSettings();
    hidePsTrackingNumberSettings();
    hideAndShowManufacturerSettings();
    hideAndShowSuppliersSettings();
    hideShowAdminShippingDistribution();

    if (typeof wk_commission_controller !== 'undefined') {
        //hide and show mp commission settings
        hideMpCommissionSettings(); //on page load

        $('#WK_MP_GLOBAL_COMMISSION_TYPE, #commision_type').on("change", function() {
            hideMpCommissionSettings();
        });

        $('#WK_MP_PRODUCT_TAX_DISTRIBUTION').on("change", function() {
            hideMpTaxCommissionSettings();
        });
    }

    // If color picker is not working  background image for color then we have to change the path.
    if (typeof color_picker_custom != 'undefined') {
        $.fn.mColorPicker.defaults.imageFolder = '../img/admin/';
    }

    // Tab panel in configuration page
    if (typeof current_config_tab !== 'undefined') {
        $('.wk_config_tab').hide();
        var tab = current_config_tab;
        $('#wk_config_tab_' + tab).addClass('active');
        $('#wk_config_' + tab).show();
        $('.wk_config_tab_link').on('click', function (e) {
            var tab = $(this).data('tab');
            $('.wk_config_tab').hide();
            $('#wk_config_tab_' + tab).removeClass('active');
            $('#wk_config_' + tab).show();
            $('#current_config_tab').val(tab);
            current_config_tab = tab;
        });
    }

    //Assign admin carriers to Admin products
    $(document).on("click", '#assign_shipping', function(e) {
        e.preventDefault();
        $('#wk-loader').html('<img src="'+module_dir+'marketplace/views/img/loader.gif">');
        $.ajax({
            url: ajaxurl_approval_settings_url,
            data: {
                action: "updateCarrierToMainProducts",
                ajax: "1"
            },
            dataType: "json",
            //async: false,
            success: function(result) {
                $('#wk-loader').html('');
                if (result.status == 'ok') {
                    $('.bootstrap').show();
                    $('.module_confirmation').show();
                    $('.module_confirmation span').text(result.msg);
                } else {
                    $('.bootstrap').show();
                    $('.module_error').show();
                    $('.module_error span').text(result.msg);
                }
                $('.loader').hide();
            }
        });
        return false;
    });
});

function hideAndShowSellerDetails() {
    if ($('input[name="WK_MP_SHOW_SELLER_DETAILS"]:checked').val() == 1) {
        $(".wk_mp_seller_details").show();
    } else {
        $(".wk_mp_seller_details").hide();
    }
}

function hideAndShowTermsCondition() {
    if ($('input[name="WK_MP_TERMS_AND_CONDITIONS_STATUS"]:checked').val() == 1) {
        $(".wk_mp_termsncond").show();
    } else {
        $(".wk_mp_termsncond").hide();
    }
}

function hideAndShowMultiLangAdminApprove() {
    if ($('input[name="WK_MP_MULTILANG_ADMIN_APPROVE"]:checked').val() == 1) {
        $('.multilang_def_lang').hide();
    } else {
        $('.multilang_def_lang').show();
    }
}

function hideAndShowLinkRewriteURL() {
    if ($('input[name="WK_MP_URL_REWRITE_ADMIN_APPROVE"]:checked').val() == 1) {
        $('.mp_url_rewrite').show();
    } else {
        $('.mp_url_rewrite').hide();
    }
}

function hideAndShowMpCombinationActivateDeactivate() {
    if ($('input[name="WK_MP_SELLER_PRODUCT_COMBINATION"]:checked').val() == 1) {
        $('.wk_mp_combination_customize').show();
    } else {
        $('.wk_mp_combination_customize').hide();
    }
}

function hideAndShowMpSocialTab() {
    if ($('input[name="WK_MP_SOCIAL_TABS"]:checked').val() == 1) {
        $('.wk_mp_social_tab').show();
    } else {
        $('.wk_mp_social_tab').hide();
    }
}

function hideSellerOrderStatus() {
    if ($('input[name="WK_MP_SELLER_ORDER_STATUS_CHANGE"]:checked').val() == 1) {
        $('.wk_mp_seller_order_status').show('slow');
    } else {
        $('.wk_mp_seller_order_status').hide('slow');
    }
}

function hideCustomerReviewSettings() {
    if ($('input[name="WK_MP_REVIEW_SETTINGS"]:checked').val() == 1) {
        $('.mp_review_settings').show('slow');
    } else {
        $('.mp_review_settings').hide('slow');
    }
}

function hideShippingDistributionSettings() {
    if ($('input[name="WK_MP_SHIPPING_DISTRIBUTION_ALLOW"]:checked').val() == 1) {
        $('.mp_shipping_distribution').show('slow');
    } else {
        $('.mp_shipping_distribution').hide('slow');
    }
}

function hidePsTrackingNumberSettings() {
    if ($('input[name="WK_MP_SELLER_ORDER_TRACKING_ALLOW"]:checked').val() == 1) {
        $('.wk_mp_tracking_ps_update').show('slow');
    } else {
        $('.wk_mp_tracking_ps_update').hide('slow');
    }
}

if (typeof wk_commission_controller !== 'undefined') {
    function hideMpCommissionSettings() {
        if ($('#WK_MP_GLOBAL_COMMISSION_TYPE, #commision_type').val() == wk_percentage) {
            $('.wk_mp_commission_rate').show('slow');
            $('.wk_mp_commission_amt').hide();
        } else if ($('#WK_MP_GLOBAL_COMMISSION_TYPE, #commision_type').val() == wk_fixed) {
            $('.wk_mp_commission_amt').show('slow');
            $('.wk_mp_commission_rate').hide();
        } else if ($('#WK_MP_GLOBAL_COMMISSION_TYPE, #commision_type').val() == wk_both_type) {
            $('.wk_mp_commission_rate').show('slow');
            $('.wk_mp_commission_amt').show('slow');
        }

        //Manage tax fixed amount
        hideMpTaxCommissionSettings();
    }

    function hideMpTaxCommissionSettings() {
        if (typeof $('#WK_MP_PRODUCT_TAX_DISTRIBUTION').val() !== 'undefined') {
            var tax_distribution_type = $('#WK_MP_PRODUCT_TAX_DISTRIBUTION').val();
        } else {
            var tax_distribution_type = product_tax_distribution;
        }

        if ((tax_distribution_type == 'distribute_both')
        && ($('#WK_MP_GLOBAL_COMMISSION_TYPE, #commision_type').val() != wk_percentage)) {
            $('.wk_mp_commission_amt_on_tax').show('slow');
        } else {
            $('.wk_mp_commission_amt_on_tax').hide();
        }
    }
}

function hideAndShowManufacturerSettings() {
    if ($('input[name="WK_MP_PRODUCT_MANUFACTURER"]:checked').val() == 1) {
        $('[name="WK_MP_PRODUCT_MANUFACTURER_ADMIN"]').parent().closest('.form-group').show('slow');
        $('[name="WK_MP_PRODUCT_MANUFACTURER_APPROVED"]').parent().closest('.form-group').show('slow');
    } else {
        $('[name="WK_MP_PRODUCT_MANUFACTURER_ADMIN"]').parent().closest('.form-group').hide('slow');
        $('[name="WK_MP_PRODUCT_MANUFACTURER_APPROVED"]').parent().closest('.form-group').hide('slow');
    }
}

function hideAndShowSuppliersSettings() {
    if ($('input[name="WK_MP_PRODUCT_SUPPLIER"]:checked').val() == 1) {
        $('[name="WK_MP_PRODUCT_SUPPLIER_ADMIN"]').parent().closest('.form-group').show('slow');
        $('[name="WK_MP_PRODUCT_SUPPLIER_APPROVED"]').parent().closest('.form-group').show('slow');
    } else {
        $('[name="WK_MP_PRODUCT_SUPPLIER_ADMIN"]').parent().closest('.form-group').hide('slow');
        $('[name="WK_MP_PRODUCT_SUPPLIER_APPROVED"]').parent().closest('.form-group').hide('slow');
    }
}

function hideShowAdminShippingDistribution() {
    if ($('input[name="MP_SHIPPING_DISTRIBUTION_ALLOW"]:checked').val() == 1) {
        $(".wk-admin-shipping-distribute").show();
    } else {
        $(".wk-admin-shipping-distribute").hide();
    }
}

$(window).ready(function() {
    setTimeout(() => {
        $('.mp_shipping_distribution').css('height', 'unset').css('width', 'unset');
    }, 500);
    if (typeof moduleAdminLink != 'undefined') {
        moduleAdminLink = moduleAdminLink.replace(/\amp;/g,'');

        window.vMenu = new Vue({
            el: '#bar-menu',
            data: {
                selectedTabName : current_config_tab,
            },
            methods: {
                makeActive: function(item){
                    this.selectedTabName = item;
                    window.history.pushState({} , '', moduleAdminLink+'&page='+item );
                },
                isActive : function(item){
                    if (this.selectedTabName == item) {
                        $('.wk_bar-menu').addClass('wk_display_none');
                        $('#'+item).removeClass('wk_display_none');
                        return true;
                    }
                }
            }
        });
        $('#'+current_config_tab).removeClass('wk_display_none');
    }
});
