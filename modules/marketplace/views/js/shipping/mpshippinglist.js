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
    $(".delete_shipping").on("click", function() {
        if ($(this).data('prod')) {
            var mpshipping_id = $(this).data('shipping-id');
            if (mpshipping_id) {
                $.ajax({
                    type: 'POST',
                    url: ajaxurl_shipping_extra,
                    async: true,
                    cache: false,
                    dataType: 'json',
                    data: {
                        mpshipping_id: mpshipping_id,
                        'delete_action': 1
                    },
                    success: function(data) {
                        $('#delete_shipping_id').val(mpshipping_id);
                        $('#extra_shipping option').remove();
                        if (Array.isArray(data) && data.length > 0) {
                            $('#extra_shipping').html(data.map(d => `<option value="${d.id}">${d.name}</option>`));
                        }
                        var selectObject = $('#extra_shipping option');
                        if (!selectObject.length) {
                            $('#shippingactive').remove();
                            $('#noshippingactive').show();
                        }
                    }
                });

                $('.delete_shipping').fancybox();
            }
        } else {
            if (!confirm(confirm_msg)) {
                return false;
            }
        }
    });

    $('#add_default_shipping').on('click', function() {
        $('#default_shipping_div').slideDown();
        $('#default_shipping_show').hide();
    });

    $('#cancel_default_shipping').on('click', function() {
        $('#default_shipping_div').slideUp();
        $('#default_shipping_show').show();
    });

    if (typeof wk_dataTables != 'undefined') {
        $('#mp_shipping_list').DataTable({
            "language": {
                "lengthMenu": display_name + " _MENU_ " + records_name,
                "zeroRecords": no_product,
                "info": show_page + " _PAGE_ " + show_of + " _PAGES_ ",
                "infoEmpty": no_record,
                "infoFiltered": "(" + filter_from + " _MAX_ " + t_record + ")",
                "sSearch": search_item,
                "oPaginate": {
                    "sPrevious": p_page,
                    "sNext": n_page
                }
            },
            "order": [
                [0, "desc"]
            ]
        });

        $('select[name="mp_shipping_list_length"]').addClass('form-control-select');
    }

    $("#submit_default_shipping").on("click", function(e) {
        e.preventDefault()
        $.ajax({
            type: 'POST',
            url: ajaxurl_shipping_extra,
            async: true,
            cache: false,
            dataType: 'json',
            data: $('.default_shipping_form').serialize() + "&action=UpdatedefaultShipping&ajax=1",
            success: function(data) {
                if (data == 1) {
                    $.growl.notice({ title: "", message: updated_default_shipping });
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else if (data == 2) {
                    $.growl.error({ title: "", message: no_shipping });
                }
            }
        });
    });

    $(document).on('click', '.mp_bulk_carrier_delete_btn', function(e) {
        e.preventDefault();
        var selectedCarrier = [];

        document.querySelectorAll('.mp_bulk_select').forEach(function(box) {
            if (box.checked) {
                selectedCarrier.push(box.value);
            }
        })

        if (selectedCarrier.length > 0) {
            if (confirm(confirm_msg)) {
                $(".loading_overlay").show();
                $(".loading_overlay").html("<img class='loading-img' src='" + module_dir + "marketplace/views/img/loader.gif'>");

                $.ajax({
                    url: ajaxurl_shipping_extra,
                    data: {
                        remove_bulk_carrier: 1,
                        mp_shipping_IDs: JSON.stringify(selectedCarrier),
                        token: static_token,
                        ajax: true,
                        action: 'bulkDeleteCarrier'
                    },
                    method: 'POST',
                    success: function(data) {
                        $(".loading_overlay").html('');
                        $(".loading_overlay").hide();
                        if (data) {
                            $('#deletecarrierajax').css("display", "block");
                            selectedCarrier.forEach((mp_shipping_id) => {
                                $('#shippingid_' + mp_shipping_id).remove();
                            })
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '#mp_all_carriers', function() {
        let boxes = document.querySelectorAll('.mp_bulk_select');
        if (this.checked) {
            boxes.forEach((box) => {
                if (!box.checked) {
                    box.checked = true;
                }
            })
        } else {
            boxes.forEach((box) => {
                box.checked = false;
            })
        }
    });
});