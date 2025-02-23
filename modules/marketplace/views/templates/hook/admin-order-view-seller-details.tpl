{**
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
*}

<div class="panel" style="background-color: #fff;">
	{if $wk_ps_version >= '1.7.7.0'}
		<div class="card-header">
			<h3 class="card-header-title">
				{l s='Seller Product List' mod='marketplace'}
				{if $mp_seller_order_details|is_array}({$mp_seller_order_details|@count|escape:'htmlall':'UTF-8'}){/if}
			</h3>
		</div>
	{else}
		<div class="panel-heading">
			<i class="icon-list"></i>
			{l s='Seller Product List' mod='marketplace'}
			{if $mp_seller_order_details|is_array}
				<span class="badge">{$mp_seller_order_details|@count|escape:'htmlall':'UTF-8'}</span>
			{/if}
		</div>
	{/if}
	<div class="table-responsive">
		<table class="table wk-table">
			<thead>
				<tr>
					<th><span class="title_box">{l s='Unique Shop Name' mod='marketplace'}</span></th>
					<th><span class="title_box">{l s='Current Order Status' mod='marketplace'}</span></th>
					<th><span class="title_box">{l s='Tracking URL' mod='marketplace'}</span></th>
					<th><span class="title_box">{l s='Tracking Number' mod='marketplace'}</span></th>
					<th>{l s='Seller Detail' mod='marketplace'}</th>
					<th>{l s='Product Detail' mod='marketplace'}</th>
					{if Configuration::get('WK_MP_SELLER_SHIPPING')}<th>{l s='Tracking Number' mod='marketplace'}</th>{/if}
					{hook h='displayAdminPsSellerOrderViewHead'}
				</tr>
			</thead>
			<tbody>
				{foreach $mp_seller_order_details as $mp_order_detail}
					<tr>
						<td>{$mp_order_detail.0.seller_shop|escape:'htmlall':'UTF-8'}</td>
						<td>
							<span style="background:{if isset($mp_order_detail.0.ostate_name)}{$mp_order_detail.0.color|escape:'htmlall':'UTF-8'}{else}{$currentState->color|escape:'htmlall':'UTF-8'}{/if};color:white !important; border-radius: 5px; padding: 5px; ">
							{if isset($mp_order_detail.0.ostate_name)}
								{$mp_order_detail.0.ostate_name|escape:'htmlall':'UTF-8'}
							{else}
								{$currentState->name|escape:'htmlall':'UTF-8'}
							{/if}
							</span>
						</td>
						<td>{$mp_order_detail.0.tracking_url|escape:'htmlall':'UTF-8'}</td>
						<td>{$mp_order_detail.0.tracking_number|escape:'htmlall':'UTF-8'}</td>
						<td><a class="btn btn-default" target="_blank" href="{$link->getAdminLink('AdminSellerInfoDetail')|escape:'htmlall':'UTF-8'}&id_seller={$mp_order_detail.0.id_seller|escape:'htmlall':'UTF-8'}&viewwk_mp_seller"><i class="icon-search-plus"></i> {l s='View Seller' mod='marketplace'}</a></td>
						<td>
							<a data-id="{$mp_order_detail.0.id_seller|escape:'htmlall':'UTF-8'}" class="btn btn-default wk-seller-prod" href="javascript:void(0);">
								<i class="icon-search-plus"></i> {l s='View Detail' mod='marketplace'}
							</a>
						</td>
						{if Configuration::get('WK_MP_SELLER_SHIPPING')}<td>{if isset($shipping_number)}{$shipping_number|escape:'htmlall':'UTF-8'}{/if}</td>{/if}
						{hook h='displayAdminPsSellerOrderViewBody' idSellerCustomer=$mp_order_detail.0.seller_customer_id}
					</tr>
					<tr class="wk-product-detail-{$mp_order_detail.0.id_seller|escape:'htmlall':'UTF-8'}" style="display: none;">
						<td colspan="12">
							<div class="panel">
							<table class="table">
								<thead>
									<tr>
										<th>{l s='Product name' mod='marketplace'}</th>
										<th>{l s='Quantity' mod='marketplace'}</th>
										<th>{l s='Price(ti)' mod='marketplace'}</th>
										<th>{l s='Price(te)' mod='marketplace'}</th>
									</tr>
								</thead>
								<tbody>
									<div class="panel-heading">
										{l s='Product details' mod='marketplace'}
									</div>
									{foreach $mp_order_detail as $order_detail}
									<tr>
										<td>
											{if isset($order_detail.id_mp_product)}
												<a href="{$link->getAdminLink('AdminSellerProductDetail')|escape:'htmlall':'UTF-8'}&id_mp_product={$order_detail.id_mp_product|escape:'htmlall':'UTF-8'}&updatewk_mp_seller_product" target="_blank">
													<span class="productName">{$order_detail.product_name|escape:'htmlall':'UTF-8'}</span>
												</a>
											{else}
												<span class="productName">{$order_detail.product_name|escape:'htmlall':'UTF-8'}</span>
											{/if}
										</td>
										<td>
											<span class="productName">{$order_detail.quantity|escape:'htmlall':'UTF-8'}</span>
										</td>
										<td>
											<span class="productName">{displayPrice price=$order_detail.price_ti currency=$order_detail.id_currency}</span>
										</td>
										<td>
											<span class="productName">{displayPrice price=$order_detail.price_te currency=$order_detail.id_currency}</span>
										</td>
									</tr>
									{/foreach}
								</tbody>
							</table>
							</div>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '.wk-seller-prod', function(){
			var idSeller = $(this).attr('data-id');
			$('.wk-product-detail-'+idSeller).toggle();
		});
	});
</script>