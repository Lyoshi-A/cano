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
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/

$(document).ready(function() {
    //display mp shipping list in popup
    $(document).on('click', '.assign_shipping', function(e) {
        if ($('#mp_productlist_form td input:checkbox').length > 0) {
            e.preventDefault();
            $('#assign_shipping_form').modal('show');
        } else {
            e.preventDefault();
            showErrorMessage(no_products_error);
        }
    });

    //Assign shipping on products
    $('#assign').click(function(e) {
        e.preventDefault();
        var form = $('#shipping_form');

        if ($(':checkbox:checked').length > 0) {
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                async: true,
                cache: false,
                data: form.serialize() + "&action=assignShipping&ajax=1",
                success: function(dataresult) {
                    $('#assign_shipping_form').hide();
                    $('.modal-backdrop.in').css('opacity', 0);
                    if (dataresult == 1) {
                        $.growl.notice({ title: "", message: success_msg });
                    } else {
                        $.growl.error({ title: "", message: error_msg });
                    }
                    setTimeout(() => {
                        window.location.href = window.location.href;
                    }, 500);
                }
            });
        } else {
            $.growl.error({ title: "", message: check_msg });
        }
    });
});