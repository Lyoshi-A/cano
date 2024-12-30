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

$(document).ready(function()
{
	$('#banner_img-selectbutton').click(function(e)
	{
		$('#banner_img').trigger('click');
	});

	$('#banner_img-name').click(function(e)
	{
		$('#banner_img').trigger('click');
	});

	$('#banner_img-name').on('dragenter', function(e)
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#banner_img-name').on('dragover', function(e)
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#banner_img-name').on('drop', function(e)
	{
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		$('#banner_img')[0].files = files;
		$(this).val(files[0].name);
	});

	$('#banner_img').change(function(e)
	{
		if ($(this)[0].files !== undefined)
		{
			var files = $(this)[0].files;
			var name  = '';

			$.each(files, function(index, value) {
				name += value.name+', ';
			});

			$('#banner_img-name').val(name.slice(0, -2));
		}
		else // Internet Explorer 9 Compatibility
		{
			var name = $(this).val().split(/[\\/]/);
			$('#banner_img-name').val(name[name.length-1]);
		}
	});

	if (typeof banner_img_max_files !== 'undefined')
	{
		$('#banner_img').closest('form').on('submit', function(e)
		{
			if ($('#banner_img')[0].files.length > banner_img_max_files)
			{
				e.preventDefault();
				alert('You can upload a maximum of  files');
			}
		});
	}


	$('#wk_logo-selectbutton').click(function(e)
	{
		$('#wk_logo').trigger('click');
	});

	$('#wk_logo-name').click(function(e)
	{
		$('#wk_logo').trigger('click');
	});

	$('#wk_logo-name').on('dragenter', function(e)
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#wk_logo-name').on('dragover', function(e)
	{
		e.stopPropagation();
		e.preventDefault();
	});

	$('#wk_logo-name').on('drop', function(e)
	{
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		$('#wk_logo')[0].files = files;
		$(this).val(files[0].name);
	});

	$('#wk_logo').change(function(e)
	{
		if ($(this)[0].files !== undefined)
		{
			var files = $(this)[0].files;
			var name  = '';

			$.each(files, function(index, value) {
				name += value.name+', ';
			});

			$('#wk_logo-name').val(name.slice(0, -2));
		}
		else // Internet Explorer 9 Compatibility
		{
			var name = $(this).val().split(/[\\/]/);
			$('#wk_logo-name').val(name[name.length-1]);
		}
	});

	if (typeof wk_logo_max_files !== 'undefined')
	{
		$('#wk_logo').closest('form').on('submit', function(e)
		{
			if ($('#wk_logo')[0].files.length > wk_logo_max_files)
			{
				e.preventDefault();
				alert('You can upload a maximum of  files');
			}
		});
	}

	/*---- Select Theme Controller Js ----*/

	$('#login_theme').on('change', function()
	{
		var id_theme = $('#login_theme').val();
		$('#theme_preview').attr( "src", preview_img_dir+'theme'+id_theme+'.jpg');
	});

	/*---- Select Theme Controller Js ----*/

	hideAndShowSellerLoginFields($('input[name="regTitleBlockActive"]:checked').data('block'), $('input[name="regTitleBlockActive"]:checked').val());
	hideAndShowSellerLoginFields($('input[name="regBlockActive"]:checked').data('block'), $('input[name="regBlockActive"]:checked').val());
	hideAndShowSellerLoginRegBlock($('input[name="regPBlockActive"]:checked').data('block'), $('input[name="regPBlockActive"]:checked').val());

	hideAndShowSellerLoginFields($('input[name="contentPBlockActive"]:checked').data('block'), $('input[name="contentPBlockActive"]:checked').val());
	hideAndShowSellerLoginFields($('input[name="featureBlockActive"]:checked').data('block'), $('input[name="featureBlockActive"]:checked').val());
	hideAndShowSellerLoginFields($('input[name="tcBlockActive"]:checked').data('block'), $('input[name="tcBlockActive"]:checked').val());

	$('input[name="regPBlockActive"]').on("click", function() {
        hideAndShowSellerLoginRegBlock($(this).data('block'), $(this).val());
    });
	$('.wk_enable_btn').on("click", function() {
        hideAndShowSellerLoginFields($(this).data('block'), $(this).val());
    });
});

function hideAndShowSellerLoginRegBlock(wk_block_id, wk_switch_val) {
    if (wk_switch_val == 1) {
        $(".wk_block_"+wk_block_id).show('slow');
		if ($('input[name="regTitleBlockActive"]:checked').val() == 1) {
			$(".wk_block_"+wk_block_id+"1").show('slow');
		}
		if ($('input[name="regBlockActive"]:checked').val() == 1) {
			$(".wk_block_"+wk_block_id+"2").show('slow');
		}
    } else {
		$(".wk_block_"+wk_block_id).hide('slow');
		$(".wk_block_"+wk_block_id+"1").hide('slow');
		$(".wk_block_"+wk_block_id+"2").hide('slow');
    }
}

function hideAndShowSellerLoginFields(wk_block_id, wk_switch_val) {
    if (wk_switch_val == 1) {
        $(".wk_block_"+wk_block_id).show('slow');
    } else {
		$(".wk_block_"+wk_block_id).hide('slow');
    }
}