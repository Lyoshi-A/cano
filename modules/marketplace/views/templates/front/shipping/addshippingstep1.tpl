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

{if isset($smarty.get.delete_success)}
	{if $smarty.get.delete_success == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Logo deleted successfully.' mod='marketplace'}
		</div>
	{/if}
{/if}
{if isset($isAdminAddCarrier) && $isAdminAddCarrier}
	{if !isset($mp_shipping_id)}
		<div class="form-group row">
			<label class="control-label required" style="font-weight: normal;">
				{l s='Choose seller' mod='marketplace'}
			</label>
			<div class="control-label">
				{if isset($customer_info)}
					<select name="seller_customer_id" id="seller_customer_id" class="col-lg-3">
						{foreach $customer_info as $cusinfo}
							<option value="{$cusinfo['id_customer']|escape:'htmlall':'UTF-8'}"
								{if isset($smarty.post.seller_customer_id)}
									{if $smarty.post.seller_customer_id == $cusinfo['id_customer']}Selected="Selected" {/if} {/if}>
									{$cusinfo['business_email']|escape:'htmlall':'UTF-8'}
									{if isset($all_shop) && $all_shop && isset($cusinfo.ps_shop_name)}
										({$cusinfo.ps_shop_name|escape:'htmlall':'UTF-8'})
									{/if}
								</option>
							{/foreach}
						</select>
					{else}
						<p class="text-left">{l s='No seller found.' mod='marketplace'}</p>
					{/if}
				</div>
			</div>
		{else}
			<input type="hidden" value="{$seller_customer_id|escape:'htmlall':'UTF-8'}" name="seller_customer_id" />
		{/if}
		<div class="form-group">
			<label class="control-label">
				{l s='Enable shipping' mod='marketplace'}
			</label>
			<div class="control-label">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" {if isset($mpShippingActive) && $mpShippingActive == 1} checked="checked" {/if}
						value="1" id="mpShippingActive_on" name="mpShippingActive">
					<label for="mpShippingActive_on">{l s='Yes' mod='marketplace'}</label>
					<input type="radio" {if isset($mpShippingActive) && $mpShippingActive == 0} checked="checked" {/if}
						value="0" id="mpShippingActive_off" name="mpShippingActive">
					<label for="mpShippingActive_off">{l s='No' mod='marketplace'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
	{/if}
	{if isset($mp_shipping_id)}
		<input type="hidden" name="mpshipping_id" value="{$mp_shipping_id|escape:'htmlall':'UTF-8'}">
	{/if}
	<div class="form-group">
		<label class="control-label required">{l s='Carrier name' mod='marketplace'}</label>
		<input type="text" name="shipping_name" id="shipping_name" class="form-control"
		value="{if isset($smarty.post.shipping_name)}{$smarty.post.shipping_name|escape:'htmlall':'UTF-8'}{else}{if isset($mp_shipping_name)}{$mp_shipping_name|escape:'htmlall':'UTF-8'}{/if}{/if}" maxlength="64">
		<p class="help-block">
			{l s='Carrier name displayed during checkout.' mod='marketplace'}
		</p>
	</div>
	<div class="form-group">
		<label class="control-label required">{l s='Transit time' mod='marketplace'}</label>
		<div class="row">
			{if $allow_multilang && $total_languages > 1}
				<div class="col-md-10">
				{else}
					<div class="col-md-12">
					{/if}
					{foreach from=$languages item=language}
						{assign var="transit_time" value="transit_time_`$language.id_lang`"}
						<input type="text" id="transit_time_{$language.id_lang|escape:'htmlall':'UTF-8'}"
							name="transit_time_{$language.id_lang|escape:'htmlall':'UTF-8'}"
							value="{if isset($smarty.post.$transit_time)}{$smarty.post.$transit_time|escape:'htmlall':'UTF-8'}{else}{if isset($transit_delay[$language.id_lang])}{$transit_delay[$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}{/if}"
							class="form-control transit_time_all {if $current_lang.id_lang == $language.id_lang}seller_default_lang_class{/if}"
							data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;" {/if} maxlength="128" />
					{/foreach}
				</div>
				{if $allow_multilang && $total_languages > 1}
					<div class="col-md-2">
						<button type="button" id="mpship_lang_btn" class="btn btn-default dropdown-toggle"
							data-toggle="dropdown">
							{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=language}
								<li>
									<a class="lang_presta" href="javascript:void(0)"
										onclick="showShippingLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
								</li>
							{/foreach}
						</ul>
						<input type="hidden" name="multi_lang" id="multi_lang" value="{$multi_lang|escape:'htmlall':'UTF-8'}">
						<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}"
							id="current_lang_id">
					</div>
				{/if}
			</div>
			<p class="help-block">
				{l s='Estimated delivery time will be displayed during checkout.' mod='marketplace'}
			</p>
		</div>
		<div class="form-group">
			<label class="control-label">{l s='Speed grade' mod='marketplace'}</label>
			<input type="text" name="grade" id="grade" class="form-control" value="{if isset($smarty.post.grade)}{$smarty.post.grade|escape:'htmlall':'UTF-8'}{else}{if isset($grade)}{$grade|escape:'htmlall':'UTF-8'}{/if}{/if}">
			<p class="help-block">
				{l s='Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.' mod='marketplace'}
			</p>
		</div>

		<div class="form-group">
			<label class="control-label">{l s='Logo' mod='marketplace'}</label>
			{if isset($imageexist)}
				<div>
					<img class="img-thumbnail" alt="{l s='Carrier Logo' mod='marketplace'}"
						src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/img/mpshipping/{$mp_shipping_id|escape:'htmlall':'UTF-8'}.jpg"
						width="50" style="margin-bottom: 5px;">
					{if isset($wk_delete_logo_path)}
						<a href="javascript:void('')" data-toggle="modal" data-target="#wk_mp_carrier_logo_image_delete"
							title="{l s='Delete' mod='marketplace'}">
							<i class="material-icons" style="vertical-align: top;">&#xE872;</i>
						</a>
						{* so modal popup for confirmation *}
						<div class="modal fade" id="wk_mp_carrier_logo_image_delete" tabindex="-1" role="dialog"
							 aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-body">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										<h4><strong>{l s='Are you sure?' mod='marketplace'}</strong></h4>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary"
											data-dismiss="modal">{l s='Cancel' mod='marketplace'}</button>
										<a class="btn btn-secondary" href="{$wk_delete_logo_path|escape:'htmlall':'UTF-8'}"
											title="{l s='Delete' mod='marketplace'}">
											{l s='Delete' mod='marketplace'}
										</a>
									</div>
								</div>
							</div>
						</div>
						{* pop code end *}

					{/if}
				</div>
			{/if}
			<div class="mp_shipping_upload-btn-wrapper">
				<button class="mp_shipping_btnr" type="button">{l s='Choose file' mod='marketplace'}</button>
				<span class="mp_shp_carrier_name">{l s='No file selected' mod='marketplace'}</span>
				<input type="file" name="shipping_logo" id="mp_shp_carrier" style="display:none" />
			</div>
			{* <input type="file" name="shipping_logo" id="shipping_logo"/> *}
			<p class="help-block">
				{l s='Image size should not exceed 125*125' mod='marketplace'}
			</p>
			<img style="display:none;" id="testImg" src="#" alt="" />
		</div>
		<div class="form-group">
			<label class="control-label">{l s='Tracking URL' mod='marketplace'}</label>
			<input type="text" name="tracking_url" id="tracking_url" class="form-control"
				value="{if isset($smarty.post.tracking_url)}{$smarty.post.tracking_url|escape:'htmlall':'UTF-8'}{else}{if isset($tracking_url)}{$tracking_url|escape:'htmlall':'UTF-8'}{/if}{/if}">
			<p class="help-block">
				{l s='Delivery tracking URL: Type @ where the tracking number should appear. It will then be automatically replaced by the tracking number.' mod='marketplace'}
			</p>
	</div>