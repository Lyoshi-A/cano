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

{extends file=$layout}
{block name='header'}
	{include file='module:marketplace/views/templates/front/_partials/header.tpl'}
{/block}
{block name='content'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Product details' mod='marketplace'}</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($mp_error_message) && $mp_error_message}
					<div class="alert alert-danger">
						{$mp_error_message|escape:'htmlall':'UTF-8'}
					</div>
				{else}
				<div class="wk_head row">
					<div class="col-xs-12 col-sm-6 col-md-6">
						<a href="{$link->getModuleLink('marketplace','productlist')|escape:'htmlall':'UTF-8'}" class="btn btn-link wk_padding_none">
							<i class="material-icons">&#xE5C4;</i>
							<span>{l s='Back to product list' mod='marketplace'}</span>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<a title="{l s='Edit Product' mod='marketplace'}"
							href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $product.id_mp_product])|escape:'htmlall':'UTF-8'}"
							class="wk-edit-profile-link" style="float:right;">
							<button class="btn btn-primary btn-sm wk_edit_profile_btn">
								{l s='Edit Product' mod='marketplace'}
							</button>
						</a>
						{if isset($is_approved)}
							<a title="{l s='View Product' mod='marketplace'}" target="_blank" href="{$product_link|escape:'htmlall':'UTF-8'}"
								class="wk-edit-profile-link" style="float:right;margin-right:10px">
								<button class="btn btn-primary btn-sm wk_edit_profile_btn">
									{l s='View Product' mod='marketplace'}
								</button>
							</a>
						{/if}
					</div>

				</div>
				<div class="wk_product_details row">
					<input type="hidden" name="token" id="wk-static-token" value="{$static_token|escape:'htmlall':'UTF-8'}">
					<div class="wk_details">
						<div class="row">
							<label class="col-md-4">{l s='Product name' mod='marketplace'} - </label>
							<div class="col-md-8">{$product.name|escape:'htmlall':'UTF-8'}</div>
						</div>
						{if $product.description != ''}
							<div class="row">
								<label class="col-md-4">{l s='Description' mod='marketplace'} - </label>
								<div class="col-md-8">{$product.description nofilter}</div>
							</div>
						{/if}
						<div class="row">
							<label class="col-md-4">{l s='Price' mod='marketplace'} -</label>
							<div class="col-md-8">{$product.price|escape:'htmlall':'UTF-8'}</div>
						</div>
						<div class="row">
							<label class="col-md-4">{l s='Quantity' mod='marketplace'} -</label>
							<div class="col-md-8">{$product.quantity|escape:'htmlall':'UTF-8'}</div>
						</div>
						<div class="row">
							<label class="col-md-4">{l s='Status' mod='marketplace'} -</label>
							<div class="col-md-8">
								{if $product.active == 1}
									{l s='Approved' mod='marketplace'}
								{else}
									{l s='Pending' mod='marketplace'}
								{/if}
							</div>
						</div>
						<div class="row">
							<label class="col-md-2"></label>
							<div class="col-md-10">
								{if isset($admin_commission)}
									<div id="wk_display_admin_commission" class="alert alert-info">
										{l s='Admin commission will be %s of your product price.' sprintf=[$admin_commission] mod='marketplace'}
									</div>
								{/if}
							</div>
						</div>
					</div>
					<div class="wk_image">
						{if isset($cover_image)}
							<a class="mp-img-preview"
								href="{$link->getImageLink($product.link_rewrite, $cover_image, 'large_default')|escape:'htmlall':'UTF-8'}">
								<img id="wk-product-detail-cover"
									src="{$link->getImageLink($product.link_rewrite, $cover_image, 'home_default')|escape:'htmlall':'UTF-8'}"
									style="width: 83%;height: auto;" />
							</a>
						{/if}
					</div>
				</div>

				{block name='imageedit'}
					{include file='module:marketplace/views/templates/front/product/imageedit.tpl'}
				{/block}

				<div class="left full">
					{if (Configuration::get('WK_MP_PACK_PRODUCTS'))}
						{include file='module:marketplace/views/templates/front/product/_partials/mpproduct-details.tpl'}
					{/if}
					{hook h="displayMpProductDetailsFooter"}
				</div>
				{/if}
			</div>
		</div>

		{block name='mp_image_preview'}
			{include file='module:marketplace/views/templates/front/product/_partials/mp-image-preview.tpl'}
		{/block}
	</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}