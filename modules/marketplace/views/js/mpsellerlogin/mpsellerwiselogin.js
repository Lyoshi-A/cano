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
$(document).ready(function() {

	$('#content-wrapper').parent().removeClass('row');

	//Display cms page in modal box
    $('.wk_terms_link').on('click', function() {
        var linkCmsPageContent = $(this).attr('href');
        $('#wk_terms_condtion_content').load(linkCmsPageContent, function() {
            //remove extra content
            $('#wk_terms_condtion_content section#wrapper').css({ "background-color": "#fff", "padding": "0px", "box-shadow": "0px 0px 0px #fff" });
            $('#wk_terms_condtion_content .breadcrumb').remove();
            $('#wk_terms_condtion_content header').remove();
            //display content
            $('#wk_terms_condtion_div').modal('show');
        });
        return false;
	});

	$('#account_btn').on('click', function() {
		var firstname = $('#firstname').val();
		var lastname = $('#lastname').val();
		var email = $('#email').val();
		var passwd = $('#passwd').val();
		var ps_customer_id = $('#ps_customer_id').val();

		if (ps_customer_id != '') {
			$('.login_act_err').show().find('.mp_error').text(emailIdError);
		} else if (firstname == '' || lastname == ''|| email == ''|| passwd == '') {
			$('.login_act_err').show().find('.mp_error').text(allFieldMandatoryError);
		} else if (!validate_isName(firstname)) {
			$('.login_act_err').show().find('.mp_error').text(firstNameError);
		} else if (!validate_isName(lastname)) {
			$('.login_act_err').show().find('.mp_error').text(lastNameError);
		} else if (!validate_isEmail(email)) {
			$('.login_act_err').show().find('.mp_error').text(invalidEmailIdError);
		} else if (passwd.length < 5) {
			$('.login_act_err').show().find('.mp_error').text(passwordLengthError);
		} else if (!validate_isPasswd(passwd)) {
			$('.login_act_err').show().find('.mp_error').text(invalidPasswordError);
		} else {
			$('.form_wrapper').toggle();
			$('.login_act_err').hide().find('.mp_error').text('');
		}
	});

	$('.wk_login_field').on('click', function() {
		$(this).css("background-image", "none");
	});

	$('#mp_register_form').on('submit', function() {
		var seller_default_lang = $('.seller_default_shop').data('lang-name');
		var mp_shop_name = $('.seller_default_shop').val().trim();
		var mp_unique_shop_name = $('#mp_shop_name_unique').val().trim();
		var mp_seller_email = $('#mp_seller_email').val().trim();
		var mp_seller_phone = $('#mp_seller_phone').val().trim();

		if (mp_unique_shop_name == '' || mp_seller_phone == '') {
			$('.login_shop_err').show().find('.mp_error').text(allFieldMandatoryError);
			return false;
		} else if($('#terms_and_conditions').prop("checked") == false) {
			$('.login_shop_err').show().find('.mp_error').text(termConditionError);
			$('#terms_and_conditions').focus();
			return false;
		} else if(!validate_shopname(mp_unique_shop_name)) {
			$('.login_shop_err').show().find('.mp_error').text(invalidUniqueShopNameError);
			$('#mp_shop_name_unique').focus();
			return false;
		} else if(checkUniqueShopName(mp_unique_shop_name, 1)) {
			$('#mp_shop_name_unique').focus();
			return false;
		} else if(mp_shop_name == '') {
			if ($('#multi_lang').val() == '1') {
				$('.login_shop_err').show().find('.mp_error').text(shopNameRequiredLang + ' ' +seller_default_lang);
			} else {
				$('.login_shop_err').show().find('.mp_error').text(shopNameRequired);
			}

			$('.seller_default_shop').focus();
			return false;
		} else if(!validate_shopname(mp_shop_name)) {
			$('.login_shop_err').show().find('.mp_error').text(invalidShopNameError);
			$('.seller_default_shop').focus();
			return false;
		} else if (!validate_isEmail(mp_seller_email)) {
			$('.login_shop_err').show().find('.mp_error').text(invalidEmailIdError);
			$('#mp_seller_email').focus();
			return false;
		} else if (!validate_isPhoneNumber(mp_seller_phone)) {
			$('.login_shop_err').show().find('.mp_error').text(phoneNumberError);
			$('#mp_seller_phone').focus();
			return false;
		} else if (MP_SELLER_COUNTRY_NEED !== '0') {
			if ($('#seller_city').val().trim() == '') {
				$('.login_shop_err').show().find('.mp_error').text(cityNameRequired);
				$('#seller_city').focus();
				return false;
			} else if (!validate_isName($('#seller_city').val().trim())) {
				$('.login_shop_err').show().find('.mp_error').text(invalidCityNameError);
				$('#seller_city').focus();
				return false;
			}
		} else {
			return true;
		}
	});

	$('#mp_login_form').on('submit', function() {
		var email = $('#login_email').val();
		var passwd = $('#login_passwd').val();
		if (email == '') {
			$('#login_email').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		} else if (passwd == '') {
			$('#login_passwd').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		} else if (!validate_isEmail(email)) {
			$('#login_email').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		} else if (!validate_isPasswd(passwd)) {
			$('#login_passwd').css({"background-image": "url("+modImgDir+"icon-close.png)", "background-repeat": "no-repeat", "background-position": "98%  center"});
			return false;
		} else {
			return true;
		}
	});

	$('#email').on('focus', function() {
		$('.login_act_err').hide();
		$('.check_email').hide();
		$('#ps_customer_id').val('');
		$('#idSeller').val('');
	});

	$('#email').on('blur', function() {
		var email = $(this).val();
		if (validate_isEmail(email) && email) {
			$(this).css({"background-image": "url("+modImgDir+"loader.gif)", "background-repeat": "no-repeat", "background-position": "98%  center"});

			$.ajax({
				url: checkCustomerAjaxUrl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					user_email: email,
					id_seller: id_seller !== 'undefined' ? id_seller : false,
					case: 'checkEmailRegister',
					action: 'sellerLogin',
					ajax: 1,
					token: wk_static_token
				},
				async: false,
				success: function(result) {
					$('#email').css("background-image", "none");
					if (result) {
						$('#ps_customer_id').val(parseInt(result.idCustomer));
						$('#mp_seller_email').val($('#email').val());
						$('.check_email').show();

						if (!parseInt(result.idSeller)) {
							$('.login_act_err').hide();
						} else {
							$('#idSeller').val(parseInt(result.idSeller));
						}
					}
				}
			});
		}
	});

	$('#wk-shop-form').on('click', function() {
		if ($('#idSeller').val()) {
			$('.login_act_err').show().find('.mp_error').text(emailAlreadyExist);
		} else if (!$('#passwd').val()) {
			$('.login_act_err').show().find('.mp_error').text(passwordRequiredError);
		} else {
			$('.form_wrapper').toggle();
		}
	});

	$('#back_account').on('click', function() {
		$('.form_wrapper').toggle();
	});

	var wk_slerror = $('#wk_slerror').val();
	if (wk_slerror) {
		$.growl.error({ title: "", message: $('.error_block').text() });
	}

	$('#seller_default_lang').on("change", function(e) {
		e.preventDefault();
		if ($('#multi_lang').val() == '1') {
			var select_lang_iso = $(this).find("option:selected").data('lang-iso');
			var select_lang_id = $(this).val();

			showLangField(select_lang_iso, select_lang_id);

			$('.shop_name_all').removeClass('seller_default_shop');
			$('#mp_shop_name_'+select_lang_id).addClass('seller_default_shop');
		}
	});

	$("#mp_shop_name_unique").on('blur', function() {
		var shop_name_unique = $(this).val().trim();
		if (checkUniqueShopName(shop_name_unique)) {
	        $(this).focus();
	        return false;
	    }
	});

	$("#seller_country").on('change', function() {
		var id_country = $(this).val();
		getState(id_country);
	});

	if ($('#form_shop_info').length) {
		$('#form_shop_info').prepend($('aside#notifications').html());
		$('aside#notifications').remove();
	} else {
		$('aside#notifications').show();
	}
});

function getState(id_country)
{
	$.ajax({
		method:"POST",
		url:checkCustomerAjaxUrl,
		data: {
			id_country: id_country,
			case: 'getSellerState',
			action: 'sellerLogin',
			ajax: 1,
			token: wk_static_token
		},
		success: function(result) {
			if (result) {
				$("#sellerStateCont").show();
				$("#seller_state").empty();
				$.each(jQuery.parseJSON(result), function(index, state) {
					$("#seller_state").append('<option value='+state.id_state+'>'+state.name+'</option>');
				});

				// Code if "Move JavaScript to the end" option is truned ON
				$("#uniform-seller_state > span").remove();
				$("#uniform-seller_state").css('width','100%');

				$("#state_avl").val(1);
			} else {
				$("#sellerStateCont").hide();
				$("#seller_state").empty();
				$("#state_avl").val(0);
			}
		}
	});
}

function showLangField(lang_iso_code, id_lang)
{
	$('#shop_name_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');
	$('#address_lang_btn').html(lang_iso_code + ' <span class="caret"></span>');

	$('.shop_name_all').hide();
	$('#mp_shop_name_'+id_lang).show();

	$('.address_all').hide();
	$('#mp_shop_address_'+id_lang).show();

}

function validate_shopname(name)
{
	if (!/^[^<>;=#{}]*$/i.test(name)) {
		return false;
	} else {
		return true;
	}
}

function validate_isEmail(email) {
    var reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return reg.test(email);
}

function validate_isName(name) {
    var reg = /^[^0-9!<>,;?=+()@#"°{}_$%:]+$/;
    return reg.test(name);
}

function validate_isPhoneNumber(phonenumber) {
    var reg = /^[+0-9. ()-]+$/;
    return reg.test(phonenumber);
}

var id_seller;
var shop_name_exist = false;
function checkUniqueShopName(shop_name, isformSubmit = 0)
{
	if (shop_name.trim()) {
		if (!isformSubmit) {
			$('#mp_shop_name_unique').css({"background-image": "url("+modImgDir+"loader.gif)", "background-repeat": "no-repeat", "background-position": "98%  center"});
		}
		$.ajax({
	        url: checkCustomerAjaxUrl,
	        type: "POST",
	        data: {
	            shop_name: shop_name,
	            case: 'checkUniqueShopName',
				action: 'sellerLogin',
				ajax: 1,
				token: wk_static_token
	        },
	        async: false,
	        success: function(result) {
	        	if (!isformSubmit) {
					$('#mp_shop_name_unique').css("background-image", "none");
				}
	   			if (result == 1) {
					$('.login_shop_err').show().find('.mp_error').text(shopNameAlreadyExist);
					shop_name_exist = true;
				}
				else if (result == 2) {
					$('.login_shop_err').show().find('.mp_error').text(shopNameError);
					shop_name_exist = true;
				}
				else {
					$('.login_shop_err').hide();
					shop_name_exist = false;
				}
	        },
			error: function(error) {
			}
	    });
	}

	return shop_name_exist;
}