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
	var xhr = ''; // This variable is used to abort ajax

	$('.mp_search_box').prop( "autocomplete", "off" );     /*browser autocomplete off*/
	$('body').on('keyup', '.mp_search_box',function(event)
	{
		if (($(".mp_search_sugg").html()) && (event.which == 40 || event.which == 38))
		{
			event.preventDefault();
			$(this).focusout();

			if (event.which == 40)
				$(".mp_search_sugg").find('li').first().find('a').attr('tabindex',0).focus();
			else if (event.which == 38)
				$(".mp_search_sugg").find('li').last().find('a').attr('tabindex',0).focus();
		}
		else
		{
			$(".mp_search_sugg").html('');
			var key_word = '';
			key_word = $(this).val();
			var search_for = $('.search_value').data('value');
			var data = {key:key_word, search_type:search_for};
			if (xhr)
				xhr.abort();

			if (key_word)
			{
				xhr = $.ajax({
					url: ajaxsearch_url,
					type: 'POST',
					dataType: 'json',
					data: data,
					success: function (result)
					{
						if (result)
						{
							$.each(result, function(key, value)
							{
								$(".mp_search_sugg").show().append("<li><a href='"+value.shop_link+"' class='search_a'>"+value.mp_seller_name+", "+value.shop_name+"</a></li>");
							});
						}
						else
						{
							$(".mp_search_sugg").hide();
						}
					},
					error: function(error)
					{
						console.log(error);
					}
				});
			}
			else
				$(".mp_search_sugg").hide();
		}
	});

	$('body').on('click', function(event)
	{
		$(".mp_search_sugg").hide().html('');
	});

	$('.mp_search_sugg').on('click',function(event)
	{
		event.stopPropagation();
	});

	$('.search_category').on('click', function(e)
	{
		e.preventDefault();
		var search_value = $(this).data('value');
		var search_for = $(this).html();

		$('#search_for').html(search_for);
		$('.search_value').data('value', search_value);
		$('#dropdownMenu1').attr('data-value', search_value);
	});

	$('body').on('keyup', '.search_a', function(event)
	{
		if (event.which == 40 || event.which == 38)
		{
			$(this).focusout();
			if (event.which == 40)
			{
				if ($(".mp_search_sugg").find('li').last() == $(this).parent())
				{
					$(".mp_search_sugg").find('li').first().find('a').attr('tabindex',0).focus();
				}
				else
				{
					$(this).parent().next().find('a').attr('tabindex',0).focus()
				}
			}
			else if (event.which == 38)
			{
				if ($(".mp_search_sugg").find('li').first() == $(this).parent())
				{
					$(".mp_search_sugg").find('li').last().find('a').attr('tabindex',0).focus();
				}
				else
				{
					$(this).parent().prev().find('a').attr('tabindex',0).focus()
				}
			}
		}
	});

	/*$(document).on('keydown','body', function (e)
	{
		if((e.which == 40 || e.which == 38) && $('.search_a').is(':focus'))
		{
			e.preventDefault();
			return false;
		}
	});*/

	// sorting product based on product name price or asc desc order
	$('#wk_orderby').on('change', function(){
		if (PS_REWRITING_SETTINGS == 1)
			var add_sign = "?";
		else
			var add_sign = "&";

		if ($('#wk_orderby option:selected').index() == '1')
			ajaxsort_url = ajaxsort_url+add_sign+"orderby=price&orderway=asc";
		else if ($('#wk_orderby option:selected').index() == '2')
			ajaxsort_url = ajaxsort_url+add_sign+"orderby=price&orderway=desc";
		else if ($('#wk_orderby option:selected').index() == '3')
			ajaxsort_url = ajaxsort_url+add_sign+"orderby=name&orderway=asc";
		else if ($('#wk_orderby option:selected').index() == '4')
			ajaxsort_url = ajaxsort_url+add_sign+"orderby=name&orderway=desc";
		else
			ajaxsort_url = ajaxsort_url;
		window.location.href = ajaxsort_url;
	});

	// view more product
	$('#wk-more-product').on('click', function(e){
		e.preventDefault();
		$('div.wk_view_more').css('margin-bottom','65px');
		$('.btn-all').css('display', 'none');
		$('.view-more-img').css('display', 'block');

		var lastid = $(this).parent().attr('data-count-prod');
		var orderby = $('#orderby').val();
		var orderway = $('#orderway').val();
		if (!orderby)
			orderby = 0;
		if (!orderway)
			orderway = 0;
		var moreproductid = parseInt(lastid) + 8;
		$.ajax({
			url : viewmore_url,
			type: 'POST',
			dataType: 'json',
			data : {
				nextid : lastid,
				orderby : orderby,
				orderway : orderway,
			},
			success : function(data)
			{
				if (data == 0)
				{
					$('.view-more-img').css('display', 'none');
					$('.btn-all').css({'display':'inline-block','pointer-events':'none'}).text(no_more_prod);
					$('div.wk_view_more').css('margin-bottom','25px');
				}
				else if (data)
				{
					$('.view-more-img').css('display', 'none');
					$('.btn-all').css('display', 'inline-block');
					$('div.wk_view_more').css('margin-bottom','25px');

					$(".wk_view_more").before(data.html);
					$('.wk_view_more').attr('data-count-prod', moreproductid);

					if (data.view_more == 0) {
						$('.view-more-img').css('display', 'none');
						$('.btn-all').css({'display':'inline-block','pointer-events':'none'}).text(no_more_prod);
						$('div.wk_view_more').css('margin-bottom','25px');
					}
				}
			}
		});
	});

	// seller search with seller shop and seller name
	$('#mpseller-search').on('click', function(){
		var key = $('#seller-search').val();
		var orderby = $('#dropdownMenu1').attr('data-value');
		key = $.trim(key);
		if (key && orderby)
		{
			if (orderby == 1)
				search_url = viewmorelist_link+'&orderby=seller_name&name='+key;
			else if (orderby == 2)
				search_url = viewmorelist_link+'&orderby=shop_name&name='+key;
			else if (orderby == 3)
				search_url = viewmorelist_link+'&orderby=address&name='+key;

			window.location.href = search_url;
		}
		else {
			$('#seller-search').css('border-color', 'red');
			return;
		}
	});
	$(document).on('keyup','#seller-search',function(e){
		if(e.which == 13)
		{
			var key = $('#seller-search').val();
			var orderby = $('#dropdownMenu1').attr('data-value');
			key = $.trim(key);
			if (key && orderby)
			{
				if (orderby == 1)
					search_url = viewmorelist_link+'&orderby=seller_name&name='+key;
				else if (orderby == 2)
					search_url = viewmorelist_link+'&orderby=shop_name&name='+key;
				else if (orderby == 3)
					search_url = viewmorelist_link+'&orderby=address&name='+key;

				window.location.href = search_url;
			}
		}
	});
});