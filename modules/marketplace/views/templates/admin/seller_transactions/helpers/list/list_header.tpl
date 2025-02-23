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

{extends file="helpers/list/list_header.tpl"}
{block name=leadin}

{if !isset($shippingDetail)}
	<div class="clearfix"></div>
	<div class="panel">
		{if isset($seller)}
			<div class="panel-heading">{l s='%s --- Earning' sprintf=[$seller['shop_name_unique']] mod='marketplace'}</div>
		{else}
			<div class="panel-heading">{l s='Total earning' mod='marketplace'}</div>
		{/if}
		{hook h="displayMpTransactionTopContent"}
		<div class="table-responsive clearfix">
			<table class="table wk_mp_seller_order">
				<thead>
					<tr class="nodrag nodrop">
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Total earning of admin & seller' mod='marketplace'}">
									{l s='Total earning' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span>
									{l s='Admin commission' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Total tax earned by admin' mod='marketplace'}">
									{l s='Admin tax' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Total shipping amount earned by admin' mod='marketplace'}">
									{l s='Admin shipping' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Sum of seller amount, seller tax and seller shipping amount' mod='marketplace'}">
									{l s='Seller earnings' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span>
									{l s='Seller tax' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span>
									{l s='Seller shipping' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span>
									{l s='Seller received' mod='marketplace'}
								</span>
							</span>
						</th>
						<th class="center">
							<span class="title_box">
								<span>
									{l s='Seller due' mod='marketplace'}
								</span>
							</span>
						</th>
						{if isset($settlement)}
						<th class="center">
							<span class="title_box">
								<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Settle seller payment' mod='marketplace'}">
									{l s='Action' mod='marketplace'}
								</span>
							</span>
						</th>
						{hook h=displayMpSellerTransactionTableColumnHead}
						{else if !isset($noshipping) && !isset($allTransaction)}
						<th class="center">
							<span class="title_box">
								<span>
									{l s='Action' mod='marketplace'}
								</span>
							</span>
						</th>
						{/if}
					</tr>
				</thead>
				<tbody>
				{if isset($sellerOrderTotal) && $sellerOrderTotal}
					{foreach $sellerOrderTotal as $orderTotal}
					<tr class="wk_mp_order">
						<td class="center">
							<input
								type="hidden"
								name="wk_earn_ti"
								id="wk_earn_ti_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_total_earning|escape:'htmlall':'UTF-8'}">
								{$orderTotal.total_earning|escape:'htmlall':'UTF-8'}
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_admin_commission"
								id="wk_admin_commission_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_admin_commission|escape:'htmlall':'UTF-8'}">
								{$orderTotal.admin_commission|escape:'htmlall':'UTF-8'}
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_admin_tax"
								id="wk_admin_tax_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_admin_tax|escape:'htmlall':'UTF-8'}">
								{$orderTotal.admin_tax|escape:'htmlall':'UTF-8'}
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_admin_shipping"
								id="wk_admin_shipping_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_admin_shipping|escape:'htmlall':'UTF-8'}">
								{$orderTotal.admin_shipping|escape:'htmlall':'UTF-8'}
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_seller_amount"
								id="wk_seller_amount_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_seller_total|escape:'htmlall':'UTF-8'}">
								<span class="badge badge-success">{$orderTotal.seller_total|escape:'htmlall':'UTF-8'}</span>
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_seller_amount"
								id="wk_seller_amount_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_seller_tax|escape:'htmlall':'UTF-8'}">
								<span class="badge badge-success">{$orderTotal.seller_tax|escape:'htmlall':'UTF-8'}</span>
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_seller_amount"
								id="wk_seller_amount_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_seller_shipping|escape:'htmlall':'UTF-8'}">
								<span class="badge badge-success">
									{$orderTotal.seller_shipping|escape:'htmlall':'UTF-8'}
								</span>
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_seller_paid"
								id="wk_seller_paid_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_seller_recieve|escape:'htmlall':'UTF-8'}">
								<span class="badge badge-paid" style="background-color: #9e5ba1;">
									{$orderTotal.seller_recieve|escape:'htmlall':'UTF-8'}
								</span>
						</td>
						<td class="center">
							<input
								type="hidden"
								name="wk_seller_due"
								data-value="{$orderTotal.seller_due_full|escape:'htmlall':'UTF-8'}"
								id="wk_seller_due_{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}"
								value="{$orderTotal.no_prefix_seller_due|escape:'htmlall':'UTF-8'}">
								<span class="badge badge-pending">{$orderTotal.seller_due|escape:'htmlall':'UTF-8'}</span>
						</td>
						{if isset($settlement)}
						<th class="center">
							<a id="wk_seller_settlement" data-id-currency="{$orderTotal.id_currency|escape:'htmlall':'UTF-8'}" data-mfp-src="#test-popup"
								class="btn btn-default open-popup-link"
								data-toggle="modal" data-target="#basicModal"
								{if Tools::ps_round($orderTotal.no_prefix_seller_due, 2) <= 0}disabled="disabled"{/if}>
								{l s='Settle' mod='marketplace'}
							</a>
						</th>
						{hook h=displayMpSellerTransactionTableColumnBody seller_payment_data=$orderTotal id_seller_customer=$seller.seller_customer_id}
						{else if !isset($noshipping) && !isset($allTransaction)}
						<th class="center">
							<a style="text-transform: none;" href="{$link->getAdminLink('AdminSellerTransactions')|escape:'htmlall':'UTF-8'}&view{$table|escape:'htmlall':'UTF-8'}&mp_transaction_detail=1&id_currency={$orderTotal.id_currency|escape:'htmlall':'UTF-8'}&all=1" id="wk_seller_settlement" class="btn btn-default"><i class="icon-search-plus"></i>
								{l s='View transaction' mod='marketplace'}
							</a>
						</th>
						{/if}
					</tr>
				{/foreach}
				{else}
				<tr>
					<td colspan="8" class="list-empty">
						<div class="list-empty-msg">
							<i class="icon-warning-sign list-empty-icon"></i>
							{l s='No records found' mod='marketplace'}
						</div>
					</td>
				</tr>
				{/if}
				</tbody>
			</table>
		</div>
	</div>

	{if isset($settlement)}
		<!--- Settlement PopUp Box -->
		<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">{l s='Seller settlement' mod='marketplace'}</h4>
				      </div>
					<form method="post" action="" id="payment_transaction" class="form-horizontal">
						<div class="modal-body">
							<input type="hidden" id="id_seller" name="id_customer_seller" value="{$seller.seller_customer_id|intval}" />
							<input type="hidden" id="transaction_id" name="transaction_id" value="" />
							<input type="hidden" id="total_due" name="total_due" value="" />
							<input type="hidden" id="id_currency" name="id_currency" value="" />

							<div style="display: none;" class="alert alert-danger" id="wk_seller_error"></div>
							<div class="form-group" style="margin-bottom:0px;">
								<label class="col-lg-4 control-label">{l s='Payment method:' mod='marketplace'}</label>
								<div class="col-lg-5">
									<p class="form-control-static">
										{if isset($payment_mode)}{$payment_mode|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='marketplace'}{/if}
									</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label"></label>
								<div class="col-lg-5">
									<strong>{l s='OR' mod='marketplace'}</strong>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">
									{l s='Other mode:' mod='marketplace'}
								</label>
								<div class="col-lg-5">
									<input type="text" name="wk_mp_payment_method" id="wk_mp_payment_method" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">{l s='Payment details:' mod='marketplace'}</label>
								<div class="col-lg-5">
									<p class="form-control-static">
										{if isset($payment_mode_details)}{$payment_mode_details|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='marketplace'}{/if}
									</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">
									{l s='Transaction ID:' mod='marketplace'}
								</label>
								<div class="col-lg-5">
									<input type="text" name="wk_mp_transaction_id" id="wk_mp_transaction_id" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">
									{l s='Remark:' mod='marketplace'}
								</label>
								<div class="col-lg-5">
									<input type="text" name="wk_mp_remark" id="wk_mp_remark" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label required">{l s='Amount:' mod='marketplace'}</label>
								<div class="col-lg-5">
									<input type="text" name="amount" id="amount" placeholder="{l s='Amount' mod='marketplace'}" class="form-control" />
									<div class="help-block">{l s='Maximum amount can be paid ' mod='marketplace'} <strong><span id="wk_max_amount"></span></strong></div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="wkbtn btn btn-info" name="submit_payment" id="pay_money"><span>{l s='Pay' mod='marketplace'}</span>
							</button>
							<button type="button" class="wkbtn btn btn-info" data-dismiss="modal">
								<span>{l s='Cancel' mod='marketplace'}</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	{/if}

	<!--- Order Detail PopUp Box -->
	<div class="modal fade" id="orderDetail" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" id="wk_seller_product_line"></div>
		</div>
	</div>

	<!--- Settlement Detail PopUp Box -->
	<div class="modal fade" id="settlementDetail" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" id="wk_seller_transaction_line"></div>
		</div>
	</div>
{/if}
<style type="text/css">
	tr.wk_mp_order td {
		padding: 8px 10px !important;
	}
	.wkbtn {
		padding: 6px 30px !important;
	}
</style>

{strip}
	{addJsDefL name=pay_more_error}{l s='You can not pay more than total due amount' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=empty}{l s='Please enter amount to pay' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=invalid_amount}{l s='Please enter valid amount' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=negative_err}{l s='Amount must be greater than zero' js=1 mod='marketplace'}{/addJsDefL}
{/strip}

{/block}
