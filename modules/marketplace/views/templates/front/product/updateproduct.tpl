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
{*tiny MCE added here because included in setMedia will not work with performance config CCC use js cache*}
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/js/tinymce/tinymce_wk_setup.js"></script>

{hook h='displayMpUpdateProductHeader'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
			<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Update Product' mod='marketplace'}</span>
		</div>
		<form action="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $id])|escape:'htmlall':'UTF-8'}" method="post" id="wk_mp_seller_product_form" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" >
			<div class="wk-mp-right-column">
				{block name='wk-form-validation'}
					{include file='module:marketplace/views/templates/front/_partials/validation.tpl'}
				{/block}
				{hook h='displayMpUpdateProductFormHeader'}
				{if $add_permission}
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a title="{l s='Add product' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'addproduct')|escape:'htmlall':'UTF-8'}" {if $allow_multilang && $total_languages > 1}class="pull-right"{/if}>
							<button class="btn btn-primary btn-sm" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add Product' mod='marketplace'}
							</button>
						</a>
					</p>
				</div>
				{/if}
				<input type="hidden" name="token" id="wk-static-token" value="{$static_token|escape:'htmlall':'UTF-8'}">
				<input type="hidden" name="default_lang" value="{$default_lang|escape:'htmlall':'UTF-8'}" id="default_lang">
				<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}" id="current_lang_id">
				<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab|escape:'htmlall':'UTF-8'}{/if}" id="active_tab">
				<input type="hidden" name="mp_product_id" value="{$id|escape:'htmlall':'UTF-8'}" id="mp_product_id">
				{block name='change-product-language'}
					{include file='module:marketplace/views/templates/front/product/_partials/change-product-language.tpl'}
				{/block}
				<div class="alert alert-danger wk_display_none" id="wk_mp_form_error"></div>
				<hr>
				<div class="tabs wk-tabs-panel">
					{block name='product-nav-tabs'}
						{include file='module:marketplace/views/templates/front/product/_partials/product-nav-tabs.tpl'}
					{/block}
					<div class="tab-content" id="tab-content">
						<div class="tab-pane fade in active show" id="wk-information">
							{hook h='displayMpUpdateProductContentTop'}

							{if (Configuration::get('WK_MP_PACK_PRODUCTS') || Configuration::get('WK_MP_VIRTUAL_PRODUCT'))}
								<div class="form-group">
									<div class="row">
										<div class="col-sm-3">
											<label class="control-label required {if isset($isAdmin)}pull-right{/if}">
												{l s='Product Type' mod='marketplace'}
											</label>
										</div>
										<div class="col-sm-9">
											<div class="row">
												<div class="col-sm-12">
													<label class="control-label">
														<input type="radio" name="product_type" class="product_type" value="1" {if isset($product_info.product_type) && $product_info.product_type == 'standard'}checked{else}checked{/if}>
														{l s='Standard product' mod='marketplace'}
													</label>
												</div>
											</div>
											{if Configuration::get('WK_MP_PACK_PRODUCTS')}
												<div class="row">
													<div class="col-sm-12">
														<label class="control-label">
															<input type="radio" name="product_type" class="product_type" value="2" {if (isset($product_info.is_pack_product) && ($product_info.is_pack_product == '1'))}checked {else if isset($smarty.post.product_type) && ($smarty.post.product_type == 2)}checked{/if} {if (isset($is_pack_item) && ($is_pack_item==1)) || (isset($combi_exist) && $combi_exist == 1)}disabled="disabled"{/if}>
															{l s='Pack of existing products' mod='marketplace'}
														</label>
													</div>
												</div>
											{/if}

											{if Configuration::get('WK_MP_VIRTUAL_PRODUCT')}
												<div class="row">
													<div class="col-sm-12">
														<label class="control-label">
															<input type="radio" name="product_type" class="product_type" value="3" {if isset($product_info.is_virtual) && $product_info.is_virtual == '1'}checked{else if isset($smarty.post.product_type) && ($smarty.post.product_type == 3)}checked{/if}
															{if isset($combi_exist) && $combi_exist == 1}disabled="disabled"{/if}>
															{l s='Virtual product (services, booking, downloadable products, etc.)' mod='marketplace'}
														</label>
													</div>
												</div>
											{/if}
										</div>
									</div>
								</div>
							{/if}
							{hook h='displayMpProductType'}
							<div class="form-group">
								<label for="product_name" class="control-label required">
									{l s='Product name' mod='marketplace'}
									{block name='mp-form-fields-flag'}
										{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
									{/block}
								</label>
								{foreach from=$languages item=language}
									{assign var="product_name" value="product_name_`$language.id_lang`"}
									<input type="text"
									id="product_name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
									name="product_name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
									value="{if isset($smarty.post.$product_name)}{$smarty.post.$product_name|escape:'htmlall':'UTF-8'}{else}{$product_info.name[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
									class="form-control product_name_all wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'} {if $default_lang == $language.id_lang}seller_default_lang_class{/if}
									{if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}"
									data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
									maxlength="128" />
								{/foreach}
								<span class="wk-msg-productname"></span>
							</div>
							{hook h='displayMpUpdateProductNameBottom'}

							{block name='pack-product'}
								{if Configuration::get('WK_MP_PACK_PRODUCTS')}
									{include file='module:marketplace/views/templates/front/product/_partials/pack-product.tpl'}
								{/if}
							{/block}

							<div class="form-group">
								<label for="prod_short_desc" class="control-label">
									{l s='Short description' mod='marketplace'}
									{block name='mp-form-fields-flag'}
										{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
									{/block}
								</label>
								{foreach from=$languages item=language}
									{assign var="short_description" value="short_description_`$language.id_lang`"}
									<div id="short_desc_div_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'} {if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}">
										<textarea
										name="short_description_{$language.id_lang|escape:'htmlall':'UTF-8'}"
										id="short_description_{$language.id_lang|escape:'htmlall':'UTF-8'}" cols="2" rows="3"
										class="wk_tinymce form-control">{if isset($smarty.post.$short_description)}{$smarty.post.$short_description|escape:'htmlall':'UTF-8'}{else}{$product_info.description_short[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}</textarea>
									</div>
								{/foreach}
							</div>
							<div class="form-group">
								<label for="prod_desc" class="control-label">
									{l s='Description' mod='marketplace'}
									{block name='mp-form-fields-flag'}
										{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-flag.tpl'}
									{/block}
								</label>
								{foreach from=$languages item=language}
									{assign var="description" value="description_`$language.id_lang`"}
									<div id="product_desc_div_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'} {if $current_lang.id_lang != $language.id_lang}wk_display_none{/if}">
										<textarea
										name="description_{$language.id_lang|escape:'htmlall':'UTF-8'}"
										id="description_{$language.id_lang|escape:'htmlall':'UTF-8'}" cols="2" rows="3"
										class="wk_tinymce form-control">{if isset($smarty.post.$description)}{$smarty.post.$description|escape:'htmlall':'UTF-8'}{else}{$product_info.description[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}</textarea>
									</div>
								{/foreach}
							</div>
							<div class="form-group row">
								{if Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')}
									<div class="col-md-6">
										<label for="reference" class="control-label">
											{l s='Reference' mod='marketplace'}
											<div class="wk_tooltip">
												<span class="wk_tooltiptext">{l s='Your internal reference code for this product. Allowed max 32 character. Allowed special characters' mod='marketplace'}:.-_#.</span>
											</div>
										</label>
										<input type="text"
										class="form-control"
										name="reference"
										id="reference"
										value="{if isset($smarty.post.reference)}{$smarty.post.reference|escape:'htmlall':'UTF-8'}{else}{$product_info.reference|escape:'htmlall':'UTF-8'}{/if}"
										maxlength="32" />
							  		</div>
								{/if}
								{if Configuration::get('WK_MP_PRODUCT_CONDITION')}
									<div class="col-md-6">
										<div class="form-group">
											<label for="condition" class="control-label">
												{l s='Condition' mod='marketplace'}
												<div class="wk_tooltip">
													<span class="wk_tooltiptext">{l s='This option enables you to indicate the condition of the product.' mod='marketplace'}</span>
												</div>
											</label>
											<select class="form-control form-control-select" name="condition" id="condition">
												<option value="new" {if $product_info.condition == 'new'}selected{/if}>{l s='New' mod='marketplace'}</option>
												<option value="used" {if $product_info.condition == 'used'}selected{/if}>{l s='Used' mod='marketplace'}</option>
												<option value="refurbished" {if $product_info.condition == 'refurbished'}selected{/if}>{l s='Refurbished' mod='marketplace'}</option>
											</select>
										</div>
										<div class="checkbox">
											<label for="show_condition">
												<input type="checkbox" name="show_condition" id="show_condition" value="1" {if $product_info.show_condition == '1'}checked="checked"{/if} />
												<span>{l s='Display condition on product page' mod='marketplace'}</span>
											</label>
										</div>
									</div>
								{/if}
							</div>
							{* Product quantity section *}
							{block name='product-quantity'}
								{include file='module:marketplace/views/templates/front/product/_partials/product-quantity.tpl'}
							{/block}
							<div class="form-group row">
								<div class="col-md-6">
									<label for="prod_category" class="control-label required">
										{l s='Category' mod='marketplace'}
										<div class="wk_tooltip">
											<span class="wk_tooltiptext">{l s='Where should the product be available on your site? The main category is where the product appears by default: this is the category which is seen in the product page\'s URL.' mod='marketplace'}</span>
										</div>
									</label>
									<div id="categorycontainer"></div>
									<input type="hidden" name="product_category" id="product_category" value="{if isset($catIdsJoin)}{$catIdsJoin|escape:'htmlall':'UTF-8'}{/if}" />
								</div>
								<div class="col-md-6" id="default_category_div">
									<label for="default_category" class="control-label required">
										{l s='Main category' mod='marketplace'}
									</label>
								  	<select class="form-control form-control-select" name="default_category" id="default_category">
								  		{if isset($defaultCategory)}
											{foreach $defaultCategory as $defaultCategoryVal}
												<option id="default_cat{$defaultCategoryVal.id_category|escape:'htmlall':'UTF-8'}" value="{$defaultCategoryVal.id_category|escape:'htmlall':'UTF-8'}" name="{$defaultCategoryVal.name|escape:'htmlall':'UTF-8'}" {if isset($defaultIdCategory)}{if $defaultIdCategory == $defaultCategoryVal.id_category} selected {/if}{/if}>{$defaultCategoryVal.name|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										{/if}
								  	</select>
							  	</div>
							</div>

							{block name='product-pricing'}
								{include file='module:marketplace/views/templates/front/product/_partials/product-pricing.tpl'}
							{/block}
							{block name='virtual-product'}
								{if Configuration::get('WK_MP_VIRTUAL_PRODUCT')}
									{include file='module:marketplace/views/templates/front/product/_partials/virtual-product.tpl'}
								{/if}
							{/block}
							{block name='product-specific-rule'}
								{include file='module:marketplace/views/templates/front/product/_partials/product-specific-rule.tpl'}
							{/block}
							{block name='product-manufacturers-list'}
								{include file='module:marketplace/views/templates/front/product/manufacturers/product_manufacturers_list.tpl'}
							{/block}
							{block name='related-product'}
								{include file='module:marketplace/views/templates/front/product/_partials/related_product.tpl'}
							{/block}
							{hook h="displayMpUpdateProductFooter"}
						</div>
						<div class="tab-pane fade in" id="wk-images">
							{block name='updateproduct_images'}
								{include file='module:marketplace/views/templates/front/product/_partials/updateproduct-images.tpl'}
							{/block}
						</div>
						{if Configuration::get('WK_MP_SELLER_PRODUCT_COMBINATION') && $permissionData.combinationPermission}
							<div class="tab-pane fade in" id="wk-combination">
								{block name='product-combination'}
									{include file='module:marketplace/views/templates/front/product/_partials/product-combination.tpl'}
								{/block}
							</div>
						{/if}
						{if Configuration::get('WK_MP_PRODUCT_FEATURE') && $permissionData.featuresPermission}
							<div class="tab-pane fade in" id="wk-feature">
								{if (isset($permissionData.featuresPermission.edit) && $permissionData.featuresPermission.edit == '0') && empty($editPermissionNotAllow)}
									{* Message for Mp seller staff module *}
									<div class="alert alert-danger">
										{l s='You do not have permission to edit this.' mod='marketplace'}
									</div>
								{/if}
								{block name='mp-product-feature'}
									{include file='module:marketplace/views/templates/front/product/_partials/product-feature.tpl'}
								{/block}
							</div>
						{/if}
						{if (Configuration::get('WK_MP_SELLER_ADMIN_SHIPPING') || Configuration::get('WK_MP_SELLER_SHIPPING')) && $permissionData.shippingPermission}
							<div class="tab-pane fade in" id="wk-product-shipping">
								{if (isset($permissionData.shippingPermission.edit) && $permissionData.shippingPermission.edit == '0') && empty($editPermissionNotAllow)}
									{* Message for Mp seller staff module *}
									<div class="alert alert-danger">
										{l s='You do not have permission to edit this.' mod='marketplace'}
									</div>
								{/if}
								{block name='mp-product-shipping'}
									{include file='module:marketplace/views/templates/front/product/_partials/product-shipping.tpl'}
								{/block}
							</div>
						{/if}
						{if Configuration::get('WK_MP_SELLER_PRODUCT_SEO') || Configuration::get('WK_MP_PRODUCT_PAGE_REDIRECTION') && $permissionData.seoPermission}
							<div class="tab-pane fade in" id="wk-seo">
								{if (isset($permissionData.seoPermission.edit) && $permissionData.seoPermission.edit == '0') && empty($editPermissionNotAllow)}
									{* Message for Mp seller staff module *}
									<div class="alert alert-danger">
										{l s='You do not have permission to edit this.' mod='marketplace'}
									</div>
								{/if}
								{block name='mp-product-seo'}
									{include file='module:marketplace/views/templates/front/product/_partials/product-seo.tpl'}
								{/block}
							</div>
						{/if}
						{if (Configuration::get('WK_MP_SELLER_PRODUCT_EAN') || Configuration::get('WK_MP_SELLER_PRODUCT_UPC') || Configuration::get('WK_MP_SELLER_PRODUCT_ISBN') || Configuration::get('WK_MP_SELLER_PRODUCT_VISIBILITY') || Configuration::get('WK_MP_SELLER_PRODUCT_AVAILABILITY') || Configuration::get('WK_MP_PRODUCT_TAGS') || Configuration::get('WK_MP_PRODUCT_SUPPLIER') || Configuration::get('WK_MP_PRODUCT_CUSTOMIZATION') || Configuration::get('WK_MP_PRODUCT_ATTACHMENT') || (Configuration::get('WK_MP_PRODUCT_MPN') && !(_PS_VERSION_ < '1.7.7.0'))) && $permissionData.optionsPermission}
							<div class="tab-pane fade in" id="wk-options">
								{if (isset($permissionData.optionsPermission) && $permissionData.optionsPermission.edit == '0') && empty($editPermissionNotAllow)}
									{* Message for Mp seller staff module *}
									<div class="alert alert-danger">
										{l s='You do not have permission to edit this.' mod='marketplace'}
									</div>
								{/if}
								{block name='mp-product-references'}
									{include file='module:marketplace/views/templates/front/product/_partials/product-references.tpl'}
								{/block}
								{if Configuration::get('WK_MP_SELLER_PRODUCT_VISIBILITY')}
									{block name='mp-product-visibility'}
										{include file='module:marketplace/views/templates/front/product/_partials/product-visibility.tpl'}
									{/block}
								{/if}
								{if Configuration::get('WK_MP_SELLER_PRODUCT_AVAILABILITY')}
									{block name='mp-product-availability-preferences'}
										{include file='module:marketplace/views/templates/front/product/_partials/product-availability-preferences.tpl'}
									{/block}
								{/if}
							</div>
						{/if}
						{hook h='displayMpProductTabContent'}
					</div>
				</div>
				{block name='mp-form-fields-notification'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
				{/block}
			</div>
			<div class="wk-mp-right-column wk_border_top_none">
				<div class="form-group row">
					<div class="col-xs-4 col-sm-4 col-md-6">
						<a href="{url entity='module' name='marketplace' controller='productlist'}" class="btn wk_btn_cancel wk_btn_extra mb-1">
							{l s='CANCEL' mod='marketplace'}
						</a>
					</div>
					<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" id="wk-product-submit" data-action="{l s='Save' mod='marketplace'}">
						<img class="wk_product_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loader.gif" width="25" />
						<button type="submit" id="StayProduct" name="StayProduct" class="btn btn-success wk_btn_extra form-control-submit mb-1">
							<span>{l s='SAVE & STAY' mod='marketplace'}</span>
						</button>
						<button type="submit" id="SubmitProduct" name="SubmitProduct" class="btn btn-success wk_btn_extra form-control-submit mb-1">
							<span>{l s='SAVE' mod='marketplace'}</span>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	{block name='mp_image_preview'}
		{include file='module:marketplace/views/templates/front/product/_partials/mp-image-preview.tpl'}
	{/block}
</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}
