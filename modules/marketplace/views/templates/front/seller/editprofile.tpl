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
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/js/tinymce/tinymce_wk_setup.js"></script>
<div class="wk-mp-block" >
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
			<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Edit Profile' mod='marketplace'}</span>
		</div>
		<form action="{$link->getModuleLink('marketplace', 'editprofile')|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" id="wk_mp_seller_form">
			<div class="wk-mp-right-column">
				{block name='wk-form-validation'}
					{include file='module:marketplace/views/templates/front/_partials/validation.tpl'}
				{/block}
				<div class="profile_content">
					<input type="hidden" name="token" id="wk-static-token" value="{$static_token|escape:'htmlall':'UTF-8'}">
					<input type="hidden" name="mp_seller_id" id="mp_seller_id" value="{$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}">
					<input type="hidden" name="current_lang_id" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}" id="current_lang_id">
					<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab|escape:'htmlall':'UTF-8'}{/if}" id="active_tab">
					<div class="form-group row">
						<div class="col-md-7">
							{if $allow_multilang && $total_languages > 1}
								<label class="control-label">{l s='Choose language' mod='marketplace'}</label>
								<div class="row">
									<div class="col-md-7">
										<select class="form-control" name="seller_lang_btn" id="seller_lang_btn">
											{foreach from=$languages item=language}
												<option data-langname="{$language.name|escape:'htmlall':'UTF-8'}" value="{$language.id_lang|escape:'htmlall':'UTF-8'}" {if ($current_lang.id_lang == $language.id_lang)}selected="selected"{/if}>{$language.name|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</select>
									</div>
								</div>
								<p class="wk_formfield_comment">{l s='Change language for updating information in multiple language.' mod='marketplace'}</p>
							{/if}
						</div>
						{if isset($mpSellerShopSettings) && $mpSellerShopSettings}
							<div class="col-md-5 wk_deactivateshop_button">
								<a class="btn btn-default button button-small wk_shop_deactivate" href="{$link->getModuleLink('marketplace','editprofile', ['deactivate' => 1])|escape:'htmlall':'UTF-8'}">
									<button type="button" class="btn btn-primary btn-sm wk_deactivate_shop">
										{l s='Deactivate your shop' mod='marketplace'}
									</button>
								</a>
							</div>
						{/if}
					</div>
					<div class="alert alert-danger wk_display_none" id="wk_mp_form_error"></div>
					<hr>
					<div class="tabs wk-tabs-panel">
						<ul class="nav nav-tabs">
							<li class="nav-item">
								<a class="nav-link active" href="#wk-information" data-toggle="tab">
									<i class="material-icons">&#xE88E;</i>
									{l s='Information' mod='marketplace'}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#wk-contact" data-toggle="tab">
									<i class="material-icons">&#xE0BA;</i>
									{l s='Address' mod='marketplace'}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#wk-images" data-toggle="tab">
									<i class="material-icons">&#xE410;</i>
									{l s='Images' mod='marketplace'}
								</a>
							</li>
							{if Configuration::get('WK_MP_SOCIAL_TABS')}
							<li class="nav-item">
								<a class="nav-link" href="#wk-social" data-toggle="tab">
									<i class="material-icons">&#xE853;</i>
									{l s='Social' mod='marketplace'}
								</a>
							</li>
							{/if}
							{if Configuration::get('WK_MP_SELLER_DETAILS_PERMISSION')}
							<li class="nav-item">
								<a class="nav-link" href="#wk-permission" data-toggle="tab">
									<i class="material-icons">&#xE862;</i>
									{l s='Permission' mod='marketplace'}
								</a>
							</li>
							{/if}
							<li class="nav-item">
								<a class="nav-link" href="#wk-seller-payment-details" data-toggle="tab">
									<i class="material-icons">&#xE8A1;</i>
									{l s='Payment Detail' mod='marketplace'}
								</a>
							</li>
							{hook h='displayMpEditProfileTab'}
						</ul>
						<div class="tab-content" id="tab-content">
							<div class="tab-pane fade in active show" id="wk-information">
								{block name='editprofile-information'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-information.tpl'}
								{/block}
							</div>
							<div class="tab-pane fade in" id="wk-contact">
								{block name='editprofile-contact'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-contact.tpl'}
								{/block}
							</div>
							<div class="tab-pane fade in" id="wk-images">
								{block name='editprofile-images'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-images.tpl'}
								{/block}
							</div>
							{if Configuration::get('WK_MP_SOCIAL_TABS')}
							<div class="tab-pane fade in" id="wk-social">
								{block name='editprofile-social'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-social.tpl'}
								{/block}
							</div>
							{/if}
							{if Configuration::get('WK_MP_SELLER_DETAILS_PERMISSION')}
							<div class="tab-pane fade in" id="wk-permission">
								{if isset($selectedDetailsByAdmin) && $selectedDetailsByAdmin && Configuration::get('WK_MP_SHOW_SELLER_DETAILS')}
									<div class="alert alert-info">
										{l s='Select which details to be displayed for customers on Seller’s Profile page and Shop page (subject to access provided by admin)' mod='marketplace'}
									</div>
									<div class="wk_select_all">
										<input type="checkbox" id="wk_select_all_checkbox">
										<label class="pull-left"><b>{l s='Select all' mod='marketplace'}</b></label>
										<div class="clearfix"></div>
									</div>
									{foreach $selectedDetailsByAdmin as $detailsVal}
										<div>
											<input type="checkbox" name="seller_details_access[]" value="{$detailsVal.id_group|escape:'htmlall':'UTF-8'}" class="pull-left" {if isset($selectedDetailsBySeller) && in_array($detailsVal.id_group, $selectedDetailsBySeller)} checked {/if}>
											<label class="pull-left" style="font-weight:400 !important;">{$detailsVal.name|escape:'htmlall':'UTF-8'}</label>
											<div class="clearfix"></div>
										</div>
									{/foreach}
								{else}
									<div class="alert alert-danger">
										{l s='Admin does not allow display of seller details.' mod='marketplace'}
									</div>
								{/if}
							</div>
							{/if}
							<div class="tab-pane fade in" id="wk-seller-payment-details">
								{block name='editprofile-payment-detail'}
									{include file='module:marketplace/views/templates/front/seller/_partials/editprofile-payment-detail.tpl'}
								{/block}
							</div>
							{hook h="displayMpEditProfileTabContent"}
						</div>
					</div>
					{hook h="displayMpEditProfileFooter"}
				</div>
				{block name='mp-form-fields-notification'}
					{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
				{/block}
			</div>
			<div class="wk-mp-right-column wk_border_top_none">
				<div class="form-group row">
					<div class="col-md-12 wk_text_right" id="wk-seller-submit" data-action="{l s='Save' mod='marketplace'}">
						<img class="wk_product_loader" src="{$module_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loader.gif" width="25" />
						<button type="submit" id="updateProfile" name="updateProfile" class="btn btn-success wk_btn_extra form-control-submit">
							<span>{l s='Save' mod='marketplace'}</span>
						</button>
					</div>
				</div>
				{if isset($adminContactEmail)}
					<hr>
					<div class="wk-admin-contact">
						{l s='For any query you can contact admin' mod='marketplace'}
						<a href="mailto:{$adminContactEmail|escape:'htmlall':'UTF-8'}">{$adminContactEmail|escape:'htmlall':'UTF-8'}</a>
					</div>
				{/if}
			</div>
		</form>
	</div>
</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}