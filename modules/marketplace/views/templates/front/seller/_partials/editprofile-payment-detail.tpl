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

{if isset($mp_payment_option) && $mp_payment_option}
	<div class="alert alert-info">
		{l s='Provide your account details to obtain payment from admin for your orders.' mod='marketplace'}
	</div>
	<div class="form-wrapper">
		<div class="form-group">
			<label for="payment_mode_id" class="control-label">{l s='Payment mode' mod='marketplace'}</label>
			<div class="row">
				<div class="col-md-5">
					<select id="payment_mode_id" name="payment_mode_id" class="form-control form-control-select">
						<option value="">{l s='--- Select payment mode ---' mod='marketplace'}</option>
						{foreach $mp_payment_option as $payment}
							<option id="{$payment.id_mp_payment|escape:'htmlall':'UTF-8'}" value="{$payment.id_mp_payment|escape:'htmlall':'UTF-8'}"
							{if isset($seller_payment_details) && $seller_payment_details.payment_mode_id == $payment.id_mp_payment}Selected="Selected"{/if}>
								{$payment.payment_mode|escape:'htmlall':'UTF-8'}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="payment_detail" class="control-label">{l s='Account details' mod='marketplace'}</label>
			<textarea id="payment_detail" name="payment_detail" class="form-control" rows="4" cols="50">{if isset($seller_payment_details)}{$seller_payment_details.payment_detail|escape:'htmlall':'UTF-8'}{/if}</textarea>
		</div>
	</div>
{else}
	<div class="alert alert-info">
		{l s='Admin has not created any payment method yet' mod='marketplace'}
	</div>
{/if}