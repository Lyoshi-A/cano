{*
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
*}

<div id="mp_product_shipping_tab">
	<img src="{$mp_module_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loader.gif" id="ajax_loader" style="display: none;z-index: 10000;position: absolute;top: 20%;left: 50%;" />
	<div id="mp_shipping_method_block">
		{if !empty($mp_shipping_data)}
		{foreach $mp_shipping_data as $shipping_data}
			<div>
				<div class="checkbox">
					<label class="">
						{if isset($mp_product_id)}
							<input style="margin-right: 5px;" type="checkbox"
							value="{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}" name="carriers[]"
							id="carriers_{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}"
							{if isset($mp_shipping_id_map)}
								{if in_array($shipping_data.id_reference, $mp_shipping_id_map)}
									checked="checked"
								{/if}
							{/if}>
						{else}
							<input style="margin-right: 5px;" type="checkbox"
							value="{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}" name="carriers[]"
							id="carriers_{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}"
							{if isset($shipping_data.is_default_shipping) && $shipping_data.is_default_shipping == 1}
							checked="checked"{/if}>
						{/if}
						{$shipping_data.id_carrier|escape:'htmlall':'UTF-8'} - {$shipping_data.name|escape:'htmlall':'UTF-8'}
					</label>
			    </div>
		    </div>
		{/foreach}
		{else}
			<div class="alert alert-warning">
				{l s='There is no active carrier.' mod='marketplace'}
			</div>
		{/if}
	</div>
	{if !empty($mp_shipping_data) && !empty($allCarrierNames)}
	<div class="form-group wk_carrier">
		<div role="alert" class="clearfix alert alert-warning">
			{if !isset($backendController)}
				<i class="material-icons wkmp_icon">info_outline</i>
			{/if}
			<span>
				{l s='If No carrier selected, Admin default carrier will apply on this product.' mod='marketplace'}
			</span>
			<br>
			{if isset($allCarrierNames) && $allCarrierNames}
				<ul style="list-style: disc; {if isset($backendController)}padding-left: 10px;{else}padding-left: 35px;{/if}">
					{foreach $allCarrierNames as $carrierName}
						<li>{$carrierName|escape:'htmlall':'UTF-8'}</li>
					{/foreach}
				</ul>
			{/if}
		</div>
	</div>
	{/if}
</div>
{*This code is working only on Backend*}
{if isset($is_admin_controller) && $is_admin_controller == 1}
<script type="text/javascript">
	var admin_ajax_link = "{$link->getAdminLink('AdminSellerProductDetail')|escape:'htmlall':'UTF-8'}";
	var no_shipping = "{l s='There is no active carrier.' js=1 mod='marketplace'}";
	var is_admin_controller = "{$is_admin_controller|escape:'htmlall':'UTF-8'}";
	$(document).ready(function(){
		if (is_admin_controller == 1) {
			var selected_id_customer = $("[name='shop_customer']").val();
			function getShippingMethodByIdCustomer(selected_id_customer) {
				$('body').css('opacity', '0.5');
				$('#ajax_loader').css('display', 'block');
				$.ajax({
					url: admin_ajax_link,
					type: 'POST',
					data: {
						selected_id_customer: selected_id_customer,
						action: "getShippingMethodByIdCustomer",
						ajax: "1"
					},
					dataType: 'json',
					success: function(data) {
						$('body').css('opacity', '1');
						$('#ajax_loader').css('display', 'none');
						$('#mp_shipping_method_block').empty();
						if (data.status == 1) {
							$.each(data.info, function(index, mp_shipping_data) {
								if (mp_shipping_data.is_default_shipping == 1) {
									$('#mp_shipping_method_block').append("<div style='width:25px;float:left;'><input type='checkbox' name='carriers[]' id='carriers_"+mp_shipping_data.id_carrier+"' value='"+mp_shipping_data.id_carrier+"' checked='checked'></div><div style='float:left;'><label for='carriers_"+mp_shipping_data.id_carrier+"' style='font-weight: normal;'>"+mp_shipping_data.name+"</label></div><div style='clear:both;'></div>");
								} else {
									$('#mp_shipping_method_block').append("<div style='width:25px;float:left;'><input type='checkbox' name='carriers[]' id='carriers_"+mp_shipping_data.id_carrier+"' value='"+mp_shipping_data.id_carrier+"'></div><div style='float:left;'><label for='carriers_"+mp_shipping_data.id_carrier+"' style='font-weight: normal;'>"+mp_shipping_data.name+"</label></div><div style='clear:both;'></div>");
								}
							});
						} else {
							$('#mp_shipping_method_block').append("<div class='alert alert-warning'>"+no_shipping+"</div>");
						}
					}, fail: function(data) {
						$('body').css('opacity', '1');
						$('#ajax_loader').css('display', 'none');
						$('#mp_shipping_method_block').empty();
						$('#mp_shipping_method_block').append("<div class='alert alert-warning'>"+no_shipping+"</div>");
					}, error: function(data) {
						$('body').css('opacity', '1');
						$('#ajax_loader').css('display', 'none');
						$('#mp_shipping_method_block').empty();
						$('#mp_shipping_method_block').append("<div class='alert alert-warning'>"+no_shipping+"</div>");
					}
				});
			}
			getShippingMethodByIdCustomer(selected_id_customer);
			$(document).on('change', "[name='shop_customer']", function(){
				selected_id_customer = $("[name='shop_customer']").val();
				getShippingMethodByIdCustomer(selected_id_customer);
			});
		}
	});
</script>
{/if}