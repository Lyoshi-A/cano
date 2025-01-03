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

<div class="form-group row">
	<div class="wk_package col-md-12">
		{if isset($backendController)}
			<h4>{l s='Package Dimension' mod='marketplace'}</h4>
		{else}
			<h4>{l s='Package Dimension' mod='marketplace'}</h4>
		{/if}
		<p class="subtitle">
			{l s='Adjust your shipping costs by filling in the product dimensions.' mod='marketplace'}
		</p>
	</div>
	<div class="col-md-3">
		<label class="form-control-label">{l s='Width' mod='marketplace'}</label>
		<div class="input-group">
			<input type="text" value="{if isset($smarty.post.width)}{$smarty.post.width|escape:'htmlall':'UTF-8'}{else if isset($product_info.width)}{$product_info.width|escape:'htmlall':'UTF-8'}{else if isset($edit)}{$product.width|escape:'htmlall':'UTF-8'}{/if}" class="form-control" name="width" id="width">
			<span class="input-group-addon">{Configuration::get('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'}</span>
		</div>
	</div>
	<div class="col-md-3">
		<label class="form-control-label">{l s='Height' mod='marketplace'}</label>
		<div class="input-group">
			<input type="text" value="{if isset($smarty.post.height)}{$smarty.post.height|escape:'htmlall':'UTF-8'}{else if isset($product_info.height)}{$product_info.height|escape:'htmlall':'UTF-8'}{else if isset($edit)}{$product.height|escape:'htmlall':'UTF-8'}{/if}" class="form-control" name="height" id="height">
			<span class="input-group-addon">{Configuration::get('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'}</span>
		</div>
	</div>
	<div class="col-md-3">
		<label class="form-control-label">{l s='Depth' mod='marketplace'}</label>
		<div class="input-group">
			<input type="text" value="{if isset($smarty.post.depth)}{$smarty.post.depth|escape:'htmlall':'UTF-8'}{else if isset($product_info.depth)}{$product_info.depth|escape:'htmlall':'UTF-8'}{else if isset($edit)}{$product.depth|escape:'htmlall':'UTF-8'}{/if}" class="form-control" name="depth" id="depth">
			<span class="input-group-addon">{Configuration::get('PS_DIMENSION_UNIT')|escape:'htmlall':'UTF-8'}</span>
		</div>
	</div>
	<div class="col-md-3">
		<label class="form-control-label">{l s='Weight' mod='marketplace'}</label>
		<div class="input-group">
			<input type="text" value="{if isset($smarty.post.weight)}{$smarty.post.weight|escape:'htmlall':'UTF-8'}{else if isset($product_info.weight)}{$product_info.weight|escape:'htmlall':'UTF-8'}{else if isset($edit)}{$product.weight|escape:'htmlall':'UTF-8'}{/if}" class="form-control" name="weight" id="weight">
			<span class="input-group-addon">{Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}</span>
		</div>
	</div>
</div>
<hr>
<div class="clearfix form-group">
	{if isset($backendController)}
		<h4>{l s='Available carriers' mod='marketplace'}</h4>
	{else}
		<h4>{l s='Available carriers' mod='marketplace'}</h4>
	{/if}
	{if Configuration::get('WK_MP_SELLER_SHIPPING')}
		{if isset($displayShipping) && $displayShipping}
			{include file='module:marketplace/views/templates/front/product/_partials/addproduct_shipping.tpl'}
		{/if}
		{hook h="displaySellerShipping"}
	{else}
		{if !empty($carriersChoices)}
			{foreach $carriersChoices as $key => $idReference}
				<div>
					<div class="checkbox">
						<label class="">
							<input style="margin-right: 5px;" type="checkbox" value="{$idReference|escape:'htmlall':'UTF-8'}" name="ps_id_carrier_reference[]"
							{if isset($selectedCarriers) && !empty($selectedCarriers)}
								{if in_array($idReference, $selectedCarriers)}checked{/if}
							{/if}>{$key}
						</label>
				    </div>
			    </div>
		    {/foreach}
		{else}
			<div class="alert alert-warning">
				{l s='No Carrier Available' mod='marketplace'}
			</div>
	    {/if}
	{/if}
</div>
{if !empty($carriersChoices) && !Configuration::get('WK_MP_SELLER_SHIPPING')}
	<div class="form-group wk_carrier">
		<div role="alert" class="clearfix alert alert-warning">
			{if !isset($backendController)}
				<i class="material-icons wkmp_icon">info_outline</i>
			{/if}
			<span>{l s='If no carrier is selected then all the carriers will be available for customers orders.' mod='marketplace'}</span>
		</div>
	</div>
{/if}
{if isset($deliveryTimeAllowed) && (Configuration::get('WK_MP_PRODUCT_DELIVERY_TIME') || isset($backendController))}
<hr>
	<h4>
		{l s='Delivery time' mod='marketplace'}
		<div class="wk_tooltip" style="vertical-align:initial;">
			<span class="wk_tooltiptext">{l s='Display delivery time for a product is advised for merchants selling in Europe to comply with the local laws.' mod='marketplace'}</span>
		</div>
	</h4>
	<div class="form-group {if isset($backendController)}row{/if}">
		<div class="row">
			<div class="{if isset($backendController)}col-lg-4{else}col-md-6{/if}">
				<div class="radio">
					<label>
						<input type="radio" value="0" name="additional_delivery_times" {if isset($product_info.additional_delivery_times) && $product_info.additional_delivery_times == '0'}checked{/if}>
						{l s='None' mod='marketplace'}
					</label>
				</div>
				<div class="radio">
					<label>
						<input type="radio" value="1" name="additional_delivery_times" {if isset($product_info.additional_delivery_times)}{if $product_info.additional_delivery_times == '1'}checked{/if}{else}checked{/if}>
						{l s='Default delivery time' mod='marketplace'}
						<div class="wk_tooltip">
							<span class="wk_tooltiptext">{l s='Default delivery time will be set by Admin.' mod='marketplace'}</span>
						</div>
					</label>
				</div>
				<div class="radio">
					<label>
						<input type="radio" value="2" name="additional_delivery_times" {if isset($product_info.additional_delivery_times) && $product_info.additional_delivery_times == '2'}checked{/if}>
						{l s='Specific delivery time to this product' mod='marketplace'}
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group {if isset($backendController)}row{/if}">
		<label for="delivery_in_stock" class="control-label {if isset($backendController)}col-lg-3{/if}">
			{l s='Delivery time of in-stock products' mod='marketplace'}
			{if $allow_multilang && $total_languages > 1}
				<img class="all_lang_icon" data-lang-id="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}" src="{$ps_img_dir|escape:'htmlall':'UTF-8'}{$current_lang.id_lang|escape:'htmlall':'UTF-8'}.jpg">
			{/if}
		</label>
		<div class="{if isset($backendController)}col-lg-6{/if}">
			{foreach from=$languages item=language}
				{assign var="delivery_in_stock" value="delivery_in_stock_`$language.id_lang`"}
				<input type="text"
				id="delivery_in_stock_{$language.id_lang|escape:'htmlall':'UTF-8'}"
				name="delivery_in_stock_{$language.id_lang|escape:'htmlall':'UTF-8'}"
				value="{if isset($smarty.post.$delivery_in_stock)}{$smarty.post.$delivery_in_stock|escape:'htmlall':'UTF-8'}{else if isset($product_info.delivery_in_stock)}{$product_info.delivery_in_stock[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
				class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
				data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}" maxlength="255"
				{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
			{/foreach}
		</div>
	</div>
	<div class="form-group {if isset($backendController)}row{/if}">
		<label for="delivery_out_stock" class="control-label {if isset($backendController)}col-lg-3{/if}">
			{l s='Delivery time of out-of-stock products with allowed orders' mod='marketplace'}
			{if $allow_multilang && $total_languages > 1}
				<img class="all_lang_icon" data-lang-id="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}" src="{$ps_img_dir|escape:'htmlall':'UTF-8'}{$current_lang.id_lang|escape:'htmlall':'UTF-8'}.jpg">
			{/if}
		</label>
		<div class="{if isset($backendController)}col-lg-6{/if}">
			{foreach from=$languages item=language}
				{assign var="delivery_out_stock" value="delivery_out_stock_`$language.id_lang`"}
				<input type="text"
				id="delivery_out_stock_{$language.id_lang|escape:'htmlall':'UTF-8'}"
				name="delivery_out_stock_{$language.id_lang|escape:'htmlall':'UTF-8'}"
				value="{if isset($smarty.post.$delivery_out_stock)}{$smarty.post.$delivery_out_stock|escape:'htmlall':'UTF-8'}{else if isset($product_info.delivery_out_stock)}{$product_info.delivery_out_stock[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
				class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
				data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}" maxlength="255"
				{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
			{/foreach}
		</div>
	</div>
{/if}
{* Addiontional Shipping Fee for product *}
{if Configuration::get('WK_MP_PRODUCT_ADDITIONAL_FEES') || isset($backendController)}
<hr>
	<h4>
		{l s='Shipping fees' mod='marketplace'}
		<div class="wk_tooltip" style="vertical-align:initial;">
			<span class="wk_tooltiptext">{l s='If a carrier has a tax, it will be added to the shipping fees. Does not apply to free shipping.' mod='marketplace'}</span>
		</div>
	</h4>
	<div class="form-group row">
		<div class="{if isset($backendController)}col-md-4{else}col-md-6{/if}">
			<label for="additional_shipping_cost" class="control-label">
				{l s='Does this product incur additional shipping costs?' mod='marketplace'}
			</label>
			<div class="input-group">
				<input type="text"
		  		id="additional_shipping_cost"
		  		name="additional_shipping_cost"
		  		value="{if isset($smarty.post.additional_shipping_cost)}{$smarty.post.additional_shipping_cost|escape:'htmlall':'UTF-8'}{else if isset($product_info)}{$product_info.additional_shipping_cost|escape:'htmlall':'UTF-8'}{else}0.000000{/if}"
		  		class="form-control"
		  		pattern="\d+(\.\d+)?" />
				<span class="input-group-addon">{$defaultCurrencySign|escape:'htmlall':'UTF-8'}</span>
			</div>
		</div>
	</div>
{/if}