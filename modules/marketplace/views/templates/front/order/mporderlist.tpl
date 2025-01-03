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

<div class="box-content">
	<div class="wk_order_table">
		<table class="table table-hover" id="my-orders-table">
			<thead>
				<tr>
					<th>{l s='ID' mod='marketplace'}</th>
					<th>{l s='Reference' mod='marketplace'}</th>
					<th>{l s='Customer' mod='marketplace'}</th>
					<th>{l s='Total' mod='marketplace'}</th>
					<th>{l s='Status' mod='marketplace'}</th>
					<th>{l s='Payment' mod='marketplace'}</th>
					<th>{l s='Date' mod='marketplace'}</th>
				</tr>
			</thead>
			<tbody>
				{if isset($mporders)}
					{foreach $mporders as $order}
						<tr class="mp_order_row" is_id_order="{$order.id_order|escape:'htmlall':'UTF-8'}" is_id_order_detail="{$order.id_order_detail|escape:'htmlall':'UTF-8'}">
							<td>{$order.id_order|escape:'htmlall':'UTF-8'}</td>
							{*<td class="wk_cust">
								<div class="wk_cust_left">
									<input value="" type="checkbox" name="wkmp_order_status">
								</div>
								<div class="wk_cust_right">
									<span>{$order.id_order}</span>
								</div>
							</td>*}
							<td>{$order.reference|escape:'htmlall':'UTF-8'}</td>
							<td>{$order.buyer_info->firstname|escape:'htmlall':'UTF-8'} {$order.buyer_info->lastname|escape:'htmlall':'UTF-8'}</td>
							<td data-order="{$order.total_paid_without_sign|escape:'htmlall':'UTF-8'}">
								{$order.total_paid|escape:'htmlall':'UTF-8'}{*TODO:should not be currency convertable*}
							</td>
							<td>{$order.order_status|escape:'htmlall':'UTF-8'}</td>
							<td>{$order.payment_mode|escape:'htmlall':'UTF-8'}</td>
							<td data-order="{$order.date_add|escape:'htmlall':'UTF-8'}">{dateFormat date=$order.date_add full=1}</td>
						</tr>
					{/foreach}
				{/if}
			</tbody>
		</table>
	</div>
</div>